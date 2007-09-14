<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

//---- �ڡ���ɽ�����饹
class LC_Page {
	
	function LC_Page() {
		$this->tpl_mainpage = TEMPLATE_DIR . '/campaign/complete.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();
$objCampaignSess = new SC_CampaignSession();

// �����ڡ���ڡ�����������ܤ�̵������TOP�ڡ�����
if(!$objCampaignSess->getIsCampaign()) {
	header("location: ". URL_DIR);
}

// ���Ͼ�����Ϥ�
$objPage->arrForm = $_POST;
$objPage->campaign_name = $objQuery->get("dtb_campaign", "campaign_name", "campaign_id = ?", array($objCampaignSess->getCampaignId()));
$site_frame = CAMPAIGN_TEMPLATE_PATH . $objCampaignSess->getCampaignDir()  . "/active/site_frame.tpl";

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display($site_frame);
// ���å����γ���
$objCampaignSess->delCampaign();
?>