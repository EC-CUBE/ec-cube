<?php
/**
 * ������Ͽ
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'entry/new.tpl';	// �ᥤ��ƥ�ץ졼��
	}
}

$objPage = new LC_Page();

// �쥤�����ȥǥ���������
$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
