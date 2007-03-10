<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'order/edit.tpl';
		$this->tpl_subnavi = 'order/subnavi.tpl';
		$this->tpl_mainno = 'order';		
		$this->tpl_subno = 'index';
		$this->tpl_subtitle = '受注管理';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrORDERSTATUS;
		$this->arrORDERSTATUS = $arrORDERSTATUS;
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objSiteInfo = new SC_SiteInfo();
$arrInfo = $objSiteInfo->data;

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();

// 認証可否の判定
sfIsSuccess($objSess);

// 検索パラメータの引き継ぎ
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrSearchHidden[$key] = $val;
	}
}

// 表示モード判定
if(sfIsInt($_GET['order_id'])) {
	$objPage->disp_mode = true;
	$order_id = $_GET['order_id'];
} else {
	$order_id = $_POST['order_id'];
}
$objPage->tpl_order_id = $order_id;

// DBから受注情報を読み込む
lfGetOrderData($order_id);

switch($_POST['mode']) {
case 'pre_edit':
case 'order_id':
	break;
case 'edit':
	// POST情報で上書き
	$objFormParam->setParam($_POST);
	
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfCheek($arrInfo);
		if(count($objPage->arrErr) == 0) {
			lfRegistData($_POST['order_id']);
			// DBから受注情報を再読込
			lfGetOrderData($order_id);
			$objPage->tpl_onload = "window.alert('受注履歴を編集しました。');";
		}
	}
	break;
// 再計算
case 'cheek':
	// POST情報で上書き
	$objFormParam->setParam($_POST);
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	if(count($objPage->arrErr) == 0) {
		$objPage->arrErr = lfCheek($arrInfo);
	}
	break;
default:
	break;
}

// 支払い方法の取得
$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
// 配送時間の取得
$arrRet = sfGetDelivTime($objFormParam->getValue('payment_id'));
$objPage->arrDelivTime = sfArrKeyValue($arrRet, 'time_id', 'deliv_time');

$objPage->arrForm = $objFormParam->getFormParamList();

$objPage->arrInfo = $arrInfo;

