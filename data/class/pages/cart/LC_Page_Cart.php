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
require_once(CLASS_EX_REALDIR . "page_extends/LC_Page_Ex.php");

/**
 * カート のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Cart extends LC_Page_Ex {

    // {{{ properties

    /** 商品規格情報の配列 */
    var $arrData;

    /** 動作モード */
    var $mode;


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
        $objCartSess = new SC_CartSession_Ex();
        $objSiteSess = new SC_SiteSession_Ex();
        $objCustomer = new SC_Customer();

        $objFormParam = $this->lfInitParam($_REQUEST);
        $this->mode = $this->getMode();

        $this->cartKeys = $objCartSess->getKeys();
        foreach ($this->cartKeys as $key) {
            // 商品購入中にカート内容が変更された。
            if($objCartSess->getCancelPurchase($key)) {
                $this->tpl_message = "商品購入中にカート内容が変更されましたので、お手数ですが購入手続きをやり直して下さい。";
            }
        }
        $this->cartItems =& $objCartSess->getAllCartList();

        $cart_no = $objFormParam->getValue('cart_no');
        $cartKey = $objFormParam->getValue('cartKey');

        switch($this->mode) {
        case 'confirm':
            // カート内情報の取得
            $cartList = $objCartSess->getCartList($cartKey);
            // カート商品が1件以上存在する場合
            if(count($cartList) > 0) {
                // カートを購入モードに設定
                $this->lfSetCurrentCart($objSiteSess,$objCartSess);
                // 購入ページへ
                SC_Response_Ex::sendRedirect(SHOPPING_URL);
                exit;
            }
            break;
        case 'up'://1個追加
            $objCartSess->upQuantity($cart_no, $cartKey);
            SC_Response_Ex::reload(array(), true);
            exit;
            break;
        case 'down'://1個減らす
            $objCartSess->downQuantity($cart_no, $cartKey);
            SC_Response_Ex::reload(array(), true);
            exit;
            break;
        case 'delete'://カートから削除
            $objCartSess->delProduct($cart_no, $cartKey);
            SC_Response_Ex::reload(array(), true);
            exit;
            break;
        default:
            break;
        }
        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
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
        // TODO: SC_CartSession::setPrevURL()利用不可。
        $this->lfGetCartPrevUrl($_SESSION,$_SERVER['HTTP_REFERER']);

        $this->tpl_prev_url = (isset($_SESSION['cart_prev_url'])) ? $_SESSION['cart_prev_url'] : '';
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * ユーザ入力値の処理
     *
     * @return object
     */
    function lfInitParam($arrRequest) {
        $objFormParam = new SC_FormParam();
        $objFormParam->addParam("カートキー", "cartKey", INT_LEN, "n", array('NUM_CHECK',"MAX_LENGTH_CHECK"));
        $objFormParam->addParam("カートナンバー", "cart_no", INT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
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
    function lfUpdateOrderTempid($pre_uniqid,$uniqid){
        $sqlval['order_temp_id'] = $uniqid;
        $where = "order_temp_id = ?";
        $objQuery =& SC_Query::getSingletonInstance();
        $res = $objQuery->update("dtb_order_temp", $sqlval, $where, array($pre_uniqid));
        if($res != 1){
            return false;
        }
        return true;
    }

    /**
     * 前頁のURLを取得
     *
     * @return void
     */
    function lfGetCartPrevUrl(&$session,$referer){
        if (!preg_match("/cart/", $referer)) {
            if (!empty($session['cart_referer_url'])) {
                $session['cart_prev_url'] = $session['cart_referer_url'];
                unset($session['cart_referer_url']);
            } else {
                if (preg_match("/entry/", $referer)) {
                    $session['cart_prev_url'] = HTTPS_URL . 'entry/kiyaku.php';
                } else {
                    $session['cart_prev_url'] = $referer;
                }
            }
        }
        // 妥当性チェック
        if (!SC_Utils_Ex::sfIsInternalDomain($session['cart_prev_url'])) {
            $session['cart_prev_url'] = '';
        }
    }

    /**
     * カートを購入モードに設定
     *
     * @return void
     */
    function lfSetCurrentCart(&$objSiteSess,&$objCartSess){
        // 正常に登録されたことを記録しておく
        $objSiteSess->setRegistFlag();
        $pre_uniqid = $objSiteSess->getUniqId();
        // 注文一時IDの発行
        $objSiteSess->setUniqId();
        $uniqid = $objSiteSess->getUniqId();
        // エラーリトライなどで既にuniqidが存在する場合は、設定を引き継ぐ
        if($pre_uniqid != "") {
            $this->lfUpdateOrderTempid($pre_uniqid,$uniqid);
        }
        // カートを購入モードに設定
        $objCartSess->saveCurrentCart($uniqid, $cartKey);
    }
}
?>
