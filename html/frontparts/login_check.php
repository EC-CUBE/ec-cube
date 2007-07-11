<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

// 不正なURLがPOSTされた場合はエラー表示
if (isset($_POST['url']) && lfIsValidURL() !== true) {
    gfPrintLog('invalid access :login_check.php $POST["url"]=' . $POST['url']);
    sfDispSiteError(PAGE_ERROR);
}

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
	$objFormParam->toLower('login_email');
	$arrErr = $objFormParam->checkError();
	$arrForm =  $objFormParam->getHashArray();
	// クッキー保存判定
	if ($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
		$objCookie->setCookie('login_email', $_POST['login_email']);
	} else {
		$objCookie->setCookie('login_email', '');
	}
	
	if(count($arrErr) == 0) {
		if($objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
			header("Location: " . $_POST['url']);
			exit;
		} else {
			$objQuery = new SC_Query;
			$where = "email ILIKE ? AND status = 1 AND del_flg = 0";
			$ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));
			
			if($ret > 0) {
				sfDispSiteError(TEMP_LOGIN_ERROR);
			} else {
				sfDispSiteError(SITE_LOGIN_ERROR);
			}
		}
	} else {
		// 入力エラーの場合、元のアドレスに戻す。
		header("Location: " . $_POST['url']);
		exit;
	}
	break;
case 'logout':
	// ログイン情報の解放
	$objCustomer->EndSession();
	$mypage_url_search = strpos('.'.$_POST['url'], "mypage");
	//マイページログイン中はログイン画面へ移行
	if ($mypage_url_search == 2){
	header("Location: /mypage/login.php");
	}else{
	header("Location: " . $_POST['url']);	
	}
	exit;
	break;
}

//-----------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("記憶する", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("メールアドレス", "login_email", STEXT_LEN, "a", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("パスワード", "login_pass", STEXT_LEN, "", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}

/* POSTされるURLが自ドメインのものかチェック*/
function lfIsValidURL() {
    $check_url = trim($_POST['url']);
    
    // ドメインチェック
    $pattern = "|^$site_url|";
    if (!preg_match($pattern, $check_url)) {
        return false;
    }

    // CRLFチェック
    $pattern = '/\r|\n|%0D|%0A/';
    if (preg_match_all($pattern, $check_url, $matches)) {
        return false;
    }
    
    return true;
}

?>