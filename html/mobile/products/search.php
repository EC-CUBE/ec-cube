<?php
/**
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
$objView = new SC_SiteView();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
