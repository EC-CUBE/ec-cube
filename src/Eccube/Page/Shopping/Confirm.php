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
use Eccube\Framework\FormParam;
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
        /* @var $objPurchase PurchaseHelper */
        $objPurchase = Application::alias('eccube.helper.purchase');
        $objPurchase->cancelPendingOrder(PENDING_ORDER_CANCEL_FLAG);

        /* @var $objCartSess CartSession */
        $objCartSess = Application::alias('eccube.cart_session');
        /* @var $objSiteSess SiteSession */
        $objSiteSess = Application::alias('eccube.site_session');
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');

        $this->is_multiple = $objPurchase->isMultiple();

        // 前のページで正しく登録手続きが行われた記録があるか判定
        if (!$objSiteSess->isPrePage()) {
            Utils::sfDispSiteError(PAGE_ERROR, $objSiteSess);
        }

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $this->tpl_uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        $this->cartKey = $objCartSess->getKey();

        // カート内商品のチェック
        $this->tpl_message = $objCartSess->checkProducts($this->cartKey);
        if (!Utils::isBlank($this->tpl_message)) {
            Application::alias('eccube.response')->sendRedirect(CART_URL);
            Application::alias('eccube.response')->actionExit();
        }

        $this->is_download = ($this->cartKey == PRODUCT_TYPE_DOWNLOAD);

        // カートの商品を取得
        $this->arrShipping = $objPurchase->getShippingTemp($this->is_multiple);
        $this->arrCartItems = $objCartSess->getCartList($this->cartKey);
        // 合計金額
        $this->tpl_total_inctax = $objCartSess->getAllProductsTotal($this->cartKey);
        // 税額
        $this->tpl_total_tax = $objCartSess->getAllProductsTax($this->cartKey);
        $this->tpl_total_tax = $objCartSess->getAllProductsTax($this->cartKey);

        $objFormParam = new FormParam();
        $this->lfInitParam($objFormParam, $this->arrShipping);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        $objFormParam->trimParam();

        // 一時受注テーブルの読込
        $arrOrderTemp = $objPurchase->getOrderTemp($this->tpl_uniqid);

        // 配送業者を取得
        $objDelivery = new DeliveryHelper();
        $this->arrDeliv = $objDelivery->getList($this->cartKey, false, true);
        $this->tpl_is_single_deliv = $objDelivery->isSingleDeliv($this->cartKey);

        // お届け日一覧の取得
        $this->arrDelivDate = $objPurchase->getDelivDate($objCartSess, $this->cartKey);

        if (Utils::sfIsInt($arrOrderTemp['deliv_id'])) {
           $this->arrPayment = $objPurchase->getSelectablePayment($objCartSess, $arrOrderTemp['deliv_id'], true);
            $this->arrDelivTime = DeliveryHelper::getDelivTime($arrOrderTemp['deliv_id']);
        }

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

        foreach ($objFormParam->getHashArray() as $key => $param) {
            if (!Utils::isBlank($param) && Utils::isBlank($this->arrForm[$key])) {
                $this->arrForm[$key] = $param;
            }
        }

        // 会員ログインチェック
        if ($objCustomer->isLoginSuccess(true)) {
            $this->tpl_login = '1';
            $this->tpl_user_point = $objCustomer->getValue('point');
        }

        // 決済モジュールを使用するかどうか
        $this->use_module = Application::alias('eccube.helper.payment')->useModule($this->arrForm['payment_id']);

        switch ($this->getMode()) {
            case 'select_deliv':
                $sqlval = $objFormParam->getHashArray();
                if (Utils::isBlank($sqlval['use_point'])) {
                    $sqlval['use_point'] = '0';
                }
                $deliv_id = $objFormParam->getValue('deliv_id');
                $arrPayment = $objPurchase->getSelectablePayment($objCartSess, $deliv_id);
                $sqlval['payment_id'] = $arrPayment[0]['payment_id'];
                $sqlval['payment_method'] = $arrPayment[0]['payment_method'];
                $objPurchase->saveOrderTemp($this->tpl_uniqid, $sqlval);
                Response::reload();
                break;
            // 前のページに戻る
            case 'return':
                // 正常な推移であることを記録しておく
                $objSiteSess->setRegistFlag();

                Application::alias('eccube.response')->sendRedirect(SHOPPING_PAYMENT_URLPATH);
                Application::alias('eccube.response')->actionExit();
                break;
            case 'confirm':
                $this->saveShippings($objFormParam, $this->arrDelivTime);
                $deliv_id = $objFormParam->getValue('deliv_id');
                $arrPayment = $objPurchase->getSelectablePayment($objCartSess, $deliv_id);
                $this->lfRegistPayment($this->tpl_uniqid, $objFormParam->getHashArray(), $objPurchase, $arrPayment);
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

                    Application::alias('eccube.response')->sendRedirect(SHOPPING_MODULE_URLPATH);
                // 購入完了ページ
                } else {
                    $objPurchase->completeOrder(ORDER_NEW);
                    PurchaseHelper::sendOrderMail($this->arrForm['order_id'], $this);

                    Application::alias('eccube.response')->sendRedirect(SHOPPING_COMPLETE_URLPATH);
                }
                Application::alias('eccube.response')->actionExit();
                break;
            default:
                break;
        }

    }



    /**
     * パラメーター情報の初期化を行う.
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param  array        $arrShipping  配送先情報の配列
     * @return void
     */
    public function lfInitParam(&$objFormParam, &$arrShipping)
    {
        $objFormParam->addParam('配送業者', 'deliv_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('ポイント', 'use_point', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK', 'ZERO_START'));
        $objFormParam->addParam('その他お問い合わせ', 'message', LTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ポイントを使用する', 'point_check', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '2');

        $objFormParam->addParam('お支払い方法', 'payment_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        foreach ($arrShipping as $val) {
            $objFormParam->addParam('お届け時間', 'deliv_time_id' . $val['shipping_id'], INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $objFormParam->addParam('お届け日', 'deliv_date' . $val['shipping_id'], STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        }

        $objFormParam->setParam($arrShipping);
        $objFormParam->convParam();
    }

    /**
     * 配送情報を保存する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param array        $arrDelivTime 配送時間の配列
     */
    public function saveShippings(&$objFormParam, $arrDelivTime)
    {
        // ダウンロード商品の場合は配送先が存在しない
        if ($this->is_download) return;

        $deliv_id = $objFormParam->getValue('deliv_id');
        /* TODO
         * SC_Purchase::getShippingTemp() で取得して,
         * リファレンスで代入すると, セッションに添字を追加できない？
         */
        foreach (array_keys($_SESSION['shipping']) as $key) {
            $shipping_id = $_SESSION['shipping'][$key]['shipping_id'];
            $time_id = $objFormParam->getValue('deliv_time_id' . $shipping_id);
            $_SESSION['shipping'][$key]['deliv_id'] = $deliv_id;
            $_SESSION['shipping'][$key]['time_id'] = $time_id;
            $_SESSION['shipping'][$key]['shipping_time'] = $arrDelivTime[$time_id];
            $_SESSION['shipping'][$key]['shipping_date'] = $objFormParam->getValue("deliv_date{$shipping_id}");
        }
    }

    /**
     * 受注一時テーブルへ登録を行う.
     *
     * @param  integer            $uniqid      受注一時テーブルのユニークID
     * @param  array              $arrForm     フォームの入力値
     * @param  SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param  array              $arrPayment  お支払い方法の配列
     * @return void
     */
    public function lfRegistPayment($uniqid, $arrForm, &$objPurchase, $arrPayment)
    {
        $arrForm['order_temp_id'] = $uniqid;
        $arrForm['update_date'] = 'CURRENT_TIMESTAMP';

        if ($arrForm['point_check'] != '1') {
            $arrForm['use_point'] = 0;
        }

        foreach ($arrPayment as $payment) {
            if ($arrForm['payment_id'] == $payment['payment_id']) {
                $arrForm['charge'] = $payment['charge'];
                $arrForm['payment_method'] = $payment['payment_method'];
                break;
            }
        }
        $this->arrForm = array_merge($this->arrForm, $arrForm);
    }
}
