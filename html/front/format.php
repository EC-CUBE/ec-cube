<?php

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = '/css/layout/product/detail.css';			// �ᥤ��CSS�ѥ�
		$this->tpl_mainpage = 'front/detail.tpl';					// �ᥤ��ƥ�ץ졼��
		$this->tpl_search_products = 'frontparts/leftnavi.tpl';		// ���ʸ����ƥ�ץ졼��
		$this->tpl_category = 'frontparts/category.tpl';			// ���ƥ���ƥ�ץ졼��
		$this->tpl_leftnavi = 'frontparts/leftnavi.tpl';			// ���ʥ�		
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();


$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>