<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/flow/index.css';	// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'flow/index.tpl';			// メインテンプレート
		$this->tpl_page_category = 'flow';				
		$this->tpl_title = 'お買い物の流れ';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>
