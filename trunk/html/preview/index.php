<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
	}
}
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSess = new SC_Session();

if ($_SESSION['preview'] === "ON") {
	// �쥤�����ȥǥ���������
	$objPage = sfGetPageLayout($objPage, true);
	
	// ���̤�ɽ��
	$objView->assignobj($objPage);
	$objView->display(SITE_FRAME);
}

//-----------------------------------------------------------------------------------------------------------------------------------

?>