<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = '/css/layout/entry/complete.css';	// �ᥤ��CSS�ѥ�
		$this->tpl_mainpage = 'entry/complete.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_title .= '�����Ͽ(��λ�ڡ���)';			//���ڡ��������ȥ�
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

// �����Ⱦ�������
$objSiteInfo = new SC_SiteInfo();
$arrInfo = $objSiteInfo->data;
$objPage->arrInfo = $arrInfo;

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//----------------------------------------------------------------------------------------------------------------------
?>