$objView->assignobj($objPage);
// 表示モード判定
if(!$objPage->disp_mode) {
	$objView->display(MAIN_FRAME);
} else {
	$objView->display('order/disp.tpl');
}
//-----------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	// 配送先情報
	$objFormParam->addParam("お名前1", "deliv_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お名前2", "deliv_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ1", "deliv_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("フリガナ2", "deliv_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("郵便番号1", "deliv_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("郵便番号2", "deliv_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("都道府県", "deliv_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("住所1", "deliv_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("住所2", "deliv_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("電話番号1", "deliv_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号2", "deliv_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号3", "deliv_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	// 受注商品情報
	$objFormParam->addParam("値引き", "discount", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("送料", "deliv_fee", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("手数料", "charge", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("利用ポイント", "use_point", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("お支払い方法", "payment_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("配送時間ID", "deliv_time_id", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("対応状況", "status", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("配達日", "deliv_date", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	$objFormParam->addParam("お支払方法名称", "payment_method");
	$objFormParam->addParam("配送時間", "deliv_time");
	
	// 受注詳細情報
	$objFormParam->addParam("単価", "price", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("個数", "quantity", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("商品ID", "product_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
	$objFormParam->addParam("ポイント付与率", "point_rate");
	$objFormParam->addParam("商品コード", "product_code");
	$objFormParam->addParam("商品名", "product_name");
	$objFormParam->addParam("規格1", "classcategory_id1");
	$objFormParam->addParam("規格2", "classcategory_id2");
	$objFormParam->addParam("規格名1", "classcategory_name1");
	$objFormParam->addParam("規格名2", "classcategory_name2");
	$objFormParam->addParam("メモ", "note", MTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	// DB読込用
	$objFormParam->addParam("小計", "subtotal");
	$objFormParam->addParam("合計", "total");
	$objFormParam->addParam("支払い合計", "payment_total");
	$objFormParam->addParam("加算ポイント", "add_point");
	$objFormParam->addParam("お誕生日ポイント", "birth_point");
	$objFormParam->addParam("消費税合計", "tax");
	$objFormParam->addParam("最終保持ポイント", "total_point");
	$objFormParam->addParam("顧客ID", "customer_id");
	$objFormParam->addParam("現在のポイント", "point");
}

function lfGetOrderData($order_id) {
	global $objFormParam;
	global $objPage;
	if(sfIsInt($order_id)) {
		// DBから受注情報を読み込む
		$objQuery = new SC_Query();
		$where = "order_id = ?";
		$arrRet = $objQuery->select("*", "dtb_order", $where, array($order_id));
		$objFormParam->setParam($arrRet[0]);
		list($point, $total_point) = sfGetCustomerPoint($order_id, $arrRet[0]['use_point'], $arrRet[0]['add_point']);
		$objFormParam->setValue('total_point', $total_point);
		$objFormParam->setValue('point', $point);
		$objPage->arrDisp = $arrRet[0];
		// 受注詳細データの取得
		$arrRet = lfGetOrderDetail($order_id);
		$arrRet = sfSwapArray($arrRet);
		$objPage->arrDisp = array_merge($objPage->arrDisp, $arrRet);
		$objFormParam->setParam($arrRet);
		
		// その他支払い情報を表示
		if($objPage->arrDisp["memo02"] != "") $objPage->arrDisp["payment_info"] = unserialize($objPage->arrDisp["memo02"]);
		if($objPage->arrDisp["memo01"] == PAYMENT_CREDIT_ID){
			$objPage->arrDisp["payment_type"] = "クレジット決済";
		}elseif($objPage->arrDisp["memo01"] == PAYMENT_CONVENIENCE_ID){
			$objPage->arrDisp["payment_type"] = "コンビニ決済";
		}else{
			$objPage->arrDisp["payment_type"] = "お支払い";
		}
	}
}

// 受注詳細データの取得
function lfGetOrderDetail($order_id) {
	$objQuery = new SC_Query();
	$col = "product_id, classcategory_id1, classcategory_id2, product_code, product_name, classcategory_name1, classcategory_name2, price, quantity, point_rate";
	$where = "order_id = ?";
	$objQuery->setorder("classcategory_id1, classcategory_id2");
	$arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
	return $arrRet;
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	return $objErr->arrErr;
}

/* 計算処理 */
function lfCheek($arrInfo) {
	global $objFormParam;
		
	$arrVal = $objFormParam->getHashArray();
			
	// 商品の種類数
	$max = count($arrVal['quantity']);
	$subtotal = 0;
	$totalpoint = 0;
	$totaltax = 0;
	for($i = 0; $i < $max; $i++) {
		// 小計の計算
		$subtotal += sfPreTax($arrVal['price'][$i], $arrInfo['tax'], $arrInfo['tax_rule']) * $arrVal['quantity'][$i];
		// 小計の計算
		$totaltax += sfTax($arrVal['price'][$i], $arrInfo['tax'], $arrInfo['tax_rule']) * $arrVal['quantity'][$i];
		// 加算ポイントの計算
		$totalpoint += sfPrePoint($arrVal['price'][$i], $arrVal['point_rate'][$i]) * $arrVal['quantity'][$i];
	}
	
	// 消費税
	$arrVal['tax'] = $totaltax;	
	// 小計
	$arrVal['subtotal'] = $subtotal;
	// 合計
	$arrVal['total'] = $subtotal - $arrVal['discount'] + $arrVal['deliv_fee'] + $arrVal['charge'];
	// お支払い合計
	$arrVal['payment_total'] = $arrVal['total'] - ($arrVal['use_point'] * POINT_VALUE);
	
	// 加算ポイント
	$arrVal['add_point'] = sfGetAddPoint($totalpoint, $arrVal['use_point'], $arrInfo);
		
	list($arrVal['point'], $arrVal['total_point']) = sfGetCustomerPoint($_POST['order_id'], $arrVal['use_point'], $arrVal['add_point']);
		
	if($arrVal['total'] < 0) {
		$arrErr['total'] = '合計額がマイナス表示にならないように調整して下さい。<br />';
	}
	
	if($arrVal['payment_total'] < 0) {
		$arrErr['payment_total'] = 'お支払い合計額がマイナス表示にならないように調整して下さい。<br />';
	}

	if($arrVal['total_point'] < 0) {
		$arrErr['total_point'] = '最終保持ポイントがマイナス表示にならないように調整して下さい。<br />';
	}

	$objFormParam->setParam($arrVal);
	return $arrErr;
}

/* DB登録処理 */
function lfRegistData($order_id) {
	global $objFormParam;
	$objQuery = new SC_Query();
	
	$objQuery->begin();

	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	
	foreach($arrRet as $key => $val) {
		// 配列は登録しない
		if(!is_array($val)) {
			$sqlval[$key] = $val;
		}
	}
	
	unset($sqlval['total_point']);
	unset($sqlval['point']);
			
	$where = "order_id = ?";
	
	// 受注ステータスの判定
	if ($sqlval['status'] == ODERSTATUS_COMMIT) {
		// 受注テーブルの発送済み日を更新する
		$addcol['commit_date'] = "Now()";
	}
	
	// 受注テーブルの更新
	$objQuery->update("dtb_order", $sqlval, $where, array($order_id), $addcol);

	$sql = "";
	$sql .= " UPDATE";
	$sql .= "     dtb_order";
	$sql .= " SET";
	$sql .= "     payment_method = (SELECT payment_method FROM dtb_payment WHERE payment_id = ?)";
	$sql .= "     ,deliv_time = (SELECT deliv_time FROM dtb_delivtime WHERE time_id = ? AND deliv_id = (SELECT deliv_id FROM dtb_payment WHERE payment_id = ? ))";
	$sql .= " WHERE order_id = ?";
	
	if ($arrRet['deliv_time_id'] == "") {
		$deliv_time_id = 0;
	}else{
		$deliv_time_id = $arrRet['deliv_time_id'];
	}
	$arrUpdData = array($arrRet['payment_id'], $deliv_time_id, $arrRet['payment_id'], $order_id);
	$objQuery->query($sql, $arrUpdData);

	// 受注詳細データの更新
	$arrDetail = $objFormParam->getSwapArray(array("product_id", "product_code", "product_name", "price", "quantity", "point_rate", "classcategory_id1", "classcategory_id2", "classcategory_name1", "classcategory_name2"));
	$objQuery->delete("dtb_order_detail", $where, array($order_id));
	
	$max = count($arrDetail);
	for($i = 0; $i < $max; $i++) {
		$sqlval = array();
		$sqlval['order_id'] = $order_id;
		$sqlval['product_id']  = $arrDetail[$i]['product_id'];
		$sqlval['product_code']  = $arrDetail[$i]['product_code'];
		$sqlval['product_name']  = $arrDetail[$i]['product_name'];
		$sqlval['price']  = $arrDetail[$i]['price'];
		$sqlval['quantity']  = $arrDetail[$i]['quantity'];
		$sqlval['point_rate']  = $arrDetail[$i]['point_rate'];
		$sqlval['classcategory_id1'] = $arrDetail[$i]['classcategory_id1'];
		$sqlval['classcategory_id2'] = $arrDetail[$i]['classcategory_id2'];
		$sqlval['classcategory_name1'] = $arrDetail[$i]['classcategory_name1'];
		$sqlval['classcategory_name2'] = $arrDetail[$i]['classcategory_name2'];		
		$objQuery->insert("dtb_order_detail", $sqlval);
	}
	$objQuery->commit();
}
?>