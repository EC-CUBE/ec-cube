<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Shopping;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Customer;
use Eccube\Framework\CartSession;
use Eccube\Framework\SiteSession;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\DeliveryHelper;
use Eccube\Framework\Helper\PaymentHelper;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;

/**
 * 入力内容確認のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Confirm extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_title = '入力内容のご確認';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrCountry = $masterData->getMasterData('mtb_country');
        $this->arrSex = $masterData->getMasterData('mtb_sex');
        $this->arrJob = $masterData->getMasterData('mtb_job');
        $this->arrMAILMAGATYPE = $masterData->getMasterData('mtb_mail_magazine_type');
        $this->arrReminder = $masterData->getMasterData('mtb_reminder');
        $this->arrDeliv = DeliveryHelper::getIDValueList('service_name');
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        //決済処理中ステータスのロールバック
        $objPurchase = new PurchaseHelper();
        $objPurchase->cancelPendingOrder(PENDING_ORDER_CANCEL_FLAG);

        $objCartSess = new CartSession();
        $objSiteSess = new SiteSession();
        $objCustomer = new Customer();

        $this->is_multiple = $objPurchase->isMultiple();

        // 前のページで正しく登録手続きが行われた記録があるか判定
        if (!$objSiteSess->isPrePage()) {
            // エラー時は、正当なページ遷移とは認めない
            $objSiteSess->setNowPage('');

            Utils::sfDispSiteError(PAGE_ERROR, $objSiteSess);
        }

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $this->tpl_uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        $this->cartKey = $objCartSess->getKey();

        // カート内商品のチェック
        $this->tpl_message = $objCartSess->checkProducts($this->cartKey);
        if (!Utils::isBlank($this->tpl_message)) {
            Response::sendRedirect(CART_URL);
            Response::actionExit();
        }

        // カートの商品を取得
        $this->arrShipping = $objPurchase->getShippingTemp($this->is_multiple);
        $this->arrCartItems = $objCartSess->getCartList($this->cartKey);
        // 合計金額
        $this->tpl_total_inctax[$this->cartKey] = $objCartSess->getAllProductsTotal($this->cartKey);
        // 税額
        $this->tpl_total_tax[$this->cartKey] = $objCartSess->getAllProductsTax($this->cartKey);
        // ポイント合計
        $this->tpl_total_point[$this->cartKey] = $objCartSess->getAllProductsPoint($this->cartKey);

        // 一時受注テーブルの読込
        $arrOrderTemp = $objPurchase->getOrderTemp($this->tpl_uniqid);
        // カート集計を元に最終計算
        $arrCalcResults = $objCartSess->calculate($this->cartKey, $objCustomer,
                                                  $arrOrderTemp['use_point'],
                                                  $objPurchase->getShippingPref($this->is_multiple),
                                                  $arrOrderTemp['charge'],
                                                  $arrOrderTemp['discount'],
                                                  $arrOrderTemp['deliv_id'],
                                                  $arrOrderTemp['order_pref'], // 税金計算の為に追加　注文者基準
                                                  $arrOrderTemp['order_country_id'] // 税金計算の為に追加　注文者基準
                                                  );

        $this->arrForm = array_merge($arrOrderTemp, $arrCalcResults);

        // 会員ログインチェック
        if ($objCustomer->isLoginSuccess(true)) {
            $this->tpl_login = '1';
            $this->tpl_user_point = $objCustomer->getValue('point');
        }

        // 決済モジュールを使用するかどうか
        $this->use_module = PaymentHelper::useModule($this->arrForm['payment_id']);

        switch ($this->getMode()) {
            // 前のページに戻る
            case 'return':
                // 正常な推移であることを記録しておく
                $objSiteSess->setRegistFlag();

                Response::sendRedirect(SHOPPING_PAYMENT_URLPATH);
                Response::actionExit();
                break;
            case 'confirm':
                /*
                 * 決済モジュールで必要なため, 受注番号を取得
                 */
                $this->arrForm['order_id'] = $objPurchase->getNextOrderID();
                $_SESSION['order_id'] = $this->arrForm['order_id'];

                // 集計結果を受注一時テーブルに反映
                $objPurchase->saveOrderTemp($this->tpl_uniqid, $this->arrForm,
                                            $objCustomer);

                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();

                // 決済モジュールを使用する場合
                if ($this->use_module) {
                    $objPurchase->completeOrder(ORDER_PENDING);

                    Response::sendRedirect(SHOPPING_MODULE_URLPATH);
                // 購入完了ページ
                } else {
                    $objPurchase->completeOrder(ORDER_NEW);
                    PurchaseHelper::sendOrderMail($this->arrForm['order_id'], $this);

                    Response::sendRedirect(SHOPPING_COMPLETE_URLPATH);
                }
                Response::actionExit();
                break;
            default:
                break;
        }

    }
}
