<?php

require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	var $tpl_login_email;
	function LC_Page() {
		$this->tpl_mainpage = 'shopping/index.tpl';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrSex;
		$this->arrSex = $arrSex;
		global $arrJob;
		$this->arrJob = $arrJob;
		$this->tpl_onload = 'fnCheckInputDeliv();';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCustomer = new SC_Customer();
$objCookie = new SC_Cookie();
$objFormParam = new SC_FormParam();			// フォーム用
lfInitParam();								// パラメータ情報の初期化
$objFormParam->setParam($_POST);			// POST値の取得

// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

$objPage->tpl_uniqid = $uniqid;

// ログインチェック
if($objCustomer->isLoginSuccess()) {
	// すでにログインされている場合は、お届け先設定画面に転送
	header("Location: ./deliv.php");
	exit;
}

switch($_POST['mode']) {
case 'nonmember_confirm':
	$objPage = lfSetNonMember($objPage);
	// ※breakなし
case 'confirm':
	// 入力値の変換
	$objFormParam->convParam();
	$objFormParam->toLower('order_mail');
	$objFormParam->toLower('order_mail_check');
	
	sfprintr($objPage->arrErr);
	
	$objPage->arrErr = lfCheckError();
	// 入力エラーなし
	if(count($objPage->arrErr) == 0) {
		// DBへのデータ登録
		lfRegistData($uniqid);
		// 正常に登録されたことを記録しておく
		$objSiteSess->setRegistFlag();
		// お支払い方法選択ページへ移動
		header("Location: " . URL_SHOP_PAYMENT);
		exit;		
	}
	
	break;
// 前のページに戻る
case 'return':
	// 確認ページへ移動
	header("Location: " . URL_CART_TOP);
	exit;
	break;
case 'nonmember':
	$objPage = lfSetNonMember($objPage);
	// ※breakなし
default:
	if($_GET['from'] == 'nonmember') {
		$objPage = lfSetNonMember($objPage);
	}
	// ユーザユニークIDの取得
	$uniqid = $objSiteSess->getUniqId();
	$objQuery = new SC_Query();
	$where = "order_temp_id = ?";
	$arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($uniqid));
	// DB値の取得
	$objFormParam->setParam($arrRet[0]);
	$objFormParam->setValue('order_email_check', $arrRet[0]['order_email']);
	$objFormParam->setDBDate($arrRet[0]['order_birth']);
	break;
}

// クッキー判定
$objPage->tpl_login_email = $objCookie->getCookie('login_email');
if($objPage->tpl_login_email != "") {
	$objPage->tpl_login_memory = "1";
}

// 選択用日付の取得
$objDate = new SC_Date(START_BIRTH_YEAR);
$objPage->arrYear = $objDate->getYear('', 1950);	//　日付プルダウン設定
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

if($objPage->year == '') {
	$objPage->year = '----';
}

// 入力値の取得
$objPage->arrForm = $objFormParam->getFormParamList();

if($objPage->arrForm['year']['value'] == ""){
	$objPage->arrForm['year']['value'] = '----';	
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//--------------------------------------------------------------------------------------------------------------------------
/* 非会員入力ページのセット */
function lfSetNonMember($objPage) {
	$objPage->tpl_mainpage = 'shopping/nonmember_input.tpl';
	$objPage->tpl_css = array();
	$objPage->tpl_css[] = '/css/layout/login/nonmember.css';
	return $objPage;
}

/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("お名前（姓）", "order_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お名前（名）", "order_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ（セイ）", "order_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ（メイ）", "order_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("郵便番号1", "order_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("郵便番号2", "order_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("都道府県", "order_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("住所1", "order_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("住所2", "order_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("電話番号1", "order_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号2", "order_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号3", "order_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX番号1", "order_fax01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX番号2", "order_fax02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX番号3", "order_fax03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("メールアドレス", "order_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"));
	$objFormParam->addParam("メールアドレス（確認）", "order_email_check", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"), "", false);
	$objFormParam->addParam("年", "year", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
	$objFormParam->addParam("月", "month", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
	$objFormParam->addParam("日", "day", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
	$objFormParam->addParam("性別", "order_sex", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("職業", "order_job", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("別のお届け先", "deliv_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("お名前（姓）", "deliv_name01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お名前（名）", "deliv_name02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ（セイ）", "deliv_kana01", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ（メイ）", "deliv_kana02", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("郵便番号1", "deliv_zip01", ZIP01_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("郵便番号2", "deliv_zip02", ZIP02_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("都道府県", "deliv_pref", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("住所1", "deliv_addr01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("住所2", "deliv_addr02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("電話番号1", "deliv_tel01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号2", "deliv_tel02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号3", "deliv_tel03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("メールマガジン", "mail_flag", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
}

/* DBへデータの登録 */
function lfRegistData($uniqid) {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();
	$sqlval = $objFormParam->getDbArray();
	// 登録データの作成
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['order_birth'] = sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);
	$sqlval['update_date'] = 'Now()';
	$sqlval['customer_id'] = '0';
	
	// 既存データのチェック
	$objQuery = new SC_Query();
	$where = "order_temp_id = ?";
	$cnt = $objQuery->count("dtb_order_temp", $where, array($uniqid));
	// 既存データがない場合
	if ($cnt == 0) {
		$objQuery->insert("dtb_order_temp", $sqlval);
	} else {
		$objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
	}
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
		
	// 別のお届け先チェック
	if($_POST['deliv_check'] == "1") { 
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
		$objErr->doFunc(array("メールマガジン", "mail_flag"), array("EXIST_CHECK"));
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
	
	// すでにメルマガテーブルに会員としてメールアドレスが登録されている場合
	if(sfCheckCustomerMailMaga($arrRet['order_email'])) {
		$objErr->arrErr['order_email'] = "このメールアドレスはすでに登録されています。<br />";
	}
		
	return $objErr->arrErr;
}
?>