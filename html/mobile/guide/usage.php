<?php
/**
 * ��Х��륵����/��������ˡ
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'guide/usage.tpl';	// �ᥤ��ƥ�ץ졼��
		$this->tpl_title = '��������ˡ';
	}
}

$objPage = new LC_Page();

switch (@$_GET['page']) {
case '1':
case '2':
case '3':
case '4':
	$objPage->tpl_mainpage = 'guide/usage' . $_GET['page'] . '.tpl';
	break;
}

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
