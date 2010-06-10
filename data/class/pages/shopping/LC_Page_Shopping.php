<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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

/**
 * ショッピングログインのページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Shopping.php 15532 2007-08-31 14:39:46Z nanasess $
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
        $this->tpl_mainpage = 'shopping/index.tpl';
        $this->tpl_column_num = 1;
        $masterData = new SC_DB_MasterData();
        $this->arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->tpl_onload = 'fnCheckInputDeliv();';
        $this->allowClientCache();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        global $objCampaignSess;

        $conn = new SC_DBConn();
        $objView = new SC_SiteView();
        $objSiteSess = new SC_SiteSession();
        $objCartSess = new SC_CartSession();
        $objCampaignSess = new SC_CampaignSession();
        $objCustomer = new SC_Customer();
        $objCookie = new SC_Cookie();
        $this->objFormParam = new SC_FormParam();            // フォーム用
        $this->lfInitParam();                                // パラメータ情報の初期化
        $this->objFormParam->setParam($_POST);            // POST値の取得

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $uniqid = SC_Utils_Ex::sfCheckNormalAccess($objSiteSess, $objCartSess);
        $this->tpl_uniqid = $uniqid;

        // ログインチェック
        if($objCustomer->isLoginSuccess()) {
            // すでにログインされている場合は、お届け先設定画面に転送
            $this->sendRedirect($this->getLocation("./deliv.php"), array());
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!$this->isValidToken()) {
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            }
        }

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'nonmember_confirm':
            $this->lfSetNonMember($this);
            // ※breakなし
        case 'confirm':
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->objFormParam->toLower('order_mail');
            $this->objFormParam->toLower('order_mail_check');

            $this->arrErr = $this->lfCheckError();

            // 入力エラーなし
            if(count($this->arrErr) == 0) {
                // DBへのデータ登録
                $this->lfRegistData($uniqid);

                // お届け先のコピー
                $this->lfCopyDeliv($uniqid, $_POST);

                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // お支払い方法選択ページへ移動
                $this->sendRedirect($this->getLocation(URL_SHOP_PAYMENT));
                exit;
            }

            break;
        // 前のページに戻る
        case 'return':
            // 確認ページへ移動
            $this->sendRedirect($this->getLocation(URL_CART_TOP));
            exit;
            break;
        case 'nonmember':
            $this->lfSetNonMember($this);
            // ※breakなし
        default:
            if(isset($_GET['from']) && $_GET['from'] == 'nonmember') {
                $this->lfSetNonMember($this);
            }
            // ユーザユニークIDの取得
            $uniqid = $objSiteSess->getUniqId();
            $objQuery = new SC_Query();
            $where = "order_temp_id = ?";
            $arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($uniqid));
            if (empty($arrRet)) $arrRet = array(
                                                array('order_email' => "",
                                                      'order_birth' => ""));

            // DB値の取得
            $this->objFormParam->setParam($arrRet[0]);
            $this->objFormParam->setValue('order_email_check', $arrRet[0]['order_email']);
            $this->objFormParam->setDBDate($arrRet[0]['order_birth']);
            break;
        }

        // クッキー判定
        $this->tpl_login_email = $objCookie->getCookie('login_email');
        if($this->tpl_login_email != "") {
            $this->tpl_login_memory = "1";
        }

        // 選択用日付の取得
        $objDate = new SC_Date(START_BIRTH_YEAR);
        $this->arrYear = $objDate->getYear('', 1950);    //　日付プルダウン設定
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        if($this->year == '') {
            $this->year = '----';
        }

        // 入力値の取得
        $this->arrForm = $this->objFormParam->getFormParamList();

        if(empty($this->arrForm['year']['value'])){
            $this->arrForm['year']['value'] = '----';
        }

        $this->transactionid = $this->getToken();
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
        $this->tpl_mainpage = MOBILE_TEMPLATE_DIR . 'shopping/index.tpl';
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $conn = new SC_DBConn();
        $objView = new SC_MobileView();
        $objSiteSess = new SC_SiteSession();
        $objCartSess = new SC_CartSession();
        $objCustomer = new SC_Customer();
        $objCookie = new SC_Cookie();
        $this->objFormParam = new SC_FormParam();            // フォーム用
        $helperMobile = new SC_Helper_Mobile_Ex();
        $this->lfInitParam();                                // パラメータ情報の初期化
        $this->objFormParam->setParam($_POST);            // POST値の取得

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $uniqid = SC_Utils_Ex::sfCheckNormalAccess($objSiteSess, $objCartSess);

        $this->tpl_uniqid = $uniqid;

        // ログインチェック
        if($objCustomer->isLoginSuccess(true)) {
            // すでにログインされている場合は、お届け先設定画面に転送
            $this->sendRedirect($this->getLocation('./deliv.php'), true);
            exit;
        }

        // 携帯端末IDが一致する会員が存在するかどうかをチェックする。
        $this->tpl_valid_phone_id = $objCustomer->checkMobilePhoneId();

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'nonmember_confirm':
            $this->lfSetNonMember($this);
            // ※breakなし
        case 'confirm':
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->objFormParam->toLower('order_mail');
            $this->objFormParam->toLower('order_mail_check');

            $this->arrErr = $this->lfCheckError();

            // 入力エラーなし
            if(count($this->arrErr) == 0) {
                // DBへのデータ登録
                $this->lfRegistData($uniqid);

                // お届け先のコピー
                $this->lfCopyDeliv($uniqid, $_POST);

                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // お支払い方法選択ページへ移動
                $this->sendRedirect($this->getLocation(MOBILE_URL_SHOP_PAYMENT), true);
                exit;
            }

            break;
            // 前のページに戻る
        case 'return':
            // 確認ページへ移動
            $this->sendRedirect($this->getLocation(MOBILE_URL_CART_TOP), true);
            exit;
            break;
        case 'nonmember':
            $this->lfSetNonMember($this);
            // ※breakなし
        default:
            if($_GET['from'] == 'nonmember') {
                $this->lfSetNonMember($this);
            }
            // ユーザユニークIDの取得
            $uniqid = $objSiteSess->getUniqId();
            $objQuery = new SC_Query();
            $where = "order_temp_id = ?";
            $arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($uniqid));

            if (empty($arrRet)) $arrRet = array(
                                                array('order_email' => "",
                                                      'order_birth' => ""));

            // DB値の取得
            $this->objFormParam->setParam($arrRet[0]);
            $this->objFormParam->setValue('order_email_check', $arrRet[0]['order_email']);
            $this->objFormParam->setDBDate($arrRet[0]['order_birth']);
            break;
        }

        // クッキー判定
        $this->tpl_login_email = $objCookie->getCookie('login_email');
        if($this->tpl_login_email != "") {
            $this->tpl_login_memory = "1";
        }

        // 選択用日付の取得
        $objDate = new SC_Date(START_BIRTH_YEAR);
        $this->arrYear = $objDate->getYear('', 1950);    //　日付プルダウン設定
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        if($this->year == '') {
            $this->year = '----';
        }

        // 入力値の取得
        $this->arrForm = $this->objFormParam->getFormParamList();

        if($this->arrForm['year']['value'] == ""){
            $this->arrForm['year']['value'] = '----';
        }

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

    /* 非会員入力ページのセット */
    function lfSetNonMember(&$objPage) {
        $objPage->tpl_mainpage = 'shopping/nonmember_input.tpl';
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("お名前（姓）", "order_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前（名）", "order_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("フリガナ（セイ）", "order_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("フリガナ（メイ）", "order_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("郵便番号1", "order_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("郵便番号2", "order_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("都道府県", "order_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("住所1", "order_addr01", MTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("住所2", "order_addr02", MTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号1", "order_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号2", "order_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号3", "order_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("FAX番号1", "order_fax01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("FAX番号2", "order_fax02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("FAX番号3", "order_fax03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("メールアドレス", "order_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"));
        $this->objFormParam->addParam("メールアドレス（確認）", "order_email_check", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"), "", false);
        $this->objFormParam->addParam("年", "year", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $this->objFormParam->addParam("月", "month", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $this->objFormParam->addParam("日", "day", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $this->objFormParam->addParam("性別", "order_sex", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("職業", "order_job", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("別のお届け先", "deliv_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("お名前（姓）", "deliv_name01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前（名）", "deliv_name02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("フリガナ（セイ）", "deliv_kana01", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("フリガナ（メイ）", "deliv_kana02", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("郵便番号1", "deliv_zip01", ZIP01_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("郵便番号2", "deliv_zip02", ZIP02_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("都道府県", "deliv_pref", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("住所1", "deliv_addr01", MTEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("住所2", "deliv_addr02", MTEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号1", "deliv_tel01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号2", "deliv_tel02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号3", "deliv_tel03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("メールマガジン", "mail_flag", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
    }

    /* DBへデータの登録 */
    function lfRegistData($uniqid) {
        $arrRet = $this->objFormParam->getHashArray();
        $sqlval = $this->objFormParam->getDbArray();
        // 登録データの作成
        $sqlval['order_temp_id'] = $uniqid;
        $sqlval['order_birth'] = SC_Utils_Ex::sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);
        $sqlval['update_date'] = 'Now()';
        $sqlval['customer_id'] = '0';

        // 既存データのチェック
        $objQuery = new SC_Query();
        $where = "order_temp_id = ?";
        $cnt = $objQuery->count("dtb_order_temp", $where, array($uniqid));
        // 既存データがない場合
        if ($cnt == 0) {
            $sqlval['create_date'] = 'Now()';
            $objQuery->insert("dtb_order_temp", $sqlval);
        } else {
            $objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
        }

    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet = $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        // 別のお届け先チェック
        if(isset($_POST['deliv_check']) && $_POST['deliv_check'] == "1") {
            $objErr->doFunc(array("お名前（姓）", "deliv_name01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("お名前（名）", "deliv_name02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("フリガナ（セイ）", "deliv_kana01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("フリガナ（メイ）", "deliv_kana02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("郵便番号1", "deliv_zip01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("郵便番号2", "deliv_zip02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("都道府県", "deliv_pref"), array("EXIST_CHECK"));
            $objErr->doFunc(array("住所1", "deliv_addr01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("住所2", "deliv_addr02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("電話番号1", "deliv_tel01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("電話番号2", "deliv_tel02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("電話番号3", "deliv_tel03"), array("EXIST_CHECK"));
        }

        // 複数項目チェック
        $objErr->doFunc(array("TEL", "order_tel01", "order_tel02", "order_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
        $objErr->doFunc(array("FAX", "order_fax01", "order_fax02", "order_fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
        $objErr->doFunc(array("郵便番号", "order_zip01", "order_zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("TEL", "deliv_tel01", "deliv_tel02", "deliv_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
        $objErr->doFunc(array("FAX", "deliv_fax01", "deliv_fax02", "deliv_fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
        $objErr->doFunc(array("郵便番号", "deliv_zip01", "deliv_zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
        $objErr->doFunc(array("メールアドレス", "メールアドレス（確認）", "order_email", "order_email_check"), array("EQUAL_CHECK"));

        //既存メールアドレスでの登録不可（購入時強制会員登録が有効の場合のみ）
        if (PURCHASE_CUSTOMER_REGIST == '1' && strlen($arrRet["order_email"]) > 0) {
            $array['email'] = strtolower($arrRet['order_email']);
            $objQuery = new SC_Query();
            $arrEmailCheck = $objQuery->select("email, update_date, del_flg", "dtb_customer","email = ? OR email_mobile = ? ORDER BY del_flg", array($array["email"], $array["email"]));

            if(!empty($arrEmailCheck)) {
                if($arrEmailCheck[0]['del_flg'] != '1') {
                    // 会員である場合
                    $objErr->arrErr["order_email"] .= "※ すでに会員登録で使用されているメールアドレスです。<br />";
                } else {
                    // 退会した会員である場合
                    $leave_time = SC_Utils_Ex::sfDBDatetoTime($arrEmailCheck[0]['update_date']);
                    $now_time = time();
                    $pass_time = $now_time - $leave_time;
                    // 退会から何時間-経過しているか判定する。
                    $limit_time = ENTRY_LIMIT_HOUR * 3600;
                    if($pass_time < $limit_time) {
                        $objErr->arrErr["order_email"] .= "※ 退会から一定期間の間は、同じメールアドレスを使用することはできません。<br />";
                    }
                }
            }
        }

        return $objErr->arrErr;
    }

    // 受注一時テーブルのお届け先をコピーする
    function lfCopyDeliv($uniqid, $arrData) {
        $objQuery = new SC_Query();

        // 別のお届け先を指定していない場合、お届け先に登録住所をコピーする。
        if($arrData["deliv_check"] != "1") {
            $sqlval['deliv_name01'] = $arrData['order_name01'];
            $sqlval['deliv_name02'] = $arrData['order_name02'];
            $sqlval['deliv_kana01'] = $arrData['order_kana01'];
            $sqlval['deliv_kana02'] = $arrData['order_kana02'];
            $sqlval['deliv_pref'] = $arrData['order_pref'];
            $sqlval['deliv_zip01'] = $arrData['order_zip01'];
            $sqlval['deliv_zip02'] = $arrData['order_zip02'];
            $sqlval['deliv_addr01'] = $arrData['order_addr01'];
            $sqlval['deliv_addr02'] = $arrData['order_addr02'];
            $sqlval['deliv_tel01'] = $arrData['order_tel01'];
            $sqlval['deliv_tel02'] = $arrData['order_tel02'];
            $sqlval['deliv_tel03'] = $arrData['order_tel03'];
            $where = "order_temp_id = ?";
            $objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
        }
    }
}
?>
