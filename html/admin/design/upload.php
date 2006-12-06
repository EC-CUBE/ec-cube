<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

class LC_Page {

	function LC_Page() {
		$this->tpl_mainpage = 'design/upload.tpl';
		$this->tpl_subnavi = 'design/subnavi.tpl';
		$this->tpl_subno = 'template';
		$this->tpl_subno_template = 'upload';
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = 'アップロード';
		$this->template_name = 'アップロード';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// ファイル管理クラス
$objUpFile = new SC_UploadFile(TEMPLATE_TEMP_DIR, USER_TEMPLATE_PATH.$_POST['template_code']);
// ファイル情報の初期化
lfInitFile();
// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();

switch($_POST['mode']) {
case 'upload':
	$objFormParam->setParam($_POST);
	$arrRet = $objFormParam->getHashArray();
	
	$objPage->arrErr = lfErrorCheck($arrRet);

	// ファイルを一時フォルダへ保存
	$ret = $objUpFile->makeTempFile('template_file', false);
	if($ret != "") {
		$objPage->arrErr['template_file'] = $ret;
	} else if(count($objPage->arrErr) <= 0) {
		// フォルダ作成
		$ret = @mkdir(USER_TEMPLATE_PATH.$arrRet['template_code']);
		// 一時フォルダから保存ディレクトリへ移動
		$objUpFile->moveTempFile();
		$objPage->tpl_onload = "alert('テンプレートファイルをアップロードしました。');";
	}
	break;
default:
	break;
}
// 画面の表示
$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

/* 
 * 関数名：lfInitFile()
 * 説明　：ファイル情報の初期化
 */
function lfInitFile() {
	global $objUpFile;

	$objUpFile->addFile("テンプレートファイル", 'template_file', array('tar.gz', 'tgz', 'tar.bz2'), TEMPLATE_SIZE, true, 0, 0, false);
}

/* 
 * 関数名：lfInitParam()
 * 説明　：パラメータ情報の初期化
 */
function lfInitParam() {
	global $objFormParam;
		
	$objFormParam->addParam("テンプレートコード", "template_code", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("テンプレート名", "template_name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
}

/* 
 * 関数名：lfErrorCheck()
 * 説明　：パラメータ情報の初期化
 */
function lfErrorCheck($arrList) {
	global $objQuery;
	global $objFormParam;
	
	$objErr = new SC_CheckError($arrList);
	$objErr->arrErr = $objFormParam->checkError();
	
	if(count($objErr->arrErr) <= 0) {
		// 同名のフォルダが存在する場合はエラー
		if(file_exists(USER_TEMPLATE_PATH.$arrList['template_code'])) {
			$objErr->arrErr['template_code'] = "※ 同名のファイルがすでに存在します。<br/>";
		}
		// DBにすでに登録されていないかチェック
		$ret = $objQuery->get("dtb_templates", "template_code", "template_code = ?", array($arrList['template_code']));
		if($ret != "") {
			$objErr->arrErr['template_code'] = "※ すでに登録されているテンプレートコードです。<br/>";
		}
	}
	
	return $objErr->arrErr;
}

function lfRegistTemplate($arrList) {
	global $objQuery;
	
	// INSERTする値を作成する。
	$sqlval['name'] = $arrList['template_code'];
	$sqlval['category_id'] = $arrList['template_name'];
	$sqlval['create_date'] = "now()";
	$sqlval['update_date'] = "now()";

	$objQuery->insert("dtb_templates", $sqlval);
}

