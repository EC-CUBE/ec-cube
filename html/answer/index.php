<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_css = '/css/layout/answer/index.css';	// �ᥤ��CSS�ѥ�
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'answer/index.tpl';			// �ᥤ��ƥ�ץ졼��
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$objView->assignobj($objPage);
$objView->display('answer/index.tpl');
//-----------------------------------------------------------------------------------------------------------------------------------
?>
