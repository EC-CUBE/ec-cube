<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/tokado/freedom.css';		// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'tokado/freedom.tpl';			// メインテンプレート
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query(); 

$objView->assignobj($objPage);
$objView->display('tokado/freedom.tpl');

//-----------------------------------------------------------------------------------------------------------------------------------
