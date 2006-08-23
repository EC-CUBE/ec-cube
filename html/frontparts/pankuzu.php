<?php

class LC_PankuzuPage {
	function LC_PankuzuPage() {
		/** 必ず変更する **/
		$this->tpl_mainpage = 'frontparts/pankuzu.tpl';	// メイン
	}
}

$objSubPage = new LC_PankuzuPage();
$objSubView = new SC_SiteView();

// 選択中のカテゴリIDを判定する
$category_id = sfGetCategoryId($_GET['product_id'], $_GET['category_id']);
// パンクズカテゴリ名の取得
list($objSubPage->arrCatID, $objSubPage->arrCatName) = lfGetCatName($category_id);

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
/* 縦系列の親カテゴリを取得 */
function lfGetCatName($category_id) {
	if($category_id != 0) {
		$objQuery = new SC_Query();
		$arrCatID = sfGetParents($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);
		// 縦系列の親カテゴリ名を取得
		$arrList = sfGetParentsCol($objQuery, "dtb_category", "category_id", "category_name", $arrCatID);
		$count = count($arrList);
		for($cnt = 0; $cnt < $count; $cnt++) {
			$arrCatName[] = $arrList[$cnt]['category_name'];
		}
	}
	return array($arrCatID, $arrCatName);
}
?>