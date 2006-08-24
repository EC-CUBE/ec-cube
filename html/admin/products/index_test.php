<?php

require_once("../require.php");
//require_once("./index_csv.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;
	var $arrProducts;
	var $arrPageMax;
	function LC_Page() {
		$this->tpl_mainpage = 'products/index_test.tpl';
		$this->tpl_mainno = 'products';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_subno = 'index';
		$this->tpl_pager = ROOT_DIR . 'data/Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '商品マスタ';

		global $arrPageMax;
		$this->arrPageMax = $arrPageMax;
		global $arrDISP;
		$this->arrDISP = $arrDISP;
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrPRODUCTSTATUS_COLOR;
		$this->arrPRODUCTSTATUS_COLOR = $arrPRODUCTSTATUS_COLOR;

	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

$objDate = new SC_Date();

// 登録・更新検索開始年
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrStartYear = $objDate->getYear();
$objPage->arrStartMonth = $objDate->getMonth();
$objPage->arrStartDay = $objDate->getDay();
// 登録・更新検索終了年
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrEndYear = $objDate->getYear();
$objPage->arrEndMonth = $objDate->getMonth();
$objPage->arrEndDay = $objDate->getDay();

// 認証可否の判定
//$objSess = new SC_Session();
//sfIsSuccess($objSess);

session_start();

//キャンペーンの編集時
if(sfIsInt($_POST['campaign_id']) && $_POST['mode'] == "camp_search") {
	$objQuery = new SC_Query();
	$search_data = $objQuery->get("dtb_campaign", "search_condition", "campaign_id = ? ", array($_POST['campaign_id']));
	$arrSearch = unserialize($search_data);
	foreach ($arrSearch as $key => $val) {
		$_POST[$key] = $val;
	}
}

// POST値の引き継ぎ
$objPage->arrForm = $_POST;

// 検索ワードの引き継ぎ
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key) || ereg("^campaign_", $key)) {
		switch($key) {
			case 'search_product_flag':
			case 'search_status':
				$objPage->arrHidden[$key] = sfMergeParamCheckBoxes($val);
				if(!is_array($val)) {
					$objPage->arrForm[$key] = split("-", $val);
				}
				break;
			default:
				$objPage->arrHidden[$key] = $val;
				break;
		}
	}
}

// ページ送り用
$objPage->arrHidden['search_pageno'] = $_POST['search_pageno'];

// 商品削除
if ($_POST['mode'] == "delete") {
	if($_POST['category_id'] != "") {
		// ランク付きレコードの削除
		$where = "category_id = " . addslashes($_POST['category_id']);
		sfDeleteRankRecord("dtb_products", "product_id", $_POST['product_id'], $where);
	} else {
		sfDeleteRankRecord("dtb_products", "product_id", $_POST['product_id']);
	}
	// 子テーブル(商品規格)の削除
	$objQuery = new SC_Query();
	$objQuery->delete("dtb_products_class", "product_id = ?", array($_POST['product_id']));
	
	// 件数カウントバッチ実行
	sfCategory_Count($objQuery);	
}


