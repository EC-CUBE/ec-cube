<?php
/**
 * ��Х��륵����/�̿����ˤĤ���
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'guide/charge.tpl';	// �ᥤ��ƥ�ץ졼��
		$this->tpl_title = '�̿����ˤĤ���';
	}
}

$objPage = new LC_Page();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
