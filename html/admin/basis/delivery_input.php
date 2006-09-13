<?php

require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/delivery_input.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'delivery';
		$this->tpl_mainno = 'basis';
		global $arrPref;
		$this->arrPref = $arrPref;
		$this->tpl_subtitle = '配送業者設定';
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

switch($_POST['mode']) {
case 'edit':
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError();
	if(count($objPage->arrErr) == 0) {
		$objPage->tpl_deliv_id = lfRegistData();
		$objPage->tpl_onload = "window.alert('配送業者設定が完了しました。');";
	}
	break;
case 'pre_edit':
	if($_POST['deliv_id'] != "") {
		lfGetDelivData($_POST['deliv_id']);
		$objPage->tpl_deliv_id = $_POST['deliv_id'];
	}
	break;
default:
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//--------------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("配送業者名", "name", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("伝票No.確認URL", "confirm_url", STEXT_LEN, "n", array("URL_CHECK", "MAX_LENGTH_CHECK"), "http://");
	
	for($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
		$objFormParam->addParam("配送時間$cnt", "deliv_time$cnt", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	}
	
	if(INPUT_DELIV_FEE) {
		for($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
			$objFormParam->addParam("配送料金$cnt", "fee$cnt", PRICE_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
		}
	}
}

/* DBに登録する */
function lfRegistData() {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();
	$objQuery = new SC_Query();
	$objQuery->begin();

	// 入力データを渡す。
	$sqlval['name'] = $arrRet['name'];
	$sqlval['service_name'] = $arrRet['name'];
	$sqlval['confirm_url'] = $arrRet['confirm_url'];
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['update_date'] = 'Now()';
	
	if($_POST['deliv_id'] != "") {
		$deliv_id = $_POST['deliv_id'];
		$where = "deliv_id = ?";
		$objQuery->update("dtb_deliv", $sqlval, $where, array($deliv_id));
		$objQuery->delete("dtb_delivfee", $where, array($deliv_id));
		$objQuery->delete("dtb_delivtime", $where, array($deliv_id));
	} else {
		// 登録する配送業者IDの取得
		$deliv_id = $objQuery->nextval('dtb_deliv', 'deliv_id');
		$sqlval['deliv_id'] = $deliv_id;
		$sqlval['rank'] = $objQuery->max("dtb_deliv", "rank") + 1;
		// INSERTの実行
		$objQuery->insert("dtb_deliv", $sqlval);
	}
	
	$sqlval = array();
	// 配送時間の設定
	for($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
		$keyname = "deliv_time$cnt";
		if($arrRet[$keyname] != "") {
			$sqlval['deliv_id'] = $deliv_id;
			$sqlval['deliv_time'] = $arrRet[$keyname];
			// INSERTの実行
			$objQuery->insert("dtb_delivtime", $sqlval);
		}
	}
	
	if(INPUT_DELIV_FEE) {
		$sqlval = array();
		// 配送料金の設定
		for($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
			$keyname = "fee$cnt";
			if($arrRet[$keyname] != "") {
				$sqlval['deliv_id'] = $deliv_id;
				$sqlval['fee'] = $arrRet[$keyname];
				$sqlval['pref'] = $cnt;
				// INSERTの実行
				$objQuery->insert("dtb_delivfee", $sqlval);
			}
		}
	}
	$objQuery->commit();
	return $deliv_id;
}

/* 配送業者情報の取得 */
function lfGetDelivData($deliv_id) {
	global $objFormParam;
	$objQuery = new SC_Query();
	// 配送業者一覧の取得
	$col = "deliv_id, name, service_name, confirm_url";
	$where = "deliv_id = ? ORDER BY deliv_id";
	$table = "dtb_deliv";
	$arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
	$objFormParam->setParam($arrRet[0]);
	// 配送時間の取得
	$col = "deliv_time";
	$where = "deliv_id = ?";
	$table = "dtb_delivtime";
	$arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
	$objQuery->getlastquery();
	$objFormParam->setParamList($arrRet, 'deliv_time');
	// 配送料金の取得
	$col = "fee";
	$where = "deliv_id = ?";
	$table = "dtb_delivfee";
	$arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
	$objFormParam->setParamList($arrRet, 'fee');
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	if(!isset($objErr->arrErr['name']) && $_POST['deliv_id'] == "") {
		// 既存チェック
		$ret = sfIsRecord("dtb_deliv", "service_name", array($arrRet['service_name']));
		if ($ret) {
			$objErr->arrErr['name'] = "※ 同じ名称の組み合わせは登録できません。<br>";
		}
	}
	
	return $objErr->arrErr;
}