if ($_POST['mode'] == "search" || $_POST['mode'] == "csv"  || $_POST['mode'] == "delete" || $_POST['mode'] == "delete_all" || $_POST['mode'] == "camp_search") {
	// 入力文字の強制変換
	lfConvertParam();
	// エラーチェック
	$objPage->arrErr = lfCheckError();

	$where = "delete = 0";

	// 入力エラーなし
	if (count($objPage->arrErr) == 0) {

		foreach ($objPage->arrForm as $key => $val) {
				
			$val = sfManualEscape($val);
			
			if($val == "") {
				continue;
			}
			
			switch ($key) {
				case 'search_product_id':
					$where .= " AND product_id = ?";
					$arrval[] = $val;
					break;
				case 'search_product_class_id':
					$where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_class_id = ?)";
					$arrval[] = $val;
					break;
				case 'search_name':
					$where .= " AND name ILIKE ?";
					$arrval[] = "%$val%";
					break;
				case 'search_category_id':
					list($tmp_where, $tmp_arrval) = sfGetCatWhere($val);
					if($tmp_where != "") {
						$where.= " AND $tmp_where";
						$arrval = array_merge($arrval, $tmp_arrval);
					}
					break;
				case 'search_product_code':
					$where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? GROUP BY product_id)";
					$arrval[] = "%$val%";
					break;
				case 'search_startyear':
					$date = sfGetTimestamp($_POST['search_startyear'], $_POST['search_startmonth'], $_POST['search_startday']);
					$where.= " AND update_date >= ?";
					$arrval[] = $date;
					break;
				case 'search_endyear':
					$date = sfGetTimestamp($_POST['search_endyear'], $_POST['search_endmonth'], $_POST['search_endday']);
					$where.= " AND update_date <= ?";
					$arrval[] = $date;
					break;
				case 'search_product_flag':
					global $arrSTATUS;
					$search_product_flag = sfSearchCheckBoxes($val);
					if($search_product_flag != "") {
						$where.= " AND product_flag LIKE ?";
						$arrval[] = $search_product_flag;					
					}
					break;
				case 'search_status':
					$tmp_where = "";
					foreach ($val as $element){
						if ($element != ""){
							if ($tmp_where == ""){
								$tmp_where.="AND (status LIKE ? ";
							}else{
								$tmp_where.="OR status LIKE ? ";
							}
							$arrval[]=$element;
						}
					}
					if ($tmp_where != ""){
						$tmp_where.=")";
						$where.= "$tmp_where";
					}
					break;
				default:
					break;
			}
		}

		$order = "update_date DESC";
		$objQuery = new SC_Query();
		
		switch($_POST['mode']) {
		case 'csv':
			// オプションの指定
			$option = "ORDER BY $order";
			// CSV出力タイトル行の作成
			$arrOutput = sfSwapArray(sfgetCsvOutput(1, " WHERE csv_id = 1 AND status = 1"));
			
			if (count($arrOutput) <= 0) break;
			
			$arrOutputCols = $arrOutput['col'];
			$arrOutputTitle = $arrOutput['disp_name'];
			
			$head = sfGetCSVList($arrOutputTitle);
			
			$data = lfGetProductsCSV($where, $option, $arrval, $arrOutputCols);

			// CSVを送信する。
			sfCSVDownload($head.$data);
			exit;
			break;
		case 'delete_all':
			// 検索結果をすべて削除
			$where = "product_id IN (SELECT product_id FROM vw_products_nonclass WHERE $where)";
			$sqlval['delete'] = 1;
			$objQuery->update("dtb_products", $sqlval, $where, $arrval);
			break;
		default:
			// 読み込む列とテーブルの指定
			$col = "product_id, name, category_id, main_list_image, status, product_code, price01, price02, stock, stock_unlimited";
			$from = "vw_products_nonclass";

			// 行数の取得
			$linemax = $objQuery->count($from, $where, $arrval);
			$objPage->tpl_linemax = $linemax;				// 何件が該当しました。表示用

			// ページ送りの処理
			if(is_numeric($_POST['search_page_max'])) {	
				$page_max = $_POST['search_page_max'];
			} else {
				$page_max = SEARCH_PMAX;
			}

			// ページ送りの取得
			$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchPage", NAVI_PMAX);
			$startno = $objNavi->start_row;
			$objPage->arrPagenavi = $objNavi->arrPagenavi;
			
			//キャンペーン商品検索時は、全結果の商品IDを変数に格納する
			if($_POST['search_mode'] == 'campaign') {
				$arrRet = $objQuery->select($col, $from, $where, $arrval);
				if(count($arrRet) > 0) {
					$arrRet = sfSwapArray($arrRet);
					$pid = implode("-", $arrRet['product_id']);
					$objPage->arrHidden['campaign_product_id'] = $pid;
				}
			}
			
			// 取得範囲の指定(開始行番号、行数のセット)
			$objQuery->setlimitoffset(10, $startno);
			// 表示順序
			$objQuery->setorder($order);
			
			// 検索結果の取得
			//$objPage->arrProducts = $objQuery->select($col, $from, $where, $arrval);
			/*
			$max = 10;
			for($i = 0; $i < $max; $i++) {
				$objPage->arrProducts[] = "dummy";
			}
			*/
			
			break;
		}
	}
}

// カテゴリの読込
$objPage->arrCatList = sfGetCategoryList();
$objPage->arrCatIDName = lfGetIDName($objPage->arrCatList);


// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

// 取得文字列の変換 
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

// エラーチェック 
// 入力エラーチェック
function lfCheckError() {
	$objErr = new SC_CheckError();
	$objErr->doFunc(array("開始日", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
	$objErr->doFunc(array("終了日", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_DATE"));
	$objErr->doFunc(array("開始日", "終了日", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));
	return $objErr->arrErr;
}

// チェックボックス用WHERE文作成
function lfGetCBWhere($key, $max) {
	$str = "";
	$find = false;
	for ($cnt = 1; $cnt <= $max; $cnt++) {
		if ($_POST[$key . $cnt] == "1") {
			$str.= "1";
			$find = true;
		} else {
			$str.= "_";
		}
	}
	if (!$find) {
		$str = "";
	}
	return $str;
}

// カテゴリIDをキー、カテゴリ名を値にする配列を返す。
function lfGetIDName($arrCatList) {
	$max = count($arrCatList);
	for ($cnt = 0; $cnt < $max; $cnt++ ) {
		$key = $arrCatList[$cnt]['category_id'];
		$val = $arrCatList[$cnt]['category_name'];
		$arrRet[$key] = $val;	
	}
	return $arrRet;
}

?>