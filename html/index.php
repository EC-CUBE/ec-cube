<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("./require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_css = '/css/layout/index.css';						// �ᥤ��CSS�ѥ�
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = HTML_PATH . "user_data/templates/top.tpl";		// �ᥤ��ƥ�ץ졼��
	}
}

$objPage = new LC_Page();
$conn = new SC_DBConn();
$objCookie = new SC_Cookie(COOKIE_EXPIRE);
$objCookie->setCookie('login_email', '');

define(DOMAIN_NAME, "eccube.net");
sfprintr(DOMAIN_NAME);
sfprintr($objCookie);
sfprintr($_COOKIE);

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "index.php");

$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------

?>