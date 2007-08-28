<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * カート のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Cart extends LC_Page {

    // {{{ properties

    /** セッションの配列 */
    var $arrSession;

    /** カテゴリの配列 */
    var $arrProductsClass;

    /** 商品規格情報の配列 */
    var $arrData;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_css = URL_DIR.'css/layout/cartin/index.css';
        $this->tpl_mainpage = 'cart/index.tpl';
        $this->tpl_title = "カゴの中を見る";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView(false);
        $objCartSess = new SC_CartSession("", false);
        $objSiteSess = new SC_SiteSession();
        $objCampaignSess = new SC_CampaignSession();
        $objSiteInfo = $objView->objSiteInfo;
        $objCustomer = new SC_Customer();
        $db = new SC_Helper_DB_Ex();
        // 基本情報の取得
        $arrInfo = $objSiteInfo->data;

        // 商品購入中にカート内容が変更された。
        if($objCartSess->getCancelPurchase()) {
            $this->tpl_message = "商品購入中にカート内容が変更されましたので、お手数ですが購入手続きをやり直して下さい。";
        }

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'up':
            $objCartSess->upQuantity($_POST['cart_no']);
            SC_Utils_Ex::sfReload();
            break;
        case 'down':
            $objCartSess->downQuantity($_POST['cart_no']);
            SC_Utils_Ex::sfReload();
            break;
        case 'delete':
            $objCartSess->delProduct($_POST['cart_no']);
            SC_Utils_Ex::sfReload();
            break;
        case 'confirm':
            // カート内情報の取得
            $arrRet = $objCartSess->getCartList();
            $max = count($arrRet);
            $cnt = 0;
            for ($i = 0; $i < $max; $i++) {
                // 商品規格情報の取得
                $this->arrData = $db->sfGetProductsClass($arrRet[$i]['id']);
                // DBに存在する商品
                if($this->arrData != "") {
                    $cnt++;
                }
            }
            // カート商品が1件以上存在する場合
            if($cnt > 0) {
                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                $pre_uniqid = $objSiteSess->getUniqId();
                // 注文一時IDの発行
                $objSiteSess->setUniqId();
                $uniqid = $objSiteSess->getUniqId();
                // エラーリトライなどで既にuniqidが存在する場合は、設定を引き継ぐ
                if($pre_uniqid != "") {
                    $sqlval['order_temp_id'] = $uniqid;
                    $where = "order_temp_id = ?";
                    $objQuery = new SC_Query();
                    $objQuery->update("dtb_order_temp", $sqlval, $where, array($pre_uniqid));
                }
                // カートを購入モードに設定
                $objCartSess->saveCurrentCart($uniqid);
                // 購入ページへ
                $this->sendRedirect(URL_SHOP_TOP, array());
            }
            break;
        default:
            break;
        }

        // カート集計処理
        $objPage = $db->sfTotalCart($this, $objCartSess, $arrInfo);
        $this->arrData = SC_Utils_Ex::sfTotalConfirm($this->arrData, $this, $objCartSess, $arrInfo, $objCustomer);

        $this->arrInfo = $arrInfo;

        // ログイン判定
        if($objCustomer->isLoginSuccess()) {
            $this->tpl_login = true;
            $this->tpl_user_point = $objCustomer->getValue('point');
            $this->tpl_name = $objCustomer->getValue('name01');
        }

        // 送料無料までの金額を計算
        $this->tpl_deliv_free = $this->arrInfo['free_rule'] - $this->tpl_total_pretax;

        // 前頁のURLを取得
        $this->tpl_prev_url = $objCartSess->getPrevURL();

        $objView->assignobj($objPage);
        // フレームを選択(キャンペーンページから遷移なら変更)
        $objCampaignSess->pageView($objView);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
