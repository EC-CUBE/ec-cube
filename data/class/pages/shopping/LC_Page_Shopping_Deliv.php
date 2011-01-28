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

    // {{{ properties

    /** フォームパラメータの配列 */
    var $objFormParam;

    /** ログインフォームパラメータ配列 */
    var $objLoginFormParam;

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
        // クッキー管理クラス
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);
        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        $this->objLoginFormParam = new SC_FormParam();	// ログインフォーム用
        $this->lfInitLoginFormParam();
        //パスワード・Eメールにある空白をトリム
        $this->lfConvertEmail($_POST["login_email"]);
        $this->lfConvertLoginPass($_POST["login_pass"]);
        $this->objLoginFormParam->setParam($_POST);		// POST値の取得

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($uniqid, $objCartSess);

        $this->tpl_uniqid = $uniqid;

        $this->cartKey = $objCartSess->getKey();

        // ログインチェック
        if($this->getMode() != 'login' && !$objCustomer->isLoginSuccess(true)) {
            // 不正アクセスとみなす
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        switch($this->getMode()) {
        case 'login':
            $this->objLoginFormParam->toLower('login_email');
            $this->arrErr = $this->objLoginFormParam->checkError();
            $arrForm =  $this->objLoginFormParam->getHashArray();
            // クッキー保存判定
            if($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
                $objCookie->setCookie('login_email', $_POST['login_email']);
            } else {
                $objCookie->setCookie('login_email', '');
            }

            if(count($this->arrErr) > 0) {
                SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
            }
            // ログイン判定
            $loginFailFlag = false;
            if(Net_UserAgent_Mobile::isMobile() === true) {
                // モバイルサイト
                if(!$objCustomer->getCustomerDataFromMobilePhoneIdPass($arrForm['login_pass']) &&
                   !$objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'], true)) {
                    $loginFailFlag = true;
                }
            } else {
                // モバイルサイト以外
                if(!$objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
                    $loginFailFlag = true;
                }
            }
            if($loginFailFlag === true) {
                // 仮登録の判定
                $where = "email = ? AND status = 1 AND del_flg = 0";
                $ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));

                if($ret > 0) {
                    SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
                } else {
                    SC_Utils_Ex::sfDispSiteError(SITE_LOGIN_ERROR);
                }
            }

            if(Net_UserAgent_Mobile::isMobile() === true) {
                // ログインが成功した場合は携帯端末IDを保存する。
                $objCustomer->updateMobilePhoneId();

                /*
                 * 携帯メールアドレスが登録されていない場合は,
                 * 携帯メールアドレス登録画面へ遷移
                 */
                $objMobile = new SC_Helper_Mobile_Ex();
                if (!$objMobile->gfIsMobileMailAddress($objCustomer->getValue('email'))) {
                    if (!$objCustomer->hasValue('email_mobile')) {
                        SC_Response_Ex::sendRedirect('../entry/email_mobile.php');
                        exit;
                    }
                }
            }

            //ダウンロード商品判定
            if($this->cartKey == PRODUCT_TYPE_DOWNLOAD){
                // 会員情報の住所を受注一時テーブルに書き込む
                $objPurchase->copyFromCustomer($sqlval, $objCustomer, 'shipping');
                // FIXME ダウンロード商品の場合は配送無し
                $sqlval['deliv_id'] = $objPurchase->getDeliv($this->cartKey);
                $objPurchase->saveShippingTemp($sqlval);
                $objPurchase->saveOrderTemp($uniqid, $sqlval, $objCustomer);
                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // ダウンロード商品有りの場合は、支払方法画面に転送
                SC_Response_Ex::sendRedirect('payment.php');
                exit;
            }
            break;
        // 削除
        case 'delete':
            if (SC_Utils_Ex::sfIsInt($_POST['other_deliv_id'])) {
                $where = "other_deliv_id = ?";
                $arrRet = $objQuery->delete("dtb_other_deliv", $where, array($_POST['other_deliv_id']));
                $this->objFormParam->setValue('select_addr_id', '');
            }
            break;
        // 会員登録住所に送る
        case 'customer_addr':
            $sqlval = array();
            $sqlval['deliv_id'] = $objPurchase->getDeliv($this->cartKey);
            // 会員登録住所がチェックされている場合
            if ($_POST['deliv_check'] == '-1') {
                // 会員情報の住所を受注一時テーブルに書き込む
                $objPurchase->copyFromCustomer($sqlval, $objCustomer, 'shipping');
                $objPurchase->saveShippingTemp($sqlval);
                $objPurchase->saveOrderTemp($uniqid, $sqlval, $objCustomer);

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
                    $objPurchase->saveOrderTemp($uniqid, $sqlval, $objCustomer);

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
            $arrOrderTemp = $objPurchase->getOrderTemp($uniqid);
            if (empty($arrOrderTemp)) $arrOrderTemp = array("");
            $this->objFormParam->setParam($arrOrderTemp);
            break;
        }

        // 登録済み住所を取得
        $this->arrAddr = $objCustomer->getCustomerAddress($_SESSION['customer']['customer_id']);
        // 入力値の取得
        if (!isset($arrErr)) $arrErr = array();
        $this->arrForm = $this->objFormParam->getFormParamList();
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

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("お名前1", "deliv_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前2", "deliv_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(フリガナ・姓)", "deliv_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(フリガナ・名)", "deliv_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("郵便番号1", "deliv_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("郵便番号2", "deliv_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("都道府県", "deliv_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("住所1", "deliv_addr01", MTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("住所2", "deliv_addr02", MTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号1", "deliv_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号2", "deliv_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号3", "deliv_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("", "deliv_check");
    }

    function lfInitLoginFormParam() {
        $this->objLoginFormParam->addParam("記憶する", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objLoginFormParam->addParam("メールアドレス", "login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $this->objLoginFormParam->addParam("パスワード", "login_pass", PASSWORD_LEN1, "", array("EXIST_CHECK"));
        $this->objLoginFormParam->addParam("パスワード", "login_pass1", PASSWORD_LEN1, "", array("EXIST_CHECK", "MIN_LENGTH_CHECK"));
        $this->objLoginFormParam->addParam("パスワード", "login_pass2", PASSWORD_LEN2, "", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }

    /* DBへデータの登録 */
    function lfRegistNewAddrData($uniqid, $objCustomer) {
        $sqlval = $this->objFormParam->getDbArray();
        // 登録データの作成
        $sqlval['deliv_check'] = '1';
        $sqlval['order_temp_id'] = $uniqid;
        $sqlval['update_date'] = 'Now()';
        $sqlval['customer_id'] = $objCustomer->getValue('customer_id');
        $sqlval['order_birth'] = $objCustomer->getValue('birth');

        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRegistTempOrder($uniqid, $sqlval);
    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();
        // 複数項目チェック
        if ($this->getMode() == 'login'){
            $objErr->doFunc(array("メールアドレス", "login_email", STEXT_LEN), array("EXIST_CHECK"));
            $objErr->doFunc(array("パスワード", "login_pass", STEXT_LEN), array("EXIST_CHECK"));
        }
        $objErr->doFunc(array("TEL", "deliv_tel01", "deliv_tel02", "deliv_tel03"), array("TEL_CHECK"));
        return $objErr->arrErr;
    }

    /**
     * 入力されたEmailから余分な改行・空白を削除する
     *
     * @param string $_POST["login_email"]
     */
    function lfConvertEmail(){
        if( strlen($_POST["login_email"]) < 1 ){ return ; }
        $_POST["login_email"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["login_email"]);
    }

    /**
     * 入力されたPassから余分な空白を削除し、最小桁数・最大桁数チェック用に変数に入れる
     *
     * @param string $_POST["login_pass"]
     */
    function lfConvertLoginPass(){
    if( strlen($_POST["login_pass"]) < 1 ){ return ; }
        $_POST["login_pass"] = trim($_POST["login_pass"]); //認証用
        $_POST["login_pass1"] = $_POST["login_pass"];      //最小桁数比較用
        $_POST["login_pass2"] = $_POST["login_pass"];      //最大桁数比較用
    }
}
?>
