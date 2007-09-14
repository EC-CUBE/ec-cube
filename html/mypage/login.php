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
		$this->tpl_title = 'MY�ڡ���(������)';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();

// ���å����������饹
$objCookie = new SC_Cookie(COOKIE_EXPIRE);

//SSLURLȽ��
if (SSLURL_CHECK == 1){
	$ssl_url= sfRmDupSlash(SSL_URL.$_SERVER['REQUEST_URI']);
	if (!ereg("^https://", $non_ssl_url)){
		sfDispSiteError(URL_ERROR);
	}
}

// ������Ƚ��
if($objCustomer->isLoginSuccess()) {
	header("location: ./index.php");
} else {
	// ���å���Ƚ��
	$objPage->tpl_login_email = $objCookie->getCookie('login_email');
		if($objPage->tpl_login_email != "") {
		$objPage->tpl_login_memory = "1";
	}
	
	// POST����Ƥ���ID���������ͥ�褹�롣
	if($_POST['mypage_login_email'] != "") {
		$objPage->tpl_login_email = $_POST['mypage_login_email'];
	}
}

$objView->assignobj($objPage);				//$objpage������ƤΥƥ�ץ졼���ѿ���smarty�˳�Ǽ
$objView->display(SITE_FRAME);				//�ѥ��ȥƥ�ץ졼���ѿ��θƤӽФ����¹�

//-------------------------------------------------------------------------------------------------------------------------
											
//���顼�����å�

function lfErrorCheck() {
	$objErr = new SC_CheckError();
			$objErr->doFunc(array("�᡼�륢�ɥ쥹", "login_email", STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","EMAIL_CHECK","MAX_LENGTH_CHECK"));
			$objErr->dofunc(array("�ѥ����", "login_password", PASSWORD_LEN2), array("EXIST_CHECK","ALNUM_CHECK"));
	return $objErr->arrErr;
}									
											
?> 