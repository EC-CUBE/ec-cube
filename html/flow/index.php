<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_css = '/css/layout/flow/index.css';	// �ᥤ��CSS�ѥ�
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'flow/index.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_page_category = 'flow';				
		$this->tpl_title = '���㤤ʪ��ή��';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>
