<?php

require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = 'regist/complete.tpl';
		$this->tpl_css = '/css/layout/regist/complete.css';
		$this->tpl_title = '�����Ͽ(��λ�ڡ���)';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteInfo = new SC_SiteInfo();
$objPage->arrInfo = $objSiteInfo->data;

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>