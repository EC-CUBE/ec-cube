<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/aboutshopping/index.css';		// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'aboutshopping/index.tpl';			// メインテンプレート
		$this->tpl_page_category = 'aboutshopping';					// 左ナビ指定用
		$this->tpl_title = 'お買い物について';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>
