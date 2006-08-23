<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/fax/index.css';	// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'fax/index.tpl';			// メインテンプレート
		$this->tpl_page_category = 'fax';				
		$this->tpl_title = 'FAX注文について';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>
