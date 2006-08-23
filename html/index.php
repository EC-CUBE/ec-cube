<?php

require_once("./require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_css = '/css/layout/index.css';						// メインCSSパス
		/** 必ず変更する **/
		$this->tpl_mainpage = ROOT_DIR . 'html/user_data/templates/top.tpl';		// メインテンプレート
	}
}

$objPage = new LC_Page();
$conn = new SC_DBConn();
$objSiteInfo = new SC_SiteInfo();


// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "index.php");

$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------

?>