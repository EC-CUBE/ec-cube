<?php
/**
 * ���ޥ���Ͽ��������ȥåץڡ���
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'magazine/index.tpl';
		$this->tpl_title .= '���ޥ���Ͽ�����';
	}
}

$objPage = new LC_Page();
$objPage->arrForm = $_POST;

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
