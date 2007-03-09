<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = URL_DIR.'css/layout/entry/complete.css';	// メインCSSパス
		
		if(CUSTOMER_CONFIRM_MAIL == true) {
			// 仮会員登録完了
			$this->tpl_mainpage = 'entry/complete.tpl';			// メインテンプレート
		} else {
			// 本会員登録完了
			$this->tpl_mainpage = 'regist/complete.tpl';		// メインテンプレート			
		}
		
		$this->tpl_title .= '会員登録(完了ページ)';			//　ページタイトル
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCampaignSess = new SC_CampaignSession();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// キャンペーンからの遷移がチェック
$objPage->is_campaign = $objCampaignSess->getIsCampaign();
$objPage->campaign_dir = $objCampaignSess->getCampaignDir();

$objView->assignobj($objPage);
// フレームを選択(キャンペーンページから遷移なら変更)
$objCampaignSess->pageView($objView);
//----------------------------------------------------------------------------------------------------------------------
?>