<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/answer/index.css';	// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'answer/index.tpl';			// メインテンプレート
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$objView->assignobj($objPage);
$objView->display('answer/index.tpl');
//-----------------------------------------------------------------------------------------------------------------------------------
?>
