<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

session_start();

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = ROOT_DIR . USER_DIR . 'templates/mypage/delivery_addr.tpl';
		$this->tpl_title = "新しいお届け先の追加･変更";
		global $arrPref;
		$this->arrPref = $arrPref;
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
$objConn = new SC_DBConn();
$ParentPage = MYPAGE_DELIVADDR_URL;

// GETでページを指定されている場合には指定ページに戻す
if (isset($_GET['page'])) {
	$ParentPage = $_GET['page'];
}
$objPage->ParentPage = $ParentPage;

//ログイン判定
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR);
}

if ($_POST['mode'] == ""){
	$_SESSION['other_deliv_id'] = $_GET['other_deliv_id'];
}

if ($_GET['other_deliv_id'] != ""){
	//不正アクセス判定
	$flag = $objQuery->count("dtb_other_deliv", "customer_id=? AND other_deliv_id=?", array($objCustomer->getValue("customer_id"), $_SESSION['other_deliv_id']));
	if (!$objCustomer->isLoginSuccess() || $flag == 0){
		sfDispSiteError(CUSTOMER_ERROR);
	}
}

//別のお届け先ＤＢ登録用カラム配列
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
							 array(  "column" => "tel01",		"convert" => "n" ),
							 array(  "column" => "tel02",		"convert" => "n" ),
							 array(  "column" => "tel03",		"convert" => "n" ),
						);

switch ($_POST['mode']){
	case 'edit':
		$_POST = lfConvertParam($_POST,$arrRegistColumn);
		$objPage->arrErr =lfErrorCheck($_POST);
		if ($objPage->arrErr){
			foreach ($_POST as $key => $val){
				$objPage->$key = $val;
			}
		}else{
			//別のお届け先登録数の取得
			$deliv_count = $objQuery->count("dtb_other_deliv", "customer_id=?", array($objCustomer->getValue('customer_id')));
			if ($deliv_count < DELIV_ADDR_MAX){
				lfRegistData($_POST,$arrRegistColumn);
			}
			$objPage->tpl_onload = "fnUpdateParent('".$_POST['ParentPage']."'); window.close();";
		}
		break;
}

if ($_GET['other_deliv_id'] != ""){
	//別のお届け先情報取得
	$arrOtherDeliv = $objQuery->select("*", "dtb_other_deliv", "other_deliv_id=? ", array($_SESSION['other_deliv_id']));
	$objPage->arrOtherDeliv = $arrOtherDeliv[0];
}

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);

//-------------------------------------------------------------------------------------------------------------

/* エラーチェック */
function lfErrorCheck() {
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("フリガナ（姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("フリガナ（名）", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("ご住所（1）", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("ご住所（2）", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("お電話番号1", 'tel01'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("お電話番号2", 'tel02'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("お電話番号3", 'tel03'), array("EXIST_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
	return $objErr->arrErr;
	
}

/* 登録実行 */
function lfRegistData($array, $arrRegistColumn) {
	global $objConn;
	global $objCustomer;
	
	foreach ($arrRegistColumn as $data) {
		if (strlen($array[ $data["column"] ]) > 0) {
			$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
		}
	}
	
	$arrRegist['customer_id'] = $objCustomer->getvalue('customer_id');
	
	//-- 編集登録実行
	$objConn->query("BEGIN");
	if ($array['other_deliv_id'] != ""){
	$objConn->autoExecute("dtb_other_deliv", $arrRegist, "other_deliv_id='" .addslashes($array["other_deliv_id"]). "'");
	}else{
	$objConn->autoExecute("dtb_other_deliv", $arrRegist);
	}
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
?>