<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

//---- ページ表示クラス
class LC_Page {
	
	function LC_Page() {
		$this->tpl_mainpage = TEMPLATE_DIR . '/campaign/complete.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();
$objCampaignSess = new SC_CampaignSession();

// キャンペーンページからの遷移で無い場合はTOPページへ
if(!$objCampaignSess->getIsCampaign()) {
	header("location: ". URL_DIR);
}

// 入力情報を渡す
$objPage->arrForm = $_POST;
$objPage->campaign_name = $objQuery->get("dtb_campaign", "campaign_name", "campaign_id = ?", array($objCampaignSess->getCampaignId()));
$site_frame = CAMPAIGN_TEMPLATE_PATH . $objCampaignSess->getCampaignDir()  . "/active/site_frame.tpl";

//----　ページ表示
$objView->assignobj($objPage);
$objView->display($site_frame);
// セッションの開放
$objCampaignSess->delCampaign();
?>