<?php
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/payment_input.tpl';
		$this->tpl_subtitle = '支払方法設定';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

// ファイル管理クラス
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
// ファイル情報の初期化
$objUpFile = lfInitFile($objUpFile);
// Hiddenからのデータを引き継ぐ
$objUpFile->setHiddenFileList($_POST);

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
		lfRegistData($_POST['payment_id']);
		// 一時ファイルを本番ディレクトリに移動する
		$objUpFile->moveTempFile();
		// 親ウィンドウを更新するようにセットする。
		$objPage->tpl_onload="fnUpdateParent('".URL_PAYMENT_TOP."'); window.close();";
	}
	break;
// 画像のアップロード
case 'upload_image':
	// ファイル存在チェック
	$objPage->arrErr = array_merge($objPage->arrErr, $objUpFile->checkEXISTS($_POST['image_key']));
	// 画像保存処理
	$objPage->arrErr[$_POST['image_key']] = $objUpFile->makeTempFile($_POST['image_key']);
	break;
// 画像の削除
case 'delete_image':
	$objUpFile->deleteFile($_POST['image_key']);
	break;
default:
	break;
}

if($_POST['mode'] == "") {
	switch($_GET['mode']) {
	case 'pre_edit':
		if(sfIsInt($_GET['payment_id'])) {
			$arrRet = lfGetData($_GET['payment_id']);
			$objFormParam->setParam($arrRet);
			// DBデータから画像ファイル名の読込
			$objUpFile->setDBFileList($arrRet);
			$objPage->tpl_payment_id = $_GET['payment_id'];
		}
		break;
	default:
		break;
	}
} else {
	$objPage->tpl_payment_id = $_POST['payment_id'];
}

$objPage->arrDelivList = sfGetIDValueList("dtb_deliv", "deliv_id", "service_name");
$objPage->arrForm = $objFormParam->getFormParamList();

// FORM表示用配列を渡す。
$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
// HIDDEN用に配列を渡す。
$objPage->arrHidden = array_merge((array)$objPage->arrHidden, (array)$objUpFile->getHiddenFileList());

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
/* ファイル情報の初期化 */
function lfInitFile($objUpFile) {
	$objUpFile->addFile("ロゴ画像", 'payment_image', array('gif'), IMAGE_SIZE, false, CLASS_IMAGE_WIDTH, CLASS_IMAGE_HEIGHT);
	return $objUpFile;
}

/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("支払方法", "payment_method", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("手数料", "charge", PRICE_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("利用条件(〜円以上)", "rule", PRICE_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("利用条件(〜円以下)", "upper_rule", PRICE_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("配送サービス", "deliv_id", INT_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("固定", "fix");
}

/* DBからデータを読み込む */
function lfGetData($payment_id) {
	$objQuery = new SC_Query();
	$where = "payment_id = ?";
	$arrRet = $objQuery->select("*", "dtb_payment", $where, array($payment_id));
	return $arrRet[0];
}

/* DBへデータを登録する */
function lfRegistData($payment_id = "") {
	global $objFormParam;
	global $objUpFile;
	
	$objQuery = new SC_Query();
	$sqlval = $objFormParam->getHashArray();
	$arrRet = $objUpFile->getDBFileList();	// ファイル名の取得
	$sqlval = array_merge($sqlval, $arrRet);	
	$sqlval['update_date'] = 'Now()';
	
	if($sqlval['fix'] != '1') {
		$sqlval['fix'] = 2;	// 自由設定
	}
	
	// 新規登録
	if($payment_id == "") {
		// INSERTの実行
		$sqlval['creator_id'] = $_SESSION['member_id'];
		$sqlval['rank'] = $objQuery->max("dtb_payment", "rank") + 1;
		$sqlval['create_date'] = 'Now()';
		$objQuery->insert("dtb_payment", $sqlval);
	// 既存編集
	} else {
		$where = "payment_id = ?";
		$objQuery->update("dtb_payment", $sqlval, $where, array($payment_id));
	}
}

/*　利用条件の数値チェック */

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	// 利用条件チェック
	$objErr->doFunc(array("利用条件(〜円以上)", "利用条件(〜円以下)", "rule", "upper_rule"), array("GREATER_CHECK"));
	
	return $objErr->arrErr;
}


?>