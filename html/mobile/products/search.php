<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * ��Х��륵����/���ʸ����ե�����
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'products/search.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_title = '���ʸ���';
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
