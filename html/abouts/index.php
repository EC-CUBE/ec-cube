<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'abouts/index.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_page_category = 'abouts';	
		$this->tpl_title = '�������ȤˤĤ���';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>