<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");
if (file_exists(MODULE_PATH . "mdl_gmopg/inc/function.php")) {
    require_once(MODULE_PATH . "mdl_gmopg/inc/function.php");
}

/**
 * カート のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Cart.php 15532 2007-08-31 14:39:46Z nanasess $
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
        $this->tpl_mainpage = 'cart/index.tpl';
        $this->tpl_column_num = 1;
        $this->tpl_title = "現在のカゴの中";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        global $objCampaignSess;

        $objView = new SC_SiteView(false);
        $objCartSess = new SC_CartSession();
        $objSiteSess = new SC_SiteSession();
        $objCampaignSess = new SC_CampaignSession();
        $objSiteInfo = $objView->objSiteInfo;
        $objCustomer = new SC_Customer();
        $objDb = new SC_Helper_DB_Ex();
        $objProduct = new SC_Product();
        // 商品購入中にカート内容が変更された。
        if($objCartSess->getCancelPurchase()) {
            $this->tpl_message = "商品購入中にカート内容が変更されましたので、お手数ですが購入手続きをやり直して下さい。";
        }

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'up':
            $objCartSess->upQuantity($_POST['cart_no']);
            $this->reload(); // PRG pattern
            break;
        case 'down':
            $objCartSess->downQuantity($_POST['cart_no']);
            $this->reload(); // PRG pattern
            break;
        case 'delete':
            $objCartSess->delProduct($_POST['cart_no']);
            $this->reload(); // PRG pattern
            break;
        case 'confirm':
            // カート内情報の取得
            $cartKey = $_POST['cartKey']; // TODO
            $arrRet = $objCartSess->getCartList($cartKey);
            $max = count($arrRet);
            $cnt = 0;
            for ($i = 0; $i < $max; $i++) {
                // 商品規格情報の取得
                $this->arrData = $objProduct->getProductsClass($arrRet[$i]['id']);
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
                $objCartSess->saveCurrentCart($uniqid, $cartKey);
                // 購入ページへ
                $this->sendRedirect(URL_SHOP_TOP);
                exit;
            }
            break;
        default:
            break;
        }

        // 基本情報の取得
        $this->arrInfo = $objSiteInfo->data;

        $this->cartKeys = $objCartSess->getKeys();
        foreach ($this->cartKeys as $key) {
            // カート集計処理
            $objDb->sfTotalCart($this, $objCartSess, $key);
            $this->arrData = $objDb->sfTotalConfirm($this->arrData, $this, $objCartSess, null, $objCustomer, $key);
            // 送料無料までの金額を計算
            $this->tpl_deliv_free[$key] = $this->arrInfo['free_rule'] - $this->tpl_total_pretax[$key];


        }

        // ログイン判定
        if($objCustomer->isLoginSuccess()) {
            $this->tpl_login = true;
            $this->tpl_user_point = $objCustomer->getValue('point');
            $this->tpl_name = $objCustomer->getValue('name01');
        }


        // 前頁のURLを取得
        $this->tpl_prev_url = $objCartSess->getPrevURL();

        $objView->assignobj($this);
        // フレームを選択(キャンペーンページから遷移なら変更)
        $objCampaignSess->pageView($objView);
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->init();
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {

        // 買い物を続ける場合
        if ($_REQUEST['mode'] == 'continue') {
            $this->sendRedirect($this->getLocation(MOBILE_URL_SITE_TOP), true);
            exit;
        }

        $objView = new SC_MobileView(false);
        $objCartSess = new SC_CartSession();
        $objSiteSess = new SC_SiteSession();
        $objSiteInfo = $objView->objSiteInfo;
        $objCustomer = new SC_Customer();
        $objDb = new SC_Helper_DB_Ex();

        // 商品購入中にカート内容が変更された。
        if($objCartSess->getCancelPurchase()) {
            $this->tpl_message = "商品購入中にｶｰﾄ内容が変更されましたので､お手数ですが購入手続きをやり直して下さい｡";
        }

        switch($_POST['mode']) {
        case 'confirm':
            // カート内情報の取得
            $arrRet = $objCartSess->getCartList();
            $max = count($arrRet);
            $cnt = 0;
            for ($i = 0; $i < $max; $i++) {
                // 商品規格情報の取得
                $arrData = $objDb->sfGetProductsClass($arrRet[$i]['id']);
                // DBに存在する商品
                if($arrData != "") {
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
                $this->sendRedirect(MOBILE_URL_SHOP_TOP, true);
                exit;
            }
            break;
        default:
            break;
        }

        if (!isset($_GET['mode'])) $_GET['mode'] = "";

        /*
         * FIXME sendRedirect() を使った方が良いが無限ループしてしまう...
         */
        switch($_GET['mode']) {
        case 'up':
            $objCartSess->upQuantity($_GET['cart_no']);
            SC_Utils_Ex::sfReload(session_name() . "=" . session_id());
            break;
        case 'down':
            $objCartSess->downQuantity($_GET['cart_no']);
            SC_Utils_Ex::sfReload(session_name() . "=" . session_id());
            break;
        case 'delete':
            $objCartSess->delProduct($_GET['cart_no']);
            SC_Utils_Ex::sfReload(session_name() . "=" . session_id());
            break;
        }

        // カート集計処理
        if (empty($arrData)) {
            $arrData = array();
        }
        $objDb->sfTotalCart($this, $objCartSess);
        $this->arrData = $objDb->sfTotalConfirm($arrData, $this, $objCartSess, null, $objCustomer);

        // 基本情報の取得
        $this->arrInfo = $objSiteInfo->data;

        // ログイン判定
        if($objCustomer->isLoginSuccess(true)) {
            $this->tpl_login = true;
            $this->tpl_user_point = $objCustomer->getValue('point');
            $this->tpl_name = $objCustomer->getValue('name01');
        }

        // 送料無料までの金額を計算
        $tpl_deliv_free = $this->arrInfo['free_rule'] - $this->tpl_total_pretax;
        $this->tpl_deliv_free = $tpl_deliv_free;

        // 前頁のURLを取得
        $this->tpl_prev_url = $objCartSess->getPrevURL();

        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
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
