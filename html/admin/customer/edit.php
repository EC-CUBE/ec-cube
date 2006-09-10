<?php

require_once("../require.php");

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

//---- ページ表示用クラス
class LC_Page {
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
				
	function LC_Page() {
		$this->tpl_mainpage = 'customer/edit.tpl';
		$this->tpl_mainno = 'customer';
		$this->tpl_subnavi = 'customer/subnavi.tpl';
		$this->tpl_subno = 'index';
		$this->tpl_pager = ROOT_DIR . 'data/Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '顧客マスタ';
		
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrJob;
		$this->arrJob = $arrJob;
		global $arrSex;		
		$this->arrSex = $arrSex;
		global $arrReminder;
		$this->arrReminder = $arrReminder;
	}
}
$objQuery = new SC_Query();
$objConn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objDate = new SC_Date(1901);
$objPage->arrYear = $objDate->getYear();	//　日付プルダウン設定
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

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

$objPage->arrSearchData= $arrSearchData;

//----　顧客編集情報取得
if (($_POST["mode"] == "edit" || $_POST["mode"] == "edit_search") && is_numeric($_POST["edit_customer_id"])) {

	//--　顧客データ取得
	$sql = "SELECT A.*, EXTRACT(EPOCH FROM A.birth) AS birth_unix, B.mail_flag FROM dtb_customer AS A LEFT OUTER JOIN dtb_customer_mail AS B USING(email)
			 WHERE A.delete = 0 AND A.customer_id = ?";
	$result = $objConn->getAll($sql, array($_POST["edit_customer_id"]));
	$objPage->list_data = $result[0];
	
	if (strlen($objPage->list_data["birth_unix"]) > 0) {
		$objPage->list_data["year"] = date("Y", $objPage->list_data["birth_unix"]);
		$objPage->list_data["month"] = date("m", $objPage->list_data["birth_unix"]);
		$objPage->list_data["day"] = date("d", $objPage->list_data["birth_unix"]);
	}
	$objPage->list_data["password"] = DEFAULT_PASSWORD;
	//DB登録のメールアドレスを渡す
	$objPage->tpl_edit_email = $result[0]['email'];
	//購入履歴情報の取得
	$objPage->arrPurchaseHistory = lfPurchaseHistory($_POST['edit_customer_id']);
	// 支払い方法の取得
	$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
}

//----　顧客情報編集
if ( $_POST["mode"] != "edit" && is_numeric($_POST["customer_id"])) {

	//-- POSTデータの引き継ぎ
	$objPage->arrForm = $_POST;
	$objPage->arrForm['email'] = strtolower($objPage->arrForm['email']);		// emailはすべて小文字で処理

	//-- 入力データの変換
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
	//-- 入力チェック
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);

	//-- 入力エラー発生 or リターン時
	if ($objPage->arrErr || $_POST["mode"] == "return") {
		foreach($objPage->arrForm as $key => $val) {
			$objPage->list_data[ $key ] = $val;
		}
		//購入履歴情報の取得
		$objPage->arrPurchaseHistory = lfPurchaseHistory($_POST['customer_id']);
		// 支払い方法の取得
		$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
		
	} else {
		//-- 確認
		if ($_POST["mode"] == "confirm") {
			$objPage->tpl_mainpage = 'customer/edit_confirm.tpl';
			$passlen = strlen($objPage->arrForm['password']);
			$objPage->passlen = lfPassLen($passlen);
			sfprintr($objPage->arrForm);
			sfprintr($objPage->passlen);
			
		}
		//--　編集
		if($_POST["mode"] == "complete") {
			$objPage->tpl_mainpage = 'customer/edit_complete.tpl';
			
			// 現在の会員情報を取得する
			$arrCusSts = $objQuery->getOne("SELECT status FROM dtb_customer WHERE customer_id = ?", array($_POST["customer_id"]));

			// 会員情報が変更されている場合にはシークレット№も更新する。
			if ($arrCusSts != $_POST['status']){
				$secret = sfGetUniqRandomId("r");
				$objPage->arrForm['secret_key'] = $secret;
				array_push($arrRegistColumn, array('column' => 'secret_key', 'convert' => 'n'));
			}
			sfprintr($objPage->arrForm);
			//-- 編集登録
			sfEditCustomerData($objPage->arrForm, $arrRegistColumn);
		}
	}
}

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);



//-------------- function

// 編集登録
function lfRegisDatat($array, $arrRegistColumn) {
	global $objConn;
	
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
		$arrRegist["password"] = crypt($array["password"]);
	} else {
		unset($arrRegist['password']);
	}
	
	$arrRegist["update_date"] = "NOW()";
	$arrRegistMail["mail_flag"] = $array["mail_flag"];
	$arrRegistMail['email'] = $array['email'];
	//-- 編集登録実行
	$objConn->query("BEGIN");
	$objConn->autoExecute("dtb_customer", $arrRegist, "customer_id = '" .addslashes($array["customer_id"]). "'");
	//-- メルマガ登録
	$objConn->autoExecute("dtb_customer_mail", $arrRegistMail, "email = '" .addslashes($array["edit_email"]). "'");
	$objConn->query("COMMIT");
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

	global $objConn;
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
		$sql = "SELECT customer_id FROM dtb_customer WHERE email ILIKE ? escape '#' AND (status = 1 OR status = 2) AND delete = 0 AND customer_id <> ?";
		$checkMail = ereg_replace( "_", "#_", $array["email"]);
		$result = $objConn->getAll($sql, array($checkMail, $array["customer_id"]));
		if (count($result) > 0) {
			$objErr->arrErr["email"] .= "※ すでに登録されているメールアドレスです。";
		} 
	}
	
	$objErr->doFunc(array('メールアドレス(モバイル)', "email_mobile", MTEXT_LEN) ,array("EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
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
	$objErr->doFunc(array("メールマガジン", "mail_flag") ,array("SELECT_CHECK", "NUM_CHECK"));
	$objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("メールマガジン", 'mail_flag'), array("SELECT_CHECK"));
	$objErr->doFunc(array("SHOP用メモ", 'note', LTEXT_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("所持ポイント", "point", TEL_LEN) ,array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	return $objErr->arrErr;
	
}

//購入履歴情報の取得
function lfPurchaseHistory($customer_id){
		global $objQuery;
		global $objPage;
		
		$objPage->tpl_pageno = $_POST['search_pageno'];
		$objPage->edit_customer_id = $customer_id;

		// ページ送りの処理
		$page_max = SEARCH_PMAX;
		//購入履歴の件数取得
		$objPage->tpl_linemax = $objQuery->count("dtb_order","customer_id=?", array($customer_id));
		$linemax = $objPage->tpl_linemax;
		
		// ページ送りの取得
		$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchPage2", NAVI_PMAX);
		$objPage->arrPagenavi = $objNavi->arrPagenavi;
		$objPage->arrPagenavi['mode'] = 'edit';
		$startno = $objNavi->start_row;
		
		// 取得範囲の指定(開始行番号、行数のセット)
		$objQuery->setlimitoffset($page_max, $startno);
		// 表示順序
		$order = "order_id DESC";
		$objQuery->setorder($order);
		//購入履歴情報の取得
		$arrPurchaseHistory = $objQuery->select("*", "dtb_order", "customer_id=?", array($customer_id));
		
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


?>