<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "include/file_manager.inc");

class LC_Page {
	
	function LC_Page() {
		$this->tpl_mainpage = 'contents/campaign_create_tag.tpl';
		$this->tpl_mainno = 'create';
		$this->tpl_subtitle = '商品設定';
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');		
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// 認証可否の判定
sfIsSuccess($objSess);

switch($_POST['mode']) {

// 商品検索
case  'search':
	// POST値の引き継ぎ
	$objPage->arrForm = $_POST;
	// 入力文字の強制変換
	lfConvertParam();
	
	$where = "del_flg = 0";
	
	// where句生成
	foreach ($objPage->arrForm as $key => $val) {
		if($val == "") {
			continue;
		}
		
		switch ($key) {
			case 'search_name':
				$where .= " AND name ILIKE ?";
				$arrval[] = "%$val%";
				break;
			case 'search_category_id':
				// 子カテゴリIDの取得
				$arrRet = sfGetChildsID("dtb_category", "parent_category_id", "category_id", $val);
				$tmp_where = "";
				foreach ($arrRet as $val) {
					if($tmp_where == "") {
						$tmp_where.= " AND ( category_id = ?";
					} else {
						$tmp_where.= " OR category_id = ?";
					}
					$arrval[] = $val;
				}
				$where.= $tmp_where . " )";
				break;
			case 'search_product_id':
				if($val != "") {
					$where .= " AND product_id = ?";
					if(!sfIsInt($val)) $val = 0;
					$arrval[] = $val;
				}
				break;
			default:
				break;
		}
	}
	
	$order = "update_date DESC, product_id DESC ";
	
	// 読み込む列とテーブルの指定
	$col = "product_id, name, category_id, main_list_image, status, product_code, price01, stock, stock_unlimited";
	$from = "vw_products_nonclass AS noncls ";

	// 行数の取得
	$linemax = $objQuery->count("dtb_products", $where, $arrval);
	$objPage->tpl_linemax = $linemax;				// 何件が該当しました。表示用
	
	// ページ送りの処理
	if(is_numeric($_POST['search_page_max'])) {	
		$page_max = $_POST['search_page_max'];
	} else {
		$page_max = SEARCH_PMAX;
	}
	
	// ページ送りの取得
	$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchOnlyPage", NAVI_PMAX);
	$objPage->tpl_strnavi = $objNavi->strnavi;		// 表示文字列
	$startno = $objNavi->start_row;
	
	// 取得範囲の指定(開始行番号、行数のセット)
	if(DB_TYPE != "mysql") $objQuery->setlimitoffset($page_max, $startno);
	// 表示順序
	$objQuery->setorder($order);
	
	// viewも絞込みをかける(mysql用)
	sfViewWhere("&&noncls_where&&", $where, $arrval, $objQuery->order . " " .  $objQuery->setlimitoffset($page_max, $startno, true));
	
	// 検索結果の取得
	$objPage->arrProducts = $objQuery->select($col, $from, $where, $arrval);
	break;
	
// タグ表示
case 'view':

	// タグを生成
	$create_tag = lfGetCreateTag($_POST['product_id']);
	$objPage->tpl_create_tag = $create_tag;
	break;
}

// カテゴリ取得
$objPage->arrCatList = sfGetCategoryList();

//----　ページ表示
$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

/* 取得文字列の変換 */
function lfConvertParam() {
	global $objPage;
	/*
	 *	文字列の変換
	 *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
	 *	C :  「全角ひら仮名」を「全角かた仮名」に変換
	 *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します	
	 *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
	 */
	$arrConvList['search_name'] = "KVa";
	$arrConvList['search_product_code'] = "KVa";
	
	// 文字変換
	foreach ($arrConvList as $key => $val) {
		// POSTされてきた値のみ変換する。
		if(isset($objPage->arrForm[$key])) {
			$objPage->arrForm[$key] = mb_convert_kana($objPage->arrForm[$key] ,$val);
		}
	}
}

/* タグを生成 */
function lfGetCreateTag($product_id) {
	
	global $objQuery;
	
	// 書き込みタグ
	$read_file = file_get_contents(CAMPAIGN_BLOC_PATH . "cart_tag.tpl");
	$read_file = ereg_replace("<{assign_product_id}>", $product_id, $read_file);
	// カートタグ保存用ファイル作成
	$create_tag .= "<!--{* ▼商品ID$product_id *}-->\n";
	$create_tag .= $read_file;
	
	return $create_tag;	
}
?>