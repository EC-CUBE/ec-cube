<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css[1] = URL_DIR.'css/layout/contact/index.css';	// メインCSSパス
		$this->tpl_mainpage = 'contact/complete.tpl';
		$this->tpl_title .= 'お問い合わせ(完了ページ)';
		$this->tpl_mainno = 'contact';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCampaignSess = new SC_CampaignSession();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// キャンペーンからの遷移かチェック
$objPage->is_campaign = $objCampaignSess->getIsCampaign();
$objPage->campaign_dir = $objCampaignSess->getCampaignDir();

$objView->assignobj($objPage);
// フレームを選択(キャンペーンページから遷移なら変更)
$objCampaignSess->pageView($objView);

?>