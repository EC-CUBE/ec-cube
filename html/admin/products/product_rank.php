<?php
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'products/product_rank.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'product_rank';
		$this->tpl_subtitle = '商品並び替え';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

$objPage->tpl_pageno = $_POST['pageno'];

// 通常時は親カテゴリを0に設定する。
$objPage->arrForm['parent_category_id'] = $_POST['parent_category_id'];

switch($_POST['mode']) {
case 'up':
	$where = "category_id = " . addslashes($_POST['parent_category_id']);
	sfRankUp("dtb_products", "product_id", $_POST['product_id'], $where);
	break;
case 'down':
	$where = "category_id = " . addslashes($_POST['parent_category_id']);
	sfRankDown("dtb_products", "product_id", $_POST['product_id'], $where);
	break;
case 'move':
	$key = "pos-".$_POST['product_id'];
	$input_pos = mb_convert_kana($_POST[$key], "n");
	if(sfIsInt($input_pos)) {
		$where = "category_id = " . addslashes($_POST['parent_category_id']);
		sfMoveRank("dtb_products", "product_id", $_POST['product_id'], $input_pos, $where);
	}
	break;
case 'tree':
	// カテゴリの切替は、ページ番号をクリアする。
	$objPage->tpl_pageno = "";
	break;
default:
	break;
}

$objPage->arrTree = sfGetCatTree($_POST['parent_category_id']);
$objPage->arrProductsList = lfGetProduct($_POST['parent_category_id']);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
/* 商品読み込み */
function lfGetProduct($category_id) {
	global $objPage;
	
	$objQuery = new SC_Query();
	$col = "product_id, name, main_list_image, rank, product_code";
	$table = "vw_products_nonclass AS noncls ";
	$where = "del_flg = 0 AND category_id = ?";
	
	// 行数の取得
	$linemax = $objQuery->count($table, $where, array($category_id));
	// 順位、該当件数表示用
	$objPage->tpl_linemax = $linemax;
	
	$objNavi = new SC_PageNavi($objPage->tpl_pageno, $linemax, SEARCH_PMAX, "fnNaviPage", NAVI_PMAX);
	$startno = $objNavi->start_row;
	$objPage->tpl_strnavi = $objNavi->strnavi;		// Navi表示文字列
	$objPage->tpl_pagemax = $objNavi->max_page;		// ページ最大数（「上へ下へ」表示判定用）
	$objPage->tpl_disppage = $objNavi->now_page;	// 表示ページ番号（「上へ下へ」表示判定用）
			
	// 取得範囲の指定(開始行番号、行数のセット)
	$objQuery->setlimitoffset(SEARCH_PMAX, $startno);
	
	$objQuery->setorder("rank DESC");
	$arrRet = $objQuery->select($col, $table, $where, array($category_id));
	return $arrRet;
}

?>