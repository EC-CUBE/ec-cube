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
		$this->tpl_mainpage = 'basis/control.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_mainno = 'basis';
		$this->tpl_subno = 'control';
		$this->tpl_subtitle = 'サイト管理設定';
	}
}
$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

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
	
		// エラーチェック
		$objPage->arrErr = lfCheckError();
		if(count($objPage->arrErr) == 0) {
			lfSiteControlData($_POST['control_id']);
			// javascript実行
			$objPage->tpl_onload = "alert('更新が完了しました。');";
		}
		
		break;
	default:
		break;
}

// サイト管理情報の取得
$arrSiteControlList = lfGetControlList();

// プルダウンの作成
for ($i = 0; $i < count($arrSiteControlList); $i++) {	
	switch ($arrSiteControlList[$i]["control_id"]) {
		// トラックバック
		case SITE_CONTROL_TRACKBACK:
			$arrSiteControlList[$i]["control_area"] = $arrSiteControlTrackBack;
			break;
		// アフィリエイト
		case SITE_CONTROL_AFFILIATE:
			$arrSiteControlList[$i]["control_area"] = $arrSiteControlAffiliate;
			break;
		default:
			break;
	}
}

$objPage->arrControlList = $arrSiteControlList;
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
// サイト管理情報の取得
function lfGetControlList() {
	$objQuery = new SC_Query();
	// サイト管理情報の取得
	$sql = "SELECT * FROM dtb_site_control ";
	$sql .= "WHERE del_flg = 0";
	$arrRet = $objQuery->getall($sql);
	return $arrRet;
}

/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("設定状況", "control_flg", INT_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
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

/* DBへデータを登録する */
function lfSiteControlData($control_id = "") {
	global $objFormParam;
	
	$objQuery = new SC_Query();
	$sqlval = $objFormParam->getHashArray();	
	$sqlval['update_date'] = 'Now()';
	
	// 新規登録
	if($control_id == "") {
		// INSERTの実行
		//$sqlval['creator_id'] = $_SESSION['member_id'];
		$sqlval['create_date'] = 'Now()';
		$objQuery->insert("dtb_site_control", $sqlval);
	// 既存編集
	} else {
		$where = "control_id = ?";
		$objQuery->update("dtb_site_control", $sqlval, $where, array($control_id));
	}
}

?>