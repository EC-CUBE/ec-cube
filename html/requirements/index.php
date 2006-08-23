<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/movement/index.css';		// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'movement/index.tpl';			// メインテンプレート
		$this->tpl_title = "動作環境について";
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query(); 

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
