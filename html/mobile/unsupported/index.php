<?php
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
$objView = new SC_SiteView();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
