<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/abouts/index.css';		// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'abouts/index.tpl';			// メインテンプレート
		$this->tpl_page_category = 'abouts';	
		$this->tpl_title = '当サイトについて';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// サイト情報を取得
$objSiteInfo = new SC_SiteInfo();
$arrInfo = $objSiteInfo->data;

// 都道府県名を変換
global $arrPref;
$arrInfo['pref'] = $arrPref[$arrInfo['pref']];

$objPage->arrInfo = $arrInfo;

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>