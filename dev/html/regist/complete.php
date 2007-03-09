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
		$this->tpl_title = '会員登録(完了ページ)';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();
$objCampaignSess = new SC_CampaignSession();

// キャンペーンからの登録の場合の処理
if($_GET["cp"] != "") {
	$arrCampaign= $objQuery->select("directory_name", "dtb_campaign", "campaign_id = ?", array($_GET["cp"]));
	// キャンペーンディレクトリ名を保持
	$dir_name = $arrCampaign[0]['directory_name'];
} else {
	$dir_name = "";
}

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
// フレームを選択(キャンペーンページから遷移なら変更)
if($objPage->dir_name != "") {
	$objView->display(CAMPAIGN_TEMPLATE_PATH . $dir_name  . "/active/site_frame.tpl");
	$objCampaignSess->delCampaign();
} else {
	$objView->display(SITE_FRAME);	
}
?>