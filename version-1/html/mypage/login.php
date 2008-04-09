<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = USER_PATH . 'templates/mypage/login.tpl';
		$this->tpl_title = 'MYページ(ログイン)';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();

// クッキー管理クラス
$objCookie = new SC_Cookie(COOKIE_EXPIRE);

//SSLURL判定
if (SSLURL_CHECK == 1){
	$ssl_url= sfRmDupSlash(SSL_URL.$_SERVER['REQUEST_URI']);
	if (!ereg("^https://", $non_ssl_url)){
		sfDispSiteError(URL_ERROR);
	}
}

// ログイン判定
if($objCustomer->isLoginSuccess()) {
	header("location: ./index.php");
} else {
	// クッキー判定
	$objPage->tpl_login_email = $objCookie->getCookie('login_email');
		if($objPage->tpl_login_email != "") {
		$objPage->tpl_login_memory = "1";
	}
	
	// POSTされてきたIDがある場合は優先する。
	if($_POST['mypage_login_email'] != "") {
		$objPage->tpl_login_email = $_POST['mypage_login_email'];
	}
}

$objView->assignobj($objPage);				//$objpage内の全てのテンプレート変数をsmartyに格納
$objView->display(SITE_FRAME);				//パスとテンプレート変数の呼び出し、実行

//-------------------------------------------------------------------------------------------------------------------------
											
//エラーチェック

function lfErrorCheck() {
	$objErr = new SC_CheckError();
			$objErr->doFunc(array("メールアドレス", "login_email", STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","EMAIL_CHECK","MAX_LENGTH_CHECK"));
			$objErr->dofunc(array("パスワード", "login_password", PASSWORD_LEN2), array("EXIST_CHECK","ALNUM_CHECK"));
	return $objErr->arrErr;
}									
											
?> 