<?php
/**
 * ��Х��륵����/�ץ饤�Х����ݥꥷ��
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'guide/privacy.tpl';	// �ᥤ��ƥ�ץ졼��
		$this->tpl_title = '�ץ饤�Х����ݥꥷ��';
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
