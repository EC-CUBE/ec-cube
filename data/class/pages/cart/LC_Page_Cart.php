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
require_once(CLASS_REALDIR . "pages/LC_Page.php");
if (file_exists(MODULE_REALDIR . "mdl_gmopg/inc/function.php")) {
    require_once(MODULE_REALDIR . "mdl_gmopg/inc/function.php");
}

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
        $this->tpl_title = "現在のカゴの中";
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrProductType = $masterData->getMasterData("mtb_product_type");

    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objView = new SC_SiteView(false);
        $objCartSess = new SC_CartSession();
        $objSiteSess = new SC_SiteSession();
        $objSiteInfo = $objView->objSiteInfo;
        $objCustomer = new SC_Customer();

        $this->cartKeys = $objCartSess->getKeys();
        foreach ($this->cartKeys as $key) {
            // 商品購入中にカート内容が変更された。
            if($objCartSess->getCancelPurchase($key)) {
                $this->tpl_message = "商品購入中にカート内容が変更されましたので、お手数ですが購入手続きをやり直して下さい。";
            }
        }
        $this->cartItems =& $objCartSess->getAllCartList();

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'confirm':
            // カート内情報の取得
            $cartKey = $_POST['cartKey'];
            $cartList = $objCartSess->getCartList($cartKey);
            // カート商品が1件以上存在する場合
            if(count($cartList) > 0) {
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
                SC_Response_Ex::sendRedirect(SHOPPING_URL);
                exit;
            }
            break;
        default:
            break;
        }

        // 商品の個数変更、削除処理
        $changeCartMode = (Net_UserAgent_Mobile::isMobile() === true) ? $_GET['mode'] : $_POST['mode'];
        /*
         * FIXME モバイルの場合 sfReload() ではなく sendRedirect() を使った方が良いが無限ループしてしまう...
         */
        switch($changeCartMode) {
        case 'up':
            if(Net_UserAgent_Mobile::isMobile() === true) {
                $objCartSess->upQuantity($_GET['cart_no'], $_GET['cartKey']);
                SC_Utils_Ex::sfReload(session_name() . "=" . session_id());
            } else {
                $objCartSess->upQuantity($_POST['cart_no'], $_POST['cartKey']);
                $this->objDisplay->reload(); // PRG pattern
            }
            break;
        case 'down':
            if(Net_UserAgent_Mobile::isMobile() === true) {
                $objCartSess->downQuantity($_GET['cart_no'], $_GET['cartKey']);
                SC_Utils_Ex::sfReload(session_name() . "=" . session_id());
            } else {
                $objCartSess->downQuantity($_POST['cart_no'], $_POST['cartKey']);
                $this->objDisplay->reload(); // PRG pattern
            }
            break;
        case 'delete':
            if(Net_UserAgent_Mobile::isMobile() === true) {
                $objCartSess->delProduct($_GET['cart_no'], $_GET['cartKey']);
                SC_Utils_Ex::sfReload(session_name() . "=" . session_id());
            } else {
                $objCartSess->delProduct($_POST['cart_no'], $_POST['cartKey']);
                $this->objDisplay->reload(); // PRG pattern
            }
            break;
        default:
            break;
        }
        
        // 基本情報の取得
        $this->arrInfo = $objSiteInfo->data;
        foreach ($this->cartKeys as $key) {
            // カート集計処理
            $this->tpl_message = $objCartSess->checkProducts($key);
            $this->tpl_total_inctax[$key] = $objCartSess->getAllProductsTotal($key);
            $this->tpl_total_tax[$key] = $objCartSess->getAllProductsTax($key);
            // ポイント合計
            $this->tpl_total_point[$key] = $objCartSess->getAllProductsPoint($key);

            $this->arrData[$key] = $objCartSess->calculate($key, $objCustomer);
            // 送料無料までの金額を計算
            $this->tpl_deliv_free[$key] = $this->arrInfo['free_rule'] - $this->tpl_total_inctax[$key];
        }

        // ログイン判定
        if($objCustomer->isLoginSuccess(true)) {
            $this->tpl_login = true;
            $this->tpl_user_point = $objCustomer->getValue('point');
            $this->tpl_name = $objCustomer->getValue('name01');
        }

        // 前頁のURLを取得
        $this->tpl_prev_url = $objCartSess->getPrevURL();
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
