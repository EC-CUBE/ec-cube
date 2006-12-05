<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
/* 商品の新規追加 */
function lfRegistProduct($objQuery) {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();
	
	// dtb_products以外に登録される値を除外する。
	foreach($arrRet as $key => $val) {
		switch($key) {
		case 'product_code':
		case 'price01':
		case 'price02':
		case 'point_rate':
		case 'stock':
		case 'product_class_id':
		case 'recommend_product_id1':
		case 'recommend_product_id2':
		case 'recommend_product_id3':
		case 'recommend_product_id4':
		case 'recommend_comment1':
		case 'recommend_comment2':
		case 'recommend_comment3':
		case 'recommend_comment4':
			break;
		default:
			if(!ereg("^dummy", $key)) {
				$sqlval[$key] = $val;
			}
			break;
		}
	}
	
	$sqlval['update_date'] = "Now()";
	$sqlval['creator_id'] = $_SESSION['member_id'];
	if($sqlval['sale_limit'] == "") {
		$sqlval['sale_unlimited'] = '1';
	} else {
		$sqlval['sale_unlimited'] = '0';		
	}
	
	if($sqlval['status'] == "") {
		$sqlval['status'] = 2;
	}
			
	if($arrRet['product_id'] != "" && $arrRet['product_class_id'] != "") {
		// UPDATEの実行
		$where = "product_id = ?";
		$objQuery->update("dtb_products", $sqlval, $where, array($sqlval['product_id']));
	} else {
		// 新規登録
		// INSERTの実行
		$sqlval['create_date'] = "now()";
		$objQuery->insert("dtb_products", $sqlval);
		$product_id = $objQuery->nextval("dtb_products", "product_id");
		$sqlval['product_id'] = $product_id;
	}
	
	// 規格登録
	lfRegistProductClass($objQuery, $arrRet, $sqlval['product_id'], $arrRet['product_class_id']);
	
	// おすすめ商品登録
	$objQuery->delete("dtb_recommend_products", "product_id = ?", array($sqlval['product_id']));
	for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
		$keyname = "recommend_product_id" . $i;
		$comment_key = "recommend_comment" . $i;
		if($arrRet[$keyname] != "") {
			$arrProduct = $objQuery->select("product_id", "dtb_products", "product_id = ?", array($arrRet[$keyname]));
			if($arrProduct[0]['product_id'] != "") {
				$arrval['product_id'] = $sqlval['product_id'];
				$arrval['recommend_product_id'] = $arrProduct[0]['product_id'];
				$arrval['comment'] = $arrRet[$comment_key];
				$arrval['update_date'] = "Now()";
				$arrval['create_date'] = "Now()";
				$arrval['creator_id'] = $_SESSION['member_id'];
				$arrval['rank'] = RECOMMEND_PRODUCT_MAX - $i + 1;
				$objQuery->insert("dtb_recommend_products", $arrval);
			}
		}
	}
}

/* 規格の登録 */
function lfRegistProductClass($objQuery, $arrList, $product_id, $product_class_id) {
	$sqlval['product_code'] = $arrList["product_code"];
	$sqlval['stock'] = $arrList["stock"];
	if($sqlval['stock'] == "") {
		$sqlval['stock_unlimited'] = '1';
	} else {
		$sqlval['stock_unlimited'] = '0';		
	}
	$sqlval['price01'] = $arrList['price01'];
	$sqlval['price02'] = $arrList['price02'];
	$sqlval['creator_id'] = $_SESSION['member_id'];
	if($sqlval['member_id'] == "") {
		$sqlval['creator_id'] = '0';
	}
		
	if($product_class_id == "") {
		// 新規登録
		$where = "product_id = ?";
		// 念のために既存の規格を削除
		$objQuery->delete("dtb_products_class", $where, array($product_id));
		$sqlval['product_id'] = $product_id;
		$sqlval['classcategory_id1'] = '0';
		$sqlval['classcategory_id2'] = '0';
		$sqlval['create_date'] = "now()";
		$objQuery->insert("dtb_products_class", $sqlval);
	} else {
		// 既存編集
		$where = "product_id = ? AND product_class_id = ?";
		$objQuery->update("dtb_products_class", $sqlval, $where, array($product_id, $product_class_id));	
	}
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError(false);
	
	if(count($objErr->arrErr) == 0) {
		$objQuery = new SC_Query();
		// 商品ID、規格IDの存在チェック
		if($arrRet['product_id'] != "") {
			$count = $objQuery->count("dtb_products", "product_id = ?", array($arrRet['product_id']));
			if($count == 0) {
				$objErr->arrErr['product_id'] = "※ 指定の商品IDは、登録されていません。";
			}
		}
				
		if($arrRet['product_class_id'] != "") {
			$count = 0;
			if($arrRet['product_id'] != "") {
				$count = $objQuery->count("dtb_products_class", "product_id = ? AND product_class_id = ?", array($arrRet['product_id'], $arrRet['product_class_id']));
			}
			if($count == 0) {
				$objErr->arrErr['product_class_id'] = "※ 指定の規格IDは、登録されていません。";
			}
		}
	}
	return $objErr->arrErr;
}
?>