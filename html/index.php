<?php

require_once("./require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_css = '/css/layout/index.css';						// �ᥤ��CSS�ѥ�
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = ROOT_DIR . 'html/user_data/templates/top.tpl';		// �ᥤ��ƥ�ץ졼��
	}
}

$objPage = new LC_Page();
$conn = new SC_DBConn();

// �����Ⱦ�����������
//$objSiteInfo = new SC_SiteInfo();
//$objPage->arrSiteInfo = $objSiteInfo->data;

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "index.php");

$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------

?>