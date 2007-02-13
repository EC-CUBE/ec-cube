<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/tradelaw.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'tradelaw';
		$this->tpl_mainno = 'basis';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrTAXRULE;
		$this->arrTAXRULE = $arrTAXRULE;
		$this->tpl_subtitle = '特定商取引法';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// 認証可否の判定
sfIsSuccess($objSess);

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

$cnt = $objQuery->count("dtb_baseinfo");

if ($cnt > 0) {
	$objPage->tpl_mode = "update";
} else {
	$objPage->tpl_mode = "insert";
}

if($_POST['mode'] != "") {
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	
	if(count($objPage->arrErr) == 0) {
		switch($_POST['mode']) {
		case 'update':
			lfUpdateData(); // 既存編集
			break;
		case 'insert':
			lfInsertData(); // 新規作成
			break;
		default:
			break;
		}
		// 再表示
		//sfReload();
		$objPage->tpl_onload = "window.alert('特定商取引法の登録が完了しました。');";
	}
} else {
	$arrCol = $objFormParam->getKeyList(); // キー名一覧を取得
	$col	= sfGetCommaList($arrCol);
	$arrRet = $objQuery->select($col, "dtb_baseinfo");
	// DB値の取得
	$objFormParam->setParam($arrRet[0]);
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//--------------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("販売業者", "law_company", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("運営責任者", "law_manager", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("郵便番号1", "law_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("郵便番号2", "law_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("都道府県", "law_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("住所1", "law_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("住所2", "law_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("電話番号1", "law_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号2", "law_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("電話番号3", "law_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX番号1", "law_fax01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX番号2", "law_fax02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX番号3", "law_fax03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("メールアドレス", "law_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"));
	$objFormParam->addParam("URL", "law_url", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "URL_CHECK"));
	$objFormParam->addParam("必要料金", "law_term01", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("注文方法", "law_term02", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("支払方法", "law_term03", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("支払期限", "law_term04", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("引き渡し時期", "law_term05", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("返品・交換について", "law_term06", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}

function lfUpdateData() {
	global $objFormParam;
	// 入力データを渡す。
	$sqlval = $objFormParam->getHashArray();
	$sqlval['update_date'] = 'Now()';
	$objQuery = new SC_Query();
	// UPDATEの実行
	$ret = $objQuery->update("dtb_baseinfo", $sqlval);
}

function lfInsertData() {
	global $objFormParam;
	// 入力データを渡す。
	$sqlval = $objFormParam->getHashArray();
	$sqlval['update_date'] = 'Now()';
	$objQuery = new SC_Query();
	// INSERTの実行
	$ret = $objQuery->insert("dtb_baseinfo", $sqlval);
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	// 電話番号チェック
	$objErr->doFunc(array("TEL", "law_tel01", "law_tel02", "law_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("FAX", "law_fax01", "law_fax02", "law_fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("郵便番号", "law_zip01", "law_zip02"), array("ALL_EXIST_CHECK"));
	
	return $objErr->arrErr;
}

?>