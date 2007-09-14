<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = 'regist/complete.tpl';
		$this->tpl_css = URL_DIR.'css/layout/regist/complete.css';
		$this->tpl_title = '�����Ͽ(��λ�ڡ���)';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();
$objCampaignSess = new SC_CampaignSession();

// �����ڡ��󤫤����Ͽ�ξ��ν���
if($_GET["cp"] != "") {
	$arrCampaign= $objQuery->select("directory_name", "dtb_campaign", "campaign_id = ?", array($_GET["cp"]));
	// �����ڡ���ǥ��쥯�ȥ�̾���ݻ�
	$dir_name = $arrCampaign[0]['directory_name'];
} else {
	$dir_name = "";
}

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
if($objPage->dir_name != "") {
	$objView->display(CAMPAIGN_TEMPLATE_PATH . $dir_name  . "/active/site_frame.tpl");
	$objCampaignSess->delCampaign();
} else {
	$objView->display(SITE_FRAME);	
}
?>