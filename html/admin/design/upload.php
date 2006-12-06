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

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// ファイル管理クラス
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
// ファイル情報の初期化
lfInitFile();
// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();

switch($_POST['mode']) {
case 'upload':
	$objPage->arrErr = lfErrorCheck();
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
function lfErrorCheck() {

	global $objQuery;
	global $objFormParam;
	
	$arrRet = $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
sfprintr($arrRet);
	return $objErr->arrErr;
}
