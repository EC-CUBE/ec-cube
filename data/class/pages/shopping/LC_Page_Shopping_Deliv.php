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

/**
 * お届け先の指定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Shopping_Deliv extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $masterData = new SC_DB_MasterData();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->tpl_title = "お届け先の指定";
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function action() {
        $objSiteSess = new SC_SiteSession();
        $objCartSess = new SC_CartSession();
        $objCustomer = new SC_Customer();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objQuery = SC_Query::getSingletonInstance();;
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);
        $this->tpl_uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        $this->cartKey = $objCartSess->getKey();

        // ログインチェック
        if(!$objCustomer->isLoginSuccess(true)) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        if($this->cartKey == PRODUCT_TYPE_DOWNLOAD){
            // 会員情報の住所を受注一時テーブルに書き込む
            $objPurchase->copyFromCustomer($sqlval, $objCustomer, 'shipping');
            $objPurchase->saveShippingTemp($sqlval);
            $objPurchase->saveOrderTemp($this->tpl_uniqid, $sqlval, $objCustomer);
            // 正常に登録されたことを記録しておく
            $objSiteSess->setRegistFlag();
            // ダウンロード商品有りの場合は、支払方法画面に転送
            SC_Response_Ex::sendRedirect('payment.php');
            exit;
        }

        switch($this->getMode()) {
        // 削除
        case 'delete':
            if (SC_Utils_Ex::sfIsInt($_POST['other_deliv_id'])) {
                $where = "other_deliv_id = ?";
                $arrRet = $objQuery->delete("dtb_other_deliv", $where, array($_POST['other_deliv_id']));
            }
            break;
        // 会員登録住所に送る
        case 'customer_addr':
            $sqlval = array();
            $arrDeliv = $objPurchase->getDeliv($this->cartKey);
            $sqlval['deliv_id'] = $arrDeliv[0]['deliv_id'];
            // 会員登録住所がチェックされている場合
            if ($_POST['deliv_check'] == '-1') {
                // 会員情報の住所を受注一時テーブルに書き込む
                $objPurchase->copyFromCustomer($sqlval, $objCustomer, 'shipping');
                $objPurchase->saveShippingTemp($sqlval);
                $objPurchase->saveOrderTemp($this->tpl_uniqid, $sqlval, $objCustomer);

                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // お支払い方法選択ページへ移動
                SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URLPATH);
                exit;
            // 別のお届け先がチェックされている場合
            } elseif($_POST['deliv_check'] >= 1) {
                if (SC_Utils_Ex::sfIsInt($_POST['deliv_check'])) {
                    $otherDeliv = $objQuery->getRow("*", "dtb_other_deliv","customer_id = ? AND other_deliv_id = ?"
                                                    ,array($objCustomer->getValue('customer_id'), $_POST['deliv_check']));
                    if (SC_Utils_Ex::isBlank($otherDeliv)) {
                        SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                    }

                    $objPurchase->copyFromOrder($sqlval, $otherDeliv, 'shipping', '');;
                    $objPurchase->saveShippingTemp($sqlval);
                    $objPurchase->saveOrderTemp($this->tpl_uniqid, $sqlval, $objCustomer);

                    // 正常に登録されたことを記録しておく
                    $objSiteSess->setRegistFlag();
                    // お支払い方法選択ページへ移動
                    SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URLPATH);
                    exit;
                }
            }else{
                // エラーを返す
                $arrErr['deli'] = '※ お届け先を選択してください。';
            }
            break;
        // 前のページに戻る
        case 'return':
            // 確認ページへ移動
            SC_Response_Ex::sendRedirect(CART_URLPATH);
            exit;
            break;
        // お届け先複数指定
        case 'multiple':
            SC_Response_Ex::sendRedirect('multiple.php');
            exit;
            break;

        default:
            $objPurchase->unsetShippingTemp();

            break;
        }

        // 登録済み住所を取得
        $this->arrAddr = $objCustomer->getCustomerAddress($_SESSION['customer']['customer_id']);
        // 入力値の取得
        if (!isset($arrErr)) $arrErr = array();
        $this->arrErr = $arrErr;
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
