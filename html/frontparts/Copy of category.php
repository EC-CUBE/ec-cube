<?php

class LC_CatPage {
	function LC_CatPage() {
		/** 必ず変更する **/
		$this->tpl_mainpage = 'frontparts/category.tpl';	// メイン
	}
}

$objSubPage = new LC_CatPage();
$objSubView = new SC_SiteView();

// 選択中のカテゴリIDを判定する
$category_id = sfGetCategoryId($_GET['product_id'], $_GET['category_id']);

if($category_id != "") {
	// 選択中のカテゴリIDの親カテゴリIDを取得する。
	$objQuery = new SC_Query();
	$parent_category_id = $objQuery->get("dtb_category", "parent_category_id", "category_id = ?", array($category_id));
}

// 選択中のカテゴリの階層を判定する
$level = lfGetCategoryLevel($category_id);

// カテゴリ一覧の取得
$objSubPage->arrCategory = lfGetCategoryList($category_id, $level);

$objSubPage->tpl_category_id = $category_id;
$objSubPage->tpl_parent_category_id = $parent_category_id;

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
/* 選択中のカテゴリのレベルを取得する */
function lfGetCategoryLevel($category_id) {
	$objQuery = new SC_Query();
	$where = "category_id = ?";
	$ret = $objQuery->get("dtb_category", "level", $where, array($category_id));
	return $ret;
}

/* カテゴリの一覧を取得する */
function lfGetCategoryList($category_id, $level) {
	$objQuery = new SC_Query();

	switch($level) {
	case '':
		break;
	case '1':
		$objQuery->setorder("rank DESC");
		$where = "parent_category_id = ? AND product_count > 0";
		$arrRet = $objQuery->select("*", "vw_category_count", $where, array($category_id));
		break;
	default:
		$arrRet = lfGetCatParentsList($category_id);
		break;
	}
	return $arrRet;
}

/* 縦系列の親カテゴリ一覧を取得 */
function lfGetCatParentsList($category_id) {
	$objQuery = new SC_Query();
	// 商品が属するカテゴリIDを縦に取得
	$arrCatID = sfGetParents($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);
	// 所属する親カテゴリ一覧
	$count = count($arrCatID);
	if ($count > 0) {
		for($cnt = 0; $cnt < $count; $cnt++) {
			if ($where == "") {
				$where = "(parent_category_id = ?";
			} else {
				$where.= " OR parent_category_id = ?";
			}
			$arrVal[] = $arrCatID[$cnt];
		}
		$where.= ")";
	}
	
	if($where != "") {
		$where.= " AND product_count > 0 AND level >= 2";
	} else {
		$where = "product_count > 0 AND level >= 2";
	}
	
	$objQuery->setorder("rank DESC");
	$arrRet = $objQuery->select("*", "vw_category_count", $where, $arrVal);
	return $arrRet;
}
?>