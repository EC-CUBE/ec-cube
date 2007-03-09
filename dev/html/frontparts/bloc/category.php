<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
class LC_CatPage {
	function LC_CatPage() {
		/** 必ず変更する **/
		$this->tpl_mainpage = BLOC_PATH . 'category.tpl';	// メイン
	}
}

$objSubPage = new LC_CatPage();
$objSubView = new SC_SiteView();

// 選択中のカテゴリIDを判定する
$category_id = sfGetCategoryId($_GET['product_id'], $_GET['category_id']);

// 選択中のカテゴリID
$objSubPage->tpl_category_id = $category_id;
$objSubPage = lfGetCatTree($category_id, true, $objSubPage);

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
// カテゴリツリーの取得
function lfGetCatTree($parent_category_id, $count_check = false, $objSubPage) {
	$objQuery = new SC_Query();
	$col = "*";
	$from = "dtb_category left join dtb_category_total_count using (category_id)";
	// 登録商品数のチェック
	if($count_check) {
		$where = "del_flg = 0 AND product_count > 0";
	} else {
		$where = "del_flg = 0";
	}
	$objQuery->setoption("ORDER BY rank DESC");
	$arrRet = $objQuery->select($col, $from, $where);
	
	$arrParentID = sfGetParents($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $parent_category_id);
	$arrBrothersID = sfGetBrothersArray($arrRet, 'parent_category_id', 'category_id', $arrParentID);
	$arrChildrenID = sfGetUnderChildrenArray($arrRet, 'parent_category_id', 'category_id', $parent_category_id);
	
	$objSubPage->root_parent_id = $arrParentID[0];
	
	$arrDispID = array_merge($arrBrothersID, $arrChildrenID);
	
	foreach($arrRet as $key => $array) {
		foreach($arrDispID as $val) {
			if($array['category_id'] == $val) {
				$arrRet[$key]['display'] = 1;
				break;
			}
		}
	}
	
	$objSubPage->arrTree = $arrRet;
	return $objSubPage;
}
?>