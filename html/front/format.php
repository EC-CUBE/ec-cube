<?php

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = '/css/layout/product/detail.css';			// メインCSSパス
		$this->tpl_mainpage = 'front/detail.tpl';					// メインテンプレート
		$this->tpl_search_products = 'frontparts/leftnavi.tpl';		// 商品検索テンプレート
		$this->tpl_category = 'frontparts/category.tpl';			// カテゴリテンプレート
		$this->tpl_leftnavi = 'frontparts/leftnavi.tpl';			// 左ナビ		
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();


$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>