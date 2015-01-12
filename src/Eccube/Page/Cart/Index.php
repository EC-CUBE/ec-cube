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

namespace Eccube\Page\Cart;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\CartSession;
use Eccube\Framework\Customer;
use Eccube\Framework\Display;
use Eccube\Framework\FormParam;
use Eccube\Framework\SiteSession;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\PurchaseHelper;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;

/**
 * カート のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractPage
{

    /** 商品規格情報の配列 */
    public $arrData;

    /** 動作モード */
    public $mode;

    /** メッセージ */
    public $tpl_message = '';

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_title = '現在のカゴの中';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrProductType = $masterData->getMasterData('mtb_product_type');
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

        $objFormParam = $this->lfInitParam($_POST);
        $this->mode = $this->getMode();

        // モバイル対応
        if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE) {
            if (isset($_GET['cart_no'])) {
                $objFormParam->setValue('cart_no', $_GET['cart_no']);
            }
            if (isset($_GET['cartKey'])) {
                $objFormParam->setValue('cartKey', $_GET['cartKey']);
            }
        }

        $this->cartKeys = $objCartSess->getKeys();
        foreach ($this->cartKeys as $key) {
            // 商品購入中にカート内容が変更された。
            if ($objCartSess->getCancelPurchase($key)) {
                $this->tpl_message .= "商品購入中にカート内容が変更されましたので、お手数ですが購入手続きをやり直して下さい。\n";
            }
        }

        $cart_no = $objFormParam->getValue('cart_no');
        $cartKey = $objFormParam->getValue('cartKey');

        // エラーチェック
        $arrError = $objFormParam->checkError();
        if (isset($arrError) && !empty($arrError)) {
            Utils::sfDispSiteError(CART_NOT_FOUND);
            Application::alias('eccube.response')->actionExit();
        }

        $objFormParam4OpenCategoryTree =
            $this->lfInitParam4OpenCategoryTree($_REQUEST);
        if ($objFormParam4OpenCategoryTree->getValue('product_id')) {
            $arrQueryString = array(
                'product_id' => $objFormParam4OpenCategoryTree->getValue(
                    'product_id'),
            );
        } else {
            $arrQueryString = array(
                'category_id' => $objFormParam4OpenCategoryTree->getValue(
                    'category_id'),
            );
        }

        switch ($this->mode) {
            case 'confirm':
                // カート内情報の取得
                $cartList = $objCartSess->getCartList($cartKey);
                // カート商品が1件以上存在する場合
                if (count($cartList) > 0) {
                    // カートを購入モードに設定
                    $this->lfSetCurrentCart($objSiteSess, $objCartSess, $cartKey);

                    // 購入ページへ
                    Application::alias('eccube.response')->sendRedirect(SHOPPING_URL);
                    Application::alias('eccube.response')->actionExit();
                }
                break;
            case 'up'://1個追加
                $objCartSess->upQuantity($cart_no, $cartKey);

                Application::alias('eccube.response')->reload($arrQueryString, true);
                Application::alias('eccube.response')->actionExit();
                break;
            case 'down'://1個減らす
                $objCartSess->downQuantity($cart_no, $cartKey);

                Application::alias('eccube.response')->reload($arrQueryString, true);
                Application::alias('eccube.response')->actionExit();
                break;
            case 'setQuantity'://数量変更
                $objCartSess->setQuantity($objFormParam->getValue('quantity'), $cart_no, $cartKey);

                Application::alias('eccube.response')->reload($arrQueryString, true);
                Application::alias('eccube.response')->actionExit();
                break;
            case 'delete'://カートから削除
                $objCartSess->delProduct($cart_no, $cartKey);

                Application::alias('eccube.response')->reload($arrQueryString, true);
                Application::alias('eccube.response')->actionExit();
                break;
            default:
                break;
        }
        $this->arrInfo = Application::alias('eccube.helper.db')->getBasisData();
        $totalIncTax = 0;
        foreach ($this->cartKeys as $key) {
            // カート集計処理
            $this->tpl_message .= $objCartSess->checkProducts($key);
            $this->tpl_total_inctax[$key] = $objCartSess->getAllProductsTotal($key);
            $totalIncTax += $this->tpl_total_inctax[$key];
            $this->tpl_total_tax[$key] = $objCartSess->getAllProductsTax($key);
            // ポイント合計
            $this->tpl_total_point[$key] = $objCartSess->getAllProductsPoint($key);

            $this->arrData[$key] = $objCartSess->calculate($key, $objCustomer);

            // 送料無料チェック
            $this->arrData[$key]['is_deliv_free'] = $objCartSess->isDelivFree($key);

            // 送料無料までの金額を計算
            $this->tpl_deliv_free[$key] = $this->arrInfo['free_rule'] - $this->tpl_total_inctax[$key];
        }

        //商品の合計金額をセット
        $this->tpl_all_total_inctax = $totalIncTax;

        $this->tpl_category_id =
            $objFormParam4OpenCategoryTree->getValue('category_id');
        $this->tpl_product_id =
            $objFormParam4OpenCategoryTree->getValue('product_id');

        // ログイン判定
        if ($objCustomer->isLoginSuccess(true)) {
            $this->tpl_login = true;
            $this->tpl_user_point = $objCustomer->getValue('point');
            $this->tpl_name = $objCustomer->getValue('name01');
        }

        // 前頁のURLを取得
        // TODO: CartSession::setPrevURL()利用不可。
        $this->lfGetCartPrevUrl($_SESSION, $_SERVER['HTTP_REFERER']);
        $this->tpl_prev_url = (isset($_SESSION['cart_prev_url'])) ? $_SESSION['cart_prev_url'] : '';

        // 全てのカートの内容を取得する
        $this->cartItems = $objCartSess->getAllCartList();
    }

    /**
     * ユーザ入力値の処理
     *
     * @return FormParam
     */
    public function lfInitParam($arrRequest)
    {
        $objFormParam = Application::alias('eccube.form_param');
        $objFormParam->addParam('カートキー', 'cartKey', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('カートナンバー', 'cart_no', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        // スマートフォン版での数量変更用
        $objFormParam->addParam('数量', 'quantity', INT_LEN, 'n', array('ZERO_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        // 値の取得
        $objFormParam->setParam($arrRequest);
        // 入力値の変換
        $objFormParam->convParam();

        return $objFormParam;
    }

    /**
     * PC版での開いているカテゴリーツリーの維持用の入力値
     *
     * @return FormParam
     */
    public function lfInitParam4OpenCategoryTree($arrRequest)
    {
        $objFormParam = Application::alias('eccube.form_param');

        $objFormParam->addParam('カテゴリID', 'category_id', INT_LEN, 'n',
            array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n',
            array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

        // 値の取得
        $objFormParam->setParam($arrRequest);
        // 入力値の変換
        $objFormParam->convParam();

        return $objFormParam;
    }

    /**
     * order_temp_id の更新
     *
     * @return
     */
    public function lfUpdateOrderTempid($pre_uniqid, $uniqid)
    {
        $sqlval['order_temp_id'] = $uniqid;
        $where = 'order_temp_id = ?';
        $objQuery = Application::alias('eccube.query');
        $res = $objQuery->update('dtb_order_temp', $sqlval, $where, array($pre_uniqid));
        if ($res != 1) {
            return false;
        }

        return true;
    }

    /**
     * 前頁のURLを取得
     *
     * @return void
     */
    public function lfGetCartPrevUrl(&$session, $referer)
    {
        if (!preg_match('/cart/', $referer)) {
            if (!empty($session['cart_referer_url'])) {
                $session['cart_prev_url'] = $session['cart_referer_url'];
                unset($session['cart_referer_url']);
            } else {
                if (preg_match('/entry/', $referer)) {
                    $session['cart_prev_url'] = HTTPS_URL . 'entry/kiyaku.php';
                } else {
                    $session['cart_prev_url'] = $referer;
                }
            }
        }
        // 妥当性チェック
        if (!Utils::sfIsInternalDomain($session['cart_prev_url'])) {
            $session['cart_prev_url'] = '';
        }
    }

    /**
     * カートを購入モードに設定
     *
     * @param SiteSession $objSiteSess
     * @param CartSession $objCartSess
     * @return void
     */
    public function lfSetCurrentCart(&$objSiteSess, &$objCartSess, $cartKey)
    {
        // 正常に登録されたことを記録しておく
        $objSiteSess->setRegistFlag();
        $pre_uniqid = $objSiteSess->getUniqId();
        // 注文一時IDの発行
        $objSiteSess->setUniqId();
        $uniqid = $objSiteSess->getUniqId();
        // エラーリトライなどで既にuniqidが存在する場合は、設定を引き継ぐ
        if ($pre_uniqid != '') {
            $this->lfUpdateOrderTempid($pre_uniqid, $uniqid);
        }
        // カートを購入モードに設定
        $objCartSess->registerKey($cartKey);
        $objCartSess->saveCurrentCart($uniqid, $cartKey);
    }
}
