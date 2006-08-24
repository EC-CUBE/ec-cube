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
$objView = new SC_View();

session_start();

$max = 10;
for($i = 0; $i < $max; $i++) {
	$objPage->arrProducts[$i]['product_id'] = $i;
}

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