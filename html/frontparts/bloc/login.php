<?php
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
class LC_LoginPage {
	var $tpl_login_email;
	function LC_LoginPage() {
		/** 必ず変更する **/
		$this->tpl_mainpage = ROOT_DIR . BLOC_DIR.'login.tpl';	// メイン
		$this->tpl_login = false;
		$this->tpl_disable_logout = false;
	}
}

$objSubPage = new LC_LoginPage();
$objCustomer = new SC_Customer();
// クッキー管理クラス
$objCookie = new SC_Cookie(COOKIE_EXPIRE);

// ログイン判定
if($objCustomer->isLoginSuccess()) {
	$objSubPage->tpl_login = true;
	$objSubPage->tpl_user_point = $objCustomer->getValue('point');
	$objSubPage->tpl_name1 = $objCustomer->getValue('name01');
	$objSubPage->tpl_name2 = $objCustomer->getValue('name02');
} else {
	// クッキー判定
	$objSubPage->tpl_login_email = $objCookie->getCookie('login_email');
	if($objSubPage->tpl_login_email != "") {
		$objSubPage->tpl_login_memory = "1";
	}
	
	// POSTされてきたIDがある場合は優先する。
	if($_POST['login_email'] != "") {
		$objSubPage->tpl_login_email = $_POST['login_email'];
	}
}

$objSubPage->tpl_disable_logout = lfCheckDisableLogout();
$objSubView = new SC_SiteView();
$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
function lfCheckDisableLogout() {
	global $arrDISABLE_LOGOUT;
	
	$nowpage = $_SERVER['PHP_SELF'];
	
	foreach($arrDISABLE_LOGOUT as $val) {
		if($nowpage == $val) {
			return true;
		}
 	}
	return false;
}
?>