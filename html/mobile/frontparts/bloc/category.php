<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 * モバイルサイト/メインカテゴリー
 */

class LC_CatPage {
	function LC_CatPage() {
		/** 必ず変更する **/
		$this->tpl_mainpage = 'frontparts/bloc/category.tpl';	// メインテンプレート
	}
}

$objSubPage = new LC_CatPage();
$objSubView = new SC_MobileView();

$objSubPage = lfGetMainCat(true, $objSubPage);

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);

//-----------------------------------------------------------------------------------------------------------------------------------

// メインカテゴリーの取得
function lfGetMainCat($count_check = false, $objSubPage) {
	$objQuery = new SC_Query();
	$col = "*";
	$from = "dtb_category left join dtb_category_total_count using (category_id)";
	// メインカテゴリーとその直下のカテゴリーを取得する。
	$where = 'level <= 2 AND del_flg = 0';
	// 登録商品数のチェック
	if($count_check) {
		$where .= " AND product_count > 0";
	}
	$objQuery->setoption("ORDER BY rank DESC");
	$arrRet = $objQuery->select($col, $from, $where);

	// メインカテゴリーを抽出する。
	$arrMainCat = array();
	foreach ($arrRet as $cat) {
		if ($cat['level'] != 1) {
			continue;
		}

		// 子カテゴリーを持つかどうかを調べる。
		$arrChildrenID = sfGetUnderChildrenArray($arrRet, 'parent_category_id', 'category_id', $cat['category_id']);
		$cat['has_children'] = count($arrChildrenID) > 0;
		$arrMainCat[] = $cat;
	}

	$objSubPage->arrCat = $arrMainCat;
	return $objSubPage;
}
?>
