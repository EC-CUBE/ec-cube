<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
class LC_SearchProductsPage {
	function LC_SearchProductsPage() {
		/** 必ず変更する **/
		$this->tpl_mainpage = BLOC_PATH . 'search_products.tpl';	// メイン
	}
}

$objSubPage = new LC_SearchProductsPage();
$arrSearch = array();	// 検索項目表示用

// 選択中のカテゴリIDを判定する
$objSubPage->category_id = sfGetCategoryId($_GET['product_id'], $_GET['category_id']);
// カテゴリ検索用選択リスト
$arrRet = sfGetCategoryList('', true, '　');

if(is_array($arrRet)) {
	// 文字サイズを制限する
	foreach($arrRet as $key => $val) {
		$arrRet[$key] = sfCutString($val, SEARCH_CATEGORY_LEN);
	}
}
$objSubPage->arrCatList = $arrRet;

$objSubView = new SC_SiteView();
$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
?>