<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 顧客情報修正 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Customer_Edit extends LC_Page {

    // {{{ properties
    // TODO
    var $arrSession;
	var $tpl_mode;
	var $list_data;

	var $arrErr;
	var $arrYear;
	var $arrMonth;
	var $arrDay;
	var $arrPref;
	var $arrJob;
	var $arrSex;
	var $arrReminder;
	var $count;

	var $tpl_strnavi;


    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'customer/edit.tpl';
		$this->tpl_mainno = 'customer';
		$this->tpl_subnavi = 'customer/subnavi.tpl';
		$this->tpl_subno = 'index';
		$this->tpl_pager = DATA_PATH . 'Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '顧客マスタ';

        $masterData = new SC_DB_MasterData_Ex();
		$this->arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
		$this->arrJob = $masterData->getMasterData("mtb_job");
		$this->arrSex = $masterData->getMasterData("mtb_sex");
		$this->arrReminder = $masterData->getMasterData("mtb_reminder");
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        $this->objQuery = new SC_Query();
        $this->objConn = new SC_DBConn();
        $objView = new SC_AdminView();
        $objDb = new SC_Helper_DB_Ex();
        $objDate = new SC_Date(1901);
        $this->arrYear = $objDate->getYear();	//　日付プルダウン設定
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        //---- 登録用カラム配列
        $arrRegistColumn = array(
                                 array(  "column" => "name01",		"convert" => "aKV" ),
                                 array(  "column" => "name02",		"convert" => "aKV" ),
                                 array(  "column" => "kana01",		"convert" => "CKV" ),
                                 array(  "column" => "kana02",		"convert" => "CKV" ),
                                 array(  "column" => "zip01",		"convert" => "n" ),
                                 array(  "column" => "zip02",		"convert" => "n" ),
                                 array(  "column" => "pref",		"convert" => "n" ),
                                 array(  "column" => "addr01",		"convert" => "aKV" ),
                                 array(  "column" => "addr02",		"convert" => "aKV" ),
                                 array(  "column" => "email",		"convert" => "a" ),
                                 array(  "column" => "email_mobile",	"convert" => "a" ),
                                 array(  "column" => "tel01",		"convert" => "n" ),
                                 array(  "column" => "tel02",		"convert" => "n" ),
                                 array(  "column" => "tel03",		"convert" => "n" ),
                                 array(  "column" => "fax01",		"convert" => "n" ),
                                 array(  "column" => "fax02",		"convert" => "n" ),
                                 array(  "column" => "fax03",		"convert" => "n" ),
                                 array(  "column" => "sex",			"convert" => "n" ),
                                 array(  "column" => "job",			"convert" => "n" ),
                                 array(  "column" => "birth",		"convert" => "n" ),
                                 array(  "column" => "password",	"convert" => "a" ),
                                 array(  "column" => "reminder",	"convert" => "n" ),
                                 array(  "column" => "reminder_answer", "convert" => "aKV" ),
                                 array(  "column" => "mailmaga_flg", "convert" => "n" ),
                                 array(  "column" => "note",		"convert" => "aKV" ),
                                 array(  "column" => "point",		"convert" => "n" ),
                                 array(  "column" => "status",		"convert" => "n" )
                                 );

        //---- 登録除外用カラム配列
        $arrRejectRegistColumn = array("year", "month", "day");

        // 検索条件を保持
        if ($_POST['mode'] == "edit_search") {
            $arrSearch = $_POST;
        }else{
            $arrSearch = $_POST['search_data'];
        }
        if(is_array($arrSearch)){
            foreach($arrSearch as $key => $val){
                $arrSearchData[$key] = $val;
            }
        }

        $this->arrSearchData= $arrSearchData;

        //----　顧客編集情報取得
        if (($_POST["mode"] == "edit" || $_POST["mode"] == "edit_search") && is_numeric($_POST["edit_customer_id"])) {

            //--　顧客データ取得
            $sql = "SELECT * FROM dtb_customer WHERE del_flg = 0 AND customer_id = ?";
            $result = $this->objConn->getAll($sql, array($_POST["edit_customer_id"]));
            $this->list_data = $result[0];

            $birth = split(" ", $this->list_data["birth"]);
            $birth = split("-",$birth[0]);

            $this->list_data["year"] = $birth[0];
            $this->list_data["month"] = $birth[1];
            $this->list_data["day"] = $birth[2];

            $this->list_data["password"] = DEFAULT_PASSWORD;
            //DB登録のメールアドレスを渡す
            $this->tpl_edit_email = $result[0]['email'];
            //購入履歴情報の取得
            $this->arrPurchaseHistory = $this->lfPurchaseHistory($_POST['edit_customer_id']);
            // 支払い方法の取得
            $this->arrPayment = $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
        }

        //----　顧客情報編集
        if ( $_POST["mode"] != "edit" && $_POST["mode"] != "edit_search" && is_numeric($_POST["customer_id"])) {

            //-- POSTデータの引き継ぎ
            $this->arrForm = $_POST;
            $this->arrForm['email'] = strtolower($this->arrForm['email']);		// emailはすべて小文字で処理

            //-- 入力データの変換
            $this->arrForm = $this->lfConvertParam($this->arrForm, $arrRegistColumn);
            //-- 入力チェック
            $this->arrErr = $this->lfErrorCheck($this->arrForm);

            //-- 入力エラー発生 or リターン時
            if ($this->arrErr || $_POST["mode"] == "return") {
                foreach($this->arrForm as $key => $val) {
                    $this->list_data[ $key ] = $val;
                }
                //購入履歴情報の取得
                $this->arrPurchaseHistory = $this->lfPurchaseHistory($_POST['customer_id']);
                // 支払い方法の取得
                $this->arrPayment = $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

            } else {
                //-- 確認
                if ($_POST["mode"] == "confirm") {
                    $this->tpl_mainpage = 'customer/edit_confirm.tpl';
                    $passlen = strlen($this->arrForm['password']);
                    $this->passlen = $this->lfPassLen($passlen);

                }
                //--　編集
                if($_POST["mode"] == "complete") {
                    $this->tpl_mainpage = 'customer/edit_complete.tpl';

                    // 現在の会員情報を取得する
                    $arrCusSts = $this->objQuery->getOne("SELECT status FROM dtb_customer WHERE customer_id = ?", array($_POST["customer_id"]));

                    // 会員情報が変更されている場合にはシークレット№も更新する。
                    if ($arrCusSts != $_POST['status']){
                        $secret = SC_Utils_Ex::sfGetUniqRandomId("r");
                        $this->arrForm['secret_key'] = $secret;
                        array_push($arrRegistColumn, array('column' => 'secret_key', 'convert' => 'n'));
                    }
                    //-- 編集登録
                    $objDb->sfEditCustomerData($this->arrForm, $arrRegistColumn);
                }
            }
        }

        //----　ページ表示
        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }


    // 編集登録
    function lfRegisDatat($array, $arrRegistColumn) {

        foreach ($arrRegistColumn as $data) {
            if($array[$data["column"]] != "") {
                $arrRegist[$data["column"]] = $array[$data["column"]];
            } else {
                $arrRegist[$data["column"]] = NULL;
            }
        }
        if (strlen($array["year"]) > 0) {
            $arrRegist["birth"] = $array["year"] ."/". $array["month"] ."/". $array["day"] ." 00:00:00";
        }

        //-- パスワードの更新がある場合は暗号化。（更新がない場合はUPDATE文を構成しない）
        if ($array["password"] != DEFAULT_PASSWORD) {
            $arrRegist["password"] = sha1($array["password"] . ":" . AUTH_MAGIC);
        } else {
            unset($arrRegist['password']);
        }

        $arrRegist["update_date"] = "Now()";

        //-- 編集登録実行
        $this->objConn->query("BEGIN");
        $this->objQuery->Insert("dtb_customer", $arrRegist, "customer_id = '" .addslashes($array["customer_id"]). "'");

        $this->objConn->query("COMMIT");
    }


    //----　取得文字列の変換
    function lfConvertParam($array, $arrRegistColumn) {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  全角英数字を半角英数字に変換する
         */
        // カラム名とコンバート情報
        foreach ($arrRegistColumn as $data) {
            $arrConvList[ $data["column"] ] = $data["convert"];
        }
        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(strlen(($array[$key])) > 0) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }
        return $array;
    }

    //---- 入力エラーチェック
    function lfErrorCheck($array) {

        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("会員状態", 'status'), array("EXIST_CHECK"));
        $objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("フリガナ（姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("フリガナ（名）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("ご住所（1）", "addr01", MTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("ご住所（2）", "addr02", MTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("EXIST_CHECK", "NO_SPTAB", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));

        //現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
        if (strlen($array["email"]) > 0) {
            $sql = "SELECT customer_id FROM dtb_customer WHERE email ILIKE ? escape '#' AND (status = 1 OR status = 2) AND del_flg = 0 AND customer_id <> ?";
            $checkMail = ereg_replace( "_", "#_", $array["email"]);
            $result = $this->objConn->getAll($sql, array($checkMail, $array["customer_id"]));
            if (count($result) > 0) {
                $objErr->arrErr["email"] .= "※ すでに登録されているメールアドレスです。";
            }
        }

        $objErr->doFunc(array('メールアドレス(モバイル)', "email_mobile", MTEXT_LEN) ,array("EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        //現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
        if (strlen($array["email_mobile"]) > 0) {
            $sql = "SELECT customer_id FROM dtb_customer WHERE email_mobile ILIKE ? escape '#' AND (status = 1 OR status = 2) AND del_flg = 0 AND customer_id <> ?";
            $checkMail = ereg_replace( "_", "#_", $array["email_mobile"]);
            $result = $this->objConn->getAll($sql, array($checkMail, $array["customer_id"]));
            if (count($result) > 0) {
                $objErr->arrErr["email_mobile"] .= "※ すでに登録されているメールアドレス(モバイル)です。";
            }
        }


        $objErr->doFunc(array("お電話番号1", 'tel01'), array("EXIST_CHECK"));
        $objErr->doFunc(array("お電話番号2", 'tel02'), array("EXIST_CHECK"));
        $objErr->doFunc(array("お電話番号3", 'tel03'), array("EXIST_CHECK"));
        $objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
        $objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03", TEL_LEN) ,array("TEL_CHECK"));
        $objErr->doFunc(array("ご性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("ご職業", "job") ,array("NUM_CHECK"));
        if ($array["password"] != DEFAULT_PASSWORD) {
            $objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
        }
        $objErr->doFunc(array("パスワードを忘れたときのヒント 質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("パスワードを忘れたときのヒント 答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("メールマガジン", "mailmaga_flg") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
        $objErr->doFunc(array("SHOP用メモ", 'note', LTEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("所持ポイント", "point", TEL_LEN) ,array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        return $objErr->arrErr;

    }

    //購入履歴情報の取得
    function lfPurchaseHistory($customer_id){
		$this->tpl_pageno = $_POST['search_pageno'];
		$this->edit_customer_id = $customer_id;

		// ページ送りの処理
		$page_max = SEARCH_PMAX;
		//購入履歴の件数取得
		$this->tpl_linemax = $this->objQuery->count("dtb_order","customer_id=? AND del_flg = 0 ", array($customer_id));
		$linemax = $this->tpl_linemax;

		// ページ送りの取得
		$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchPage2", NAVI_PMAX);
		$this->arrPagenavi = $objNavi->arrPagenavi;
		$this->arrPagenavi['mode'] = 'edit';
		$startno = $objNavi->start_row;

		// 取得範囲の指定(開始行番号、行数のセット)
		$this->objQuery->setlimitoffset($page_max, $startno);
		// 表示順序
		$order = "order_id DESC";
		$this->objQuery->setorder($order);
		//購入履歴情報の取得
		$arrPurchaseHistory = $this->objQuery->select("*", "dtb_order", "customer_id=? AND del_flg = 0 ", array($customer_id));

		return $arrPurchaseHistory;
    }

    //確認ページ用パスワード表示用
    function lfPassLen($passlen){
        $ret = "";
        for ($i=0;$i<$passlen;true){
            $ret.="*";
            $i++;
        }
        return $ret;
    }
}
?>
