<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css[1] = URL_DIR.'css/layout/contact/index.css';	// �ᥤ��CSS�ѥ�
		$this->tpl_mainpage = 'contact/complete.tpl';
		$this->tpl_title .= '���䤤��碌(��λ�ڡ���)';
		$this->tpl_mainno = 'contact';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>