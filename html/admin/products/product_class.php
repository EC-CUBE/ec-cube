<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrCatList;
	var $arrSRANK;
	var $arrForm;
	var $arrSubList;
	var $arrHidden;
	var $arrTempImage;
	var $arrSaveImage;
	var $tpl_mode;
	var $arrSearchHidden;
	function LC_Page() {
		$this->tpl_mainpage = 'products/product_class.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'product';
		$this->tpl_subtitle = '商品登録';
		global $arrSRANK;
		$this->arrSRANK = $arrSRANK;
		global $arrDISP;
		$this->arrDISP = $arrDISP;
		global $arrCLASS;
		$this->arrCLASS = $arrCLASS;
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		$this->tpl_onload = "";
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// 検索パラメータの引き継ぎ
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrSearchHidden[$key] = $val;	
	}
}

$objPage->tpl_product_id = $_POST['product_id'];
$objPage->tpl_pageno = $_POST['pageno'];

switch($_POST['mode']) {
// 規格削除要求
case 'delete':
	$objQuery = new SC_Query();
	
	$objQuery->setLimitOffset(1);
	$where = "product_id = ? AND NOT (classcategory_id1 = 0 AND classcategory_id2 = 0)";
	$objQuery->setOrder("rank1 DESC, rank2 DESC");
	$arrRet = $objQuery->select("*", "vw_cross_products_class AS crs_prd", $where, array($_POST['product_id']));
	
	if(count($arrRet) > 0) {

		$sqlval['product_id'] = $arrRet[0]['product_id'];
		$sqlval['classcategory_id1'] = '0';
		$sqlval['classcategory_id2'] = '0';
		$sqlval['product_code'] = $arrRet[0]['product_code'];
		$sqlval['stock'] = $arrRet[0]['stock'];
		$sqlval['price01'] = $arrRet[0]['price01'];
		$sqlval['price02'] = $arrRet[0]['price02'];
		$sqlval['creator_id'] = $_SESSION['member_id'];
		$sqlval['create_date'] = "now()";
		$sqlval['update_date'] = "now()";

		$objQuery->begin();
		$where = "product_id = ?";
		$objQuery->delete("dtb_products_class", $where, array($_POST['product_id']));		
		$objQuery->insert("dtb_products_class", $sqlval);
		
		$objQuery->commit();
	}
	
	lfProductClassPage();	// 規格登録ページ	
	break;
	
// 編集要求
case 'pre_edit':
	$objQuery = new SC_Query();
	$where = "product_id = ? AND NOT(classcategory_id1 = 0 AND classcategory_id2 = 0) ";
	$ret = $objQuery->count("dtb_products_class", $where, array($_POST['product_id']));
	
	if($ret > 0) {
		// 規格組み合わせ一覧の取得(DBの値を優先する。)
		$objPage->arrClassCat = lfGetClassCatListEdit($_POST['product_id']);	
	}
	
	lfProductClassPage();	// 規格登録ページ
	break;
// 規格組み合わせ表示
case 'disp':
	$objPage->arrForm['select_class_id1'] = $_POST['select_class_id1'];
	$objPage->arrForm['select_class_id2'] = $_POST['select_class_id2'];

	$objPage->arrErr = lfClassError();
	if (count($objPage->arrErr) == 0) {
		// 規格組み合わせ一覧の取得
		$objPage->arrClassCat = lfGetClassCatListDisp($_POST['select_class_id1'], $_POST['select_class_id2']);
	}
	
	lfProductClassPage();	// 規格登録ページ
	break;
// 規格登録要求
case 'edit':
	// 入力値の変換
	$objPage->arrForm = lfConvertParam($_POST);
	// エラーチェック
	$objPage->arrErr = lfProductClassError($objPage->arrForm);
	
	if(count($objPage->arrErr) == 0) {
		// 確認ページ設定
		$objPage->tpl_mainpage = 'products/product_class_confirm.tpl';
		lfProductConfirmPage(); // 確認ページ表示
	} else {
		// 規格組み合わせ一覧の取得
		$objPage->arrClassCat = lfGetClassCatListDisp($_POST['class_id1'], $_POST['class_id2'], false);
		lfProductClassPage();	// 規格登録ページ
	}
	break;
// 確認ページからの戻り
case 'confirm_return':
	// フォームパラメータの引き継ぎ
	$objPage->arrForm = $_POST;
	// 規格の選択情報は引き継がない。
	$objPage->arrForm['select_class_id1'] = "";
	$objPage->arrForm['select_class_id2'] = "";
	// 規格組み合わせ一覧の取得(デフォルト値は出力しない)
	$objPage->arrClassCat = lfGetClassCatListDisp($_POST['class_id1'], $_POST['class_id2'], false);
	lfProductClassPage();	// 規格登録ページ
	break;
case 'complete':
	// 完了ページ設定	
	$objPage->tpl_mainpage = 'products/product_class_complete.tpl';
	// 商品規格の登録
	lfInsertProductClass($_POST, $_POST['product_id']);
	break;
default:
	lfProductClassPage();	// 規格登録ページ
	break;
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------
/* 規格登録ページ表示用 */
function lfProductClassPage() {
	global $objPage;
	$objPage->arrHidden = $_POST;
	$objPage->arrHidden['select_class_id1'] = "";
	$objPage->arrHidden['select_class_id2'] = "";
	$arrClass = sfGetIDValueList("dtb_class", 'class_id', 'name');
	
	// 規格分類が登録されていない規格は表示しないようにする。
	$arrClassCatCount = sfGetClassCatCount();
	
	foreach($arrClass as $key => $val) {
		if($arrClassCatCount[$key] > 0) {
			$objPage->arrClass[$key] = $arrClass[$key];
		}
	}
	
	// 商品名を取得
	$objQuery = new SC_Query();
	$product_name = $objQuery->getOne("SELECT name FROM dtb_products WHERE product_id = ?", array($_POST['product_id']));
	$objPage->arrForm['product_name'] = $product_name;
}

function lfSetDefaultClassCat($objQuery, $product_id, $max) {
	global $objPage;
	
	// デフォルト値の読込
	$col = "product_code, price01, price02, stock, stock_unlimited";
	$arrRet = $objQuery->select($col, "dtb_products_class", "product_id = ? AND classcategory_id1 = 0 AND classcategory_id2 = 0", array($product_id));;
	
	if(count($arrRet) > 0) {
		$no = 1;
		for($cnt = 0; $cnt < $max; $cnt++) {
			$objPage->arrForm["product_code:".$no] = $arrRet[0]['product_code'];
			$objPage->arrForm['stock:'.$no] = $arrRet[0]['stock'];
			$objPage->arrForm['price01:'.$no] = $arrRet[0]['price01'];
			$objPage->arrForm['price02:'.$no] = $arrRet[0]['price02'];
			$objPage->arrForm['stock_unlimited:'.$no] = $arrRet[0]['stock_unlimited'];
			$no++;
		}
	}
}

/* 規格組み合わせ一覧の取得 */
function lfGetClassCatListDisp($class_id1, $class_id2, $default = true) {
	global $objPage;
	$objQuery = new SC_Query();
		
	if($class_id2 != "") {
		// 規格1と規格2
		$sql = "SELECT * ";
		$sql.= "FROM vw_cross_class AS crs_cls ";
		$sql.= "WHERE class_id1 = ? AND class_id2 = ? ORDER BY rank1 DESC, rank2 DESC;";
		$arrRet = $objQuery->getall($sql, array($class_id1, $class_id2));
	} else {
		// 規格1のみ
		$sql = "SELECT * ";
		$sql.= "FROM vw_cross_class AS crs_cls ";
		$sql.= "WHERE class_id1 = ? AND class_id2 = 0 ORDER BY rank1 DESC;";
		$arrRet = $objQuery->getall($sql, array($class_id1));
		
	}
	
	$max = count($arrRet);
	
	if($default) {
		// デフォルト値を設定
		lfSetDefaultClassCat($objQuery, $_POST['product_id'], $max);
	}
	
	$objPage->arrForm["class_id1"] = $arrRet[0]['class_id1'];
	$objPage->arrForm["class_id2"] = $arrRet[0]['class_id2'];
	$objPage->tpl_onload.= "fnCheckAllStockLimit('$max', '" . DISABLED_RGB . "');";
	
	return $arrRet;
}

/* 規格組み合わせ一覧の取得(編集画面) */
function lfGetClassCatListEdit($product_id) {
	global $objPage;
	// 既存編集の場合
	$objQuery = new SC_Query();
	
	$col = "class_id1, class_id2, name1, name2, rank1, rank2, ";
	$col.= "product_class_id, product_id, T1_classcategory_id AS classcategory_id1, T2_classcategory_id AS classcategory_id2, ";
	$col.= "product_code, stock, stock_unlimited, sale_limit, price01, price02, status";
	
	$sql = "SELECT $col FROM ";
	$sql.= "( ";
	$sql.= "SELECT T1.class_id AS class_id1, T2.class_id AS class_id2, T1.classcategory_id AS T1_classcategory_id, T2.classcategory_id AS T2_classcategory_id, T1.name AS name1, T2.name AS name2, T1.rank AS rank1, T2.rank AS rank2 ";
	$sql.= "FROM dtb_classcategory AS T1, dtb_classcategory AS T2 ";
	$sql.= "WHERE T1.class_id IN (SELECT class_id1 FROM vw_cross_products_class AS crs_prd WHERE product_id = ? GROUP BY class_id1, class_id2) AND T2.class_id IN (SELECT class_id2 FROM vw_cross_products_class AS crs_prd WHERE product_id = ? GROUP BY class_id1, class_id2)";
	$sql.= ") AS T1 ";
			
	$sql.= "LEFT JOIN (SELECT * FROM dtb_products_class WHERE product_id = ?) AS T3 ";
	$sql.= "ON T1_classcategory_id = T3.classcategory_id1 AND T2_classcategory_id = T3.classcategory_id2 ";
	$sql.= "ORDER BY rank1 DESC, rank2 DESC";
	
	$arrList =  $objQuery->getAll($sql, array($product_id, $product_id, $product_id));
	
	$objPage->arrForm["class_id1"] = $arrList[0]['class_id1'];
	$objPage->arrForm["class_id2"] = $arrList[0]['class_id2'];
	
	$max = count($arrList);
	
	// デフォルト値を設定
	lfSetDefaultClassCat($objQuery, $product_id, $max);
	
	$no = 1;
	
	for($cnt = 0; $cnt < $max; $cnt++) {
		$objPage->arrForm["classcategory_id1:".$no] = $arrList[$cnt]['classcategory_id1'];
		$objPage->arrForm["classcategory_id2:".$no] = $arrList[$cnt]['classcategory_id2'];
		if($arrList[$cnt]['product_id'] != "") {
			$objPage->arrForm["product_code:".$no] = $arrList[$cnt]['product_code'];
			$objPage->arrForm['stock:'.$no] = $arrList[$cnt]['stock'];
			$objPage->arrForm['stock_unlimited:'.$no] = $arrList[$cnt]['stock_unlimited'];
			$objPage->arrForm['price01:'.$no] = $arrList[$cnt]['price01'];
			$objPage->arrForm['price02:'.$no] = $arrList[$cnt]['price02'];
			// JavaScript初期化用文字列
			$line.= "'check:".$no."',";			
		}
		$no++;
	}
		
	$line = ereg_replace(",$", "", $line);
	$objPage->tpl_javascript = "list = new Array($line);";
	$color = DISABLED_RGB;
	$objPage->tpl_onload.= "fnListCheck(list); fnCheckAllStockLimit('$max', '$color');";

	return $arrList;
}

/* 規格の登録 */
function lfInsertProductClass($arrList, $product_id) {
	$objQuery = new SC_Query();
	
	$objQuery->begin();
		
	// 既存規格の削除
	$where = "product_id = ?";
	$objQuery->delete("dtb_products_class", $where, array($product_id));
	
	$cnt = 1;
	// すべての規格を登録する。
	while($arrList["classcategory_id1:".$cnt] != "") {
		if($arrList["check:".$cnt] == 1) {
			$sqlval['product_id'] = $product_id;
			$sqlval['classcategory_id1'] = $arrList["classcategory_id1:".$cnt];
			$sqlval['classcategory_id2'] = $arrList["classcategory_id2:".$cnt];
			$sqlval['product_code'] = $arrList["product_code:".$cnt];
			$sqlval['stock'] = $arrList["stock:".$cnt];
			$sqlval['stock_unlimited'] = $arrList["stock_unlimited:".$cnt];
			$sqlval['price01'] = $arrList['price01:'.$cnt];
			$sqlval['price02'] = $arrList['price02:'.$cnt];
			$sqlval['creator_id'] = $_SESSION['member_id'];
			$sqlval['create_date'] = "now()";
			$sqlval['update_date'] = "now()";
			// INSERTの実行
			$objQuery->insert("dtb_products_class", $sqlval);
		}
		$cnt++;
	}
	
	$objQuery->commit();
}

// 規格選択エラーチェック
function lfClassError() {
	$objErr = new SC_CheckError();
	$objErr->doFunc(array("規格1", "select_class_id1"), array("EXIST_CHECK"));
	$objErr->doFunc(array("規格", "select_class_id1", "select_class_id2"), array("TOP_EXIST_CHECK"));
	$objErr->doFunc(array("規格1", "規格2", "select_class_id1", "select_class_id2"), array("DIFFERENT_CHECK"));
	return $objErr->arrErr;
}

/* 取得文字列の変換 */
function lfConvertParam($array) {
	/*
	 *	文字列の変換
	 *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
	 *	C :  「全角ひら仮名」を「全角かた仮名」に変換
	 *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します	
	 *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
	 */

	$no = 1;
	while($array["classcategory_id1:".$no] != "") {
		$arrConvList["product_code:".$no] = "KVa";
		$arrConvList["price01:".$no] = "n";
		$arrConvList["price02:".$no] = "n";
		$arrConvList["stock:".$no] = "n";
		$no++;
	}
	
	// 文字変換
	foreach ($arrConvList as $key => $val) {
		// POSTされてきた値のみ変換する。
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

// 商品規格エラーチェック
function lfProductClassError($array) {
	$objErr = new SC_CheckError($array);
	$no = 1;
		
	while($array["classcategory_id1:".$no] != "") {
		if($array["check:".$no] == 1) {
			$objErr->doFunc(array("商品コード", "product_code:".$no, STEXT_LEN), array("MAX_LENGTH_CHECK"));
			$objErr->doFunc(array(NORMAL_PRICE_TITLE, "price01:".$no, PRICE_LEN), array("ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
			$objErr->doFunc(array(SALE_PRICE_TITLE, "price02:".$no, PRICE_LEN), array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

			if($array["stock_unlimited:".$no] != '1') {
				$objErr->doFunc(array("在庫数", "stock:".$no, AMOUNT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
			}
		}
		if(count($objErr->arrErr) > 0) {
			$objErr->arrErr["error:".$no] = $objErr->arrErr["product_code:".$no];
			$objErr->arrErr["error:".$no].= $objErr->arrErr["price01:".$no];
			$objErr->arrErr["error:".$no].= $objErr->arrErr["price02:".$no];
			$objErr->arrErr["error:".$no].= $objErr->arrErr["stock:".$no];
		}
		$no++;
	}
	return $objErr->arrErr;
}

/* 確認ページ表示用 */
function lfProductConfirmPage() {
	global $objPage;
	$objPage->arrForm['mode'] = 'complete';
	$objPage->arrClass = sfGetIDValueList("dtb_class", 'class_id', 'name');
	$cnt = 0;
	$check = 0;
	$no = 1;
	while($_POST["classcategory_id1:".$no] != "") {
		if($_POST["check:".$no] != "") {
			$check++;
		}
		$no++;
		$cnt++;
	}
	$objPage->tpl_check = $check;
	$objPage->tpl_count = $cnt;
}
?>