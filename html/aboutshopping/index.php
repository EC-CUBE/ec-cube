<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_css = '/css/layout/aboutshopping/index.css';		// �ᥤ��CSS�ѥ�
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'aboutshopping/index.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_page_category = 'aboutshopping';					// ���ʥӻ�����
		$this->tpl_title = '���㤤ʪ�ˤĤ���';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>
