<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");
require_once(DATA_PATH. "module/Tar.php");

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

// アップロードしたファイルをフォルダ
$new_file_dir = USER_TEMPLATE_PATH.$_POST['template_code'];

// ファイル管理クラス
$objUpFile = new SC_UploadFile(TEMPLATE_TEMP_DIR, $new_file_dir);
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
		$ret = @mkdir($new_file_dir);
		// 一時フォルダから保存ディレクトリへ移動
		$objUpFile->moveTempFile();
		// 解凍
		lfUnpacking($new_file_dir, $_FILES['template_file']['name'], $new_file_dir."/");
		// DBにテンプレート情報を保存
		lfRegistTemplate($arrRet);
		// 完了表示javascript
		$objPage->tpl_onload = "alert('テンプレートファイルをアップロードしました。');";
		// フォーム値をクリア
		$objFormParam->setParam(array('template_code' => "", 'template_name' => ""));
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

	$objUpFile->addFile("テンプレートファイル", 'template_file', array(), TEMPLATE_SIZE, true, 0, 0, false);
}

/* 
 * 関数名：lfInitParam()
 * 説明　：パラメータ情報の初期化
 */
function lfInitParam() {
	global $objFormParam;
		
	$objFormParam->addParam("テンプレートコード", "template_code", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "ALNUM_CHECK"));
	$objFormParam->addParam("テンプレート名", "template_name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
}

/* 
 * 関数名：lfErrorCheck()
 * 引数1 ：フォームの値
 * 説明　：エラーチェック
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
		// ファイルの拡張子チェック(.tar/tar.gzのみ許可)
		$errFlag = true;
		$array_ext = explode(".", $_FILES['template_file']['name']);
		$ext = $array_ext[ count ( $array_ext ) - 1 ];
		$ext = strtolower($ext);
		// .tarチェック
		if ($ext == 'tar') {
			$errFlag = false;
		}

		$ext = $array_ext[ count ( $array_ext ) - 2 ].".".$ext;
		$ext = strtolower($ext);
		// .tar.gzチェック
		if ($ext== 'tar.gz') {
			$errFlag = false;
		}
		
		if($errFlag) {
			$objErr->arrErr['template_file'] = "※ アップロードするテンプレートファイルで許可されている形式は、tar/tar.gzです。<br />";		
		}
	}
	
	return $objErr->arrErr;
}

/* 
 * 関数名：lfErrorCheck()
 * 引数1 ：パラメータ
 * 説明　：テンプレートデータ登録
 */
function lfRegistTemplate($arrList) {
	global $objQuery;
	
	// INSERTする値を作成する。
	$sqlval['template_code'] = $arrList['template_code'];
	$sqlval['template_name'] = $arrList['template_name'];
	$sqlval['create_date'] = "now()";
	$sqlval['update_date'] = "now()";

	$objQuery->insert("dtb_templates", $sqlval);
}

/* 
 * 関数名：lfUnpacking
 * 引数1 ：ディレクトリ
 * 引数2 ：ファイルネーム
 * 引数3 ：解凍ディレクトリ
 * 説明　：テンプレートデータ登録
 */
function lfUnpacking($dir, $file_name, $unpacking_dir) {

	// 圧縮フラグTRUEはgzip解凍をおこなう
	$tar = new Archive_Tar("$dir/$file_name", TRUE);

	// 拡張子を切り取る
	$unpacking_name = ereg_replace("\.tar$", "", $file_name);
	$unpacking_name = ereg_replace("\.tar\.gz$", "", $file_name);

	// 指定されたフォルダ内に解凍する
	$err = $tar->extractModify($unpacking_dir, $unpacking_name);

	// フォルダ削除
	@sfDelFile("$dir/$unpacking_name");
	// 圧縮ファイル削除
	@unlink("$dir/$file_name");

	return $err;
}