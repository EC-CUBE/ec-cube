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
 * ショッピングログインのページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Shopping extends LC_Page {

    // {{{ properties

    /** フォームパラメータ */
    var $objFormParam;

    /** 年 */
    var $year;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = 'ログイン';
        $masterData = new SC_DB_MasterData();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->tpl_onload = 'fnCheckInputDeliv();';
        $this->httpCacheControl('nocache');
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
     * Page のプロセス.
     *
     * @return void
     */
    function action() {
        $objSiteSess = new SC_SiteSession();
        $objCartSess = new SC_CartSession();
        $objCustomer = new SC_Customer();
        $objCookie = new SC_Cookie();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $this->objFormParam = new SC_FormParam();            // フォーム用
        $this->lfInitParam();                                // パラメータ情報の初期化
        $this->objFormParam->setParam($_POST);            // POST値の取得

        $uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($uniqid, $objCartSess);

        $this->tpl_uniqid = $uniqid;

        $this->cartKey = $objCartSess->getKey();

        // ログインチェック
        if($objCustomer->isLoginSuccess(true)) {

            switch ($this->cartKey) {
            // ダウンロード商品の場合は支払方法設定画面に転送
            case PRODUCT_TYPE_DOWNLOAD:
                // 会員情報の住所を受注一時テーブルに書き込む
                $objPurchase->saveOrderTemp($uniqid, array(), $objCustomer);
                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                SC_Response_Ex::sendRedirect('payment.php');
                exit;
                break;

            case PRODUCT_TYPE_NORMAL:
            default:
                // お届け先設定画面に転送
                SC_Response_Ex::sendRedirect('deliv.php');
                exit;
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!SC_Helper_Session_Ex::isValidToken()) {
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            }
        }

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'nonmember_confirm':
            $this->tpl_mainpage = 'shopping/nonmember_input.tpl';
            $this->tpl_title = 'お客様情報入力';
            //非会員のダウンロード商品を含んだ買い物はNG
            if ($this->cartKey == PRODUCT_TYPE_DOWNLOAD) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, $objSiteSess, false,
                                             "ダウンロード商品を含むお買い物は、会員登録が必要です。<br/>お手数ですが、会員登録をお願いします。");
            }
            // ※breakなし
        case 'confirm':
            $this->arrErr = $this->lfCheckError();

            // 入力エラーなし
            if(count($this->arrErr) == 0) {
                // DBへのデータ登録
                $this->lfRegistData($uniqid, $objPurchase, $objCustomer, $this->cartKey);
                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // お支払い方法選択ページへ移動
                SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URL_PATH);
                exit;
            }

            break;
        // 前のページに戻る
        case 'return':
            // 確認ページへ移動
            SC_Response_Ex::sendRedirect(CART_URL_PATH);
            exit;
            break;

        case 'multiple':
            $this->arrErr = $this->lfCheckError();

            // 入力エラーなし
            if(count($this->arrErr) == 0) {
                // DBへのデータ登録
                $this->lfRegistData($uniqid, $objPurchase, $objCustomer, $this->cartKey, true);
                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();

                SC_Response_Ex::sendRedirect(MULTIPLE_URL_PATH);
                exit;
            }
            // breakなし

        case 'nonmember':
            $this->tpl_mainpage = 'shopping/nonmember_input.tpl';
            $this->tpl_title = 'お客様情報入力';
            //非会員のダウンロード商品を含んだ買い物はNG
            if ($this->cartKey == PRODUCT_TYPE_DOWNLOAD) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, $objSiteSess, false,
                                             "ダウンロード商品を含むお買い物は、会員登録が必要です。<br/>お手数ですが、会員登録をお願いします。");
            }
            // ※breakなし
        default:
            if(isset($_GET['from']) && $_GET['from'] == 'nonmember') {
                $this->tpl_mainpage = 'shopping/nonmember_input.tpl';
                $this->tpl_title = 'お客様情報入力';
            }
            $arrOrderTemp = $objPurchase->getOrderTemp($uniqid);
            if (empty($arrOrderTemp)) $arrOrderTemp = array('order_email' => "",
                                                            'order_birth' => "");
            $arrShippingTemp = $objPurchase->getShippingTemp();
            // DB値の取得
            $this->objFormParam->setParam($arrOrderTemp);
            /*
             * count($arrShippingTemp) > 1 は複数配送であり,
             * $arrShippingTemp[0] は注文者が格納されている
             */
            if (count($arrShippingTemp) > 1) {
                $this->objFormParam->setParam($arrShippingTemp[1]);
            } else {
                $this->objFormParam->setParam($arrShippingTemp[0]);
            }
            $this->objFormParam->setValue('order_email02', $arrOrderTemp['order_email']);
            $this->objFormParam->setDBDate($arrOrderTemp['order_birth']);
            $objPurchase->unsetShippingTemp();
        }

        // クッキー判定
        $this->tpl_login_email = $objCookie->getCookie('login_email');
        if($this->tpl_login_email != "") {
            $this->tpl_login_memory = "1";
        }

        // 生年月日選択肢の取得
        $objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
        $this->arrYear = $objDate->getYear('', 1950, '');
        $this->arrMonth = $objDate->getMonth(true);
        $this->arrDay = $objDate->getDay(true);

        // 入力値の取得
        $this->arrForm = $this->objFormParam->getFormParamList();

        $this->transactionid = SC_Helper_Session_Ex::getToken();

        // 携帯端末IDが一致する会員が存在するかどうかをチェックする。
        if(Net_UserAgent_Mobile::isMobile() === true) {
            $this->tpl_valid_phone_id = $objCustomer->checkMobilePhoneId();
        }
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileAction() {
        $objView = new SC_MobileView();
        $objSiteSess = new SC_SiteSession();
        $objCartSess = new SC_CartSession();
        $objCustomer = new SC_Customer();
        $objCookie = new SC_Cookie();
        $this->objFormParam = new SC_FormParam();            // フォーム用
        $helperMobile = new SC_Helper_Mobile_Ex();
        $objDb = new SC_Helper_DB_Ex();
        $this->lfInitParam();                                // パラメータ情報の初期化
        $this->objFormParam->setParam($_POST);            // POST値の取得

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $uniqid = SC_Utils_Ex::sfCheckNormalAccess($objSiteSess, $objCartSess);

        $this->tpl_uniqid = $uniqid;

        //ダウンロード商品判定
        $this->cartdown = $objDb->chkCartDown($objCartSess);

        // ログインチェック
        if($objCustomer->isLoginSuccess(true)) {
            // すでにログインされている場合
            if ($this->cartdown == 2) {
                // 会員情報の住所を受注一時テーブルに書き込む
                $objDb->sfRegistDelivData($uniqid, $objCustomer);
                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                //カート内が全てダウンロード商品の場合は支払方法設定画面に転送
                SC_Response_Ex::sendRedirect('payment.php');
            } else {
                // お届け先設定画面に転送
                SC_Response_Ex::sendRedirect('deliv.php');
            }
            exit;
        }

        // 携帯端末IDが一致する会員が存在するかどうかをチェックする。
        $this->tpl_valid_phone_id = $objCustomer->checkMobilePhoneId();

        // クッキー判定
        $this->tpl_login_email = $objCookie->getCookie('login_email');
        if($this->tpl_login_email != "") {
            $this->tpl_login_memory = "1";
        }
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
        $this->objFormParam->addParam("お名前(姓)", "order_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(名)", "order_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(フリガナ・姓)", "order_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(フリガナ・名)", "order_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("郵便番号1", "order_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("郵便番号2", "order_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("都道府県", "order_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("住所1", "order_addr01", MTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("住所2", "order_addr02", MTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号1", "order_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号2", "order_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号3", "order_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("FAX番号1", "order_fax01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("FAX番号2", "order_fax02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("FAX番号3", "order_fax03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("メールアドレス", "order_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"));
        $this->objFormParam->addParam("メールアドレス（確認）", "order_email02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"), "", false);
        $this->objFormParam->addParam("年", "year", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $this->objFormParam->addParam("月", "month", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $this->objFormParam->addParam("日", "day", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $this->objFormParam->addParam("性別", "order_sex", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("職業", "order_job", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("別のお届け先", "deliv_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("お名前(姓)", "shipping_name01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(名)", "shipping_name02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(フリガナ・姓)", "shipping_kana01", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(フリガナ・名)", "shipping_kana02", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("郵便番号1", "shipping_zip01", ZIP01_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("郵便番号2", "shipping_zip02", ZIP02_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("都道府県", "shipping_pref", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("住所1", "shipping_addr01", MTEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("住所2", "shipping_addr02", MTEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号1", "shipping_tel01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号2", "shipping_tel02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号3", "shipping_tel03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("メールマガジン", "mail_flag", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
    }

    /* DBへデータの登録 */
    function lfRegistData($uniqid, &$objPurchase, &$objCustomer, $productTypeId, $isMultiple = false) {
        $params = $this->objFormParam->getHashArray();
        $sqlval = $this->objFormParam->getDbArray();
        // 登録データの作成
        $sqlval['order_birth'] = SC_Utils_Ex::sfGetTimestamp($params['year'], $params['month'], $params['day']);
        $sqlval['update_date'] = 'Now()';
        $sqlval['customer_id'] = '0';

        // お届け先を指定しない場合、
        if ($params['deliv_check'] != '1') {
            // order_* を shipping_* へコピー
            $objPurchase->copyFromOrder($sqlval, $params);
        }

        $deliv_id = $objPurchase->getDeliv($productTypeId);
        $order_val = array('deliv_id' => $deliv_id);
        $shipping_val = array('deliv_id' => $deliv_id);

        /*
         * order_* と shipping_* をそれぞれ $_SESSION['shipping'][$shipping_id]
         * に, shipping_* というキーで保存
         */
        foreach ($sqlval as $key => $val) {
            if (preg_match('/^order_/', $key)) {
                $order_val['shipping_' . str_replace('order_', '', $key)] = $val;
            } elseif (preg_match('/^shipping_/', $key)) {
                $shipping_val[$key] = $val;
            }
        }

        if ($isMultiple) {
            $objPurchase->saveShippingTemp($order_val, 0);
            if ($params['deliv_check'] == '1') {
                $objPurchase->saveShippingTemp($shipping_val, 1);
            }
        } else {
            if ($params['deliv_check'] == '1') {
                $objPurchase->saveShippingTemp($shipping_val, 0);
            } else {
                $objPurchase->saveShippingTemp($order_val, 0);
            }
        }
        $objPurchase->saveOrderTemp($uniqid, $sqlval, $objCustomer);
    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力値の変換
        $this->objFormParam->convParam();
        $this->objFormParam->toLower('order_mail');
        $this->objFormParam->toLower('order_mail_check');

        // 入力データを渡す。
        $arrRet = $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        // 別のお届け先チェック
        if(isset($_POST['deliv_check']) && $_POST['deliv_check'] == "1") {
            $objErr->doFunc(array("お名前(姓)", "shipping_name01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("お名前(名)", "shipping_name02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("お名前(フリガナ・姓)", "shipping_kana01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("お名前(フリガナ・名)", "shipping_kana02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("郵便番号1", "shipping_zip01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("郵便番号2", "shipping_zip02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("都道府県", "shipping_pref"), array("EXIST_CHECK"));
            $objErr->doFunc(array("住所1", "shipping_addr01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("住所2", "shipping_addr02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("電話番号1", "shipping_tel01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("電話番号2", "shipping_tel02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("電話番号3", "shipping_tel03"), array("EXIST_CHECK"));
        }

        // 複数項目チェック
        $objErr->doFunc(array("TEL", "order_tel01", "order_tel02", "order_tel03"), array("TEL_CHECK"));
        $objErr->doFunc(array("FAX", "order_fax01", "order_fax02", "order_fax03"), array("TEL_CHECK"));
        $objErr->doFunc(array("郵便番号", "order_zip01", "order_zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("TEL", "shipping_tel01", "shipping_tel02", "shipping_tel03"), array("TEL_CHECK"));
        $objErr->doFunc(array("郵便番号", "shipping_zip01", "shipping_zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_BIRTHDAY"));
        $objErr->doFunc(array("メールアドレス", "メールアドレス（確認）", "order_email", "order_email02"), array("EQUAL_CHECK"));

        return $objErr->arrErr;
    }
}
?>
