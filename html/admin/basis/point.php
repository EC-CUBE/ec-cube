<?php

require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/point.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'point';
		$this->tpl_mainno = 'basis';
		$this->tpl_subtitle = 'ポイント設定';
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
	$objPage->arrErr = $objFormParam->checkError();
	
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
		$objPage->tpl_onload = "window.alert('ポイント設定が完了しました。');";
	}
} else {
	$arrCol = $objFormParam->getKeyList(); // キー名一覧を取得
	$col	= sfGetCommaList($arrCol);
	$arrRet = $objQuery->select($col, "dtb_baseinfo");
	// POST値の取得
	$objFormParam->setParam($arrRet[0]);
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//--------------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("ポイント付与率", "point_rate", PERCENTAGE_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("会員登録時付与ポイント", "welcome_point", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
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

