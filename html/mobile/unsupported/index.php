<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_css = '';			// �ᥤ��CSS�ѥ�
		/** ɬ�����ꤹ�� **/
		// �ᥤ��ƥ�ץ졼��
		$this->tpl_mainpage = 'unsupported/index.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
