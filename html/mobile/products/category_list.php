<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * モバイルサイト/カテゴリー一覧
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_mainpage = 'products/category_list.tpl';			// メインテンプレート
		$this->tpl_title = 'カテゴリ一覧ページ';
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// カテゴリー情報を取得する。
lfGetCategories(@$_GET['category_id'], true, $objPage);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

/**
 * 選択されたカテゴリーとその子カテゴリーの情報を取得し、
 * ページオブジェクトに格納する。
 *
 * @param string $category_id カテゴリーID
 * @param boolean $count_check 有効な商品がないカテゴリーを除くかどうか
 * @param object &$objPage ページオブジェクト
 * @return void
 */
function lfGetCategories($category_id, $count_check = false, &$objPage) {
	// カテゴリーの正しいIDを取得する。
	$category_id = sfGetCategoryId('', $category_id);
	if ($category_id == 0) {
		sfDispSiteError(CATEGORY_NOT_FOUND, "", false, "", true);
	}

	$arrCategory = null;	// 選択されたカテゴリー
	$arrChildren = array();	// 子カテゴリー

	$arrAll = sfGetCatTree($category_id, $count_check);
	foreach ($arrAll as $category) {
		// 選択されたカテゴリーの場合
		if ($category['category_id'] == $category_id) {
			$arrCategory = $category;
			continue;
		}

		// 関係のないカテゴリーはスキップする。
		if ($category['parent_category_id'] != $category_id) {
			continue;
		}

		// 子カテゴリーの場合は、孫カテゴリーが存在するかどうかを調べる。
		$arrGrandchildrenID = sfGetUnderChildrenArray($arrAll, 'parent_category_id', 'category_id', $category['category_id']);
		$category['has_children'] = count($arrGrandchildrenID) > 0;
		$arrChildren[] = $category;
	}

	if (!isset($arrCategory)) {
		sfDispSiteError(CATEGORY_NOT_FOUND, "", false, "", true);
	}

	// 子カテゴリーの商品数を合計する。
	$children_product_count = 0;
	foreach ($arrChildren as $category) {
		$children_product_count += $category['product_count'];
	}

	// 選択されたカテゴリーに直属の商品がある場合は、子カテゴリーの先頭に追加する。
	if ($arrCategory['product_count'] > $children_product_count) {
		$arrCategory['product_count'] -= $children_product_count;	// 子カテゴリーの商品数を除く。
		$arrCategory['has_children'] = false;	// 商品一覧ページに遷移させるため。
		array_unshift($arrChildren, $arrCategory);
	}

	// 結果を格納する。
	$objPage->arrCategory = $arrCategory;
	$objPage->arrChildren = $arrChildren;
}
?>
