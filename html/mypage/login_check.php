<?php

require_once("../require.php");

$objCustomer = new SC_Customer();
// クッキー管理クラス
$objCookie = new SC_Cookie(COOKIE_EXPIRE);
// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

switch($_POST['mode']) {
case 'login':
	$objFormParam->toLower('mypage_login_email');
	$arrErr = $objFormParam->checkError();
	$arrForm =  $objFormParam->getHashArray();
	
	// クッキー保存判定
	if ($arrForm['mypage_login_memory'] == "1" && $arrForm['mypage_login_email'] != "") {
		$objCookie->setCookie('login_email', $_POST['mypage_login_email']);
	} else {
		$objCookie->setCookie('login_email', '');
	}
	if ($count == 0){
		if($objCustomer->getCustomerDataFromEmailPass($arrForm['mypage_login_pass'], $arrForm['mypage_login_email'])) {
			header("Location: ./index.php");
			exit;
		} else {
			$objQuery = new SC_Query;
			$where = "email = ? AND status = 1 AND delete = 0";
			$ret = $objQuery->count("dtb_customer", $where, array($arrForm['mypage_login_email']));
			
			if($ret > 0) {
				sfDispSiteError(TEMP_LOGIN_ERROR);
			} else {
				sfDispSiteError(SITE_LOGIN_ERROR);
			}
		}
	}
	
	break;

}

//-----------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("記憶する", "mypage_login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("メールアドレス", "mypage_login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("パスワード", "mypage_login_pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}
?>