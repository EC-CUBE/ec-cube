<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = 'contents/file_manager.tpl';
		$this->tpl_mainno = 'contents';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "file";
		$this->tpl_subtitle = 'ファイル管理';		
	}
}

// ルートディレクトリ
$top_dir = USER_PATH;

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// 現在の階層を取得
if($_POST['mode'] != "") {
	$now_dir = $_POST['now_file'];
} else {
	// 初期表示はルートディレクトリ(user_data/upload/)を表示
	$now_dir = $top_dir;
}

// ファイル管理クラス
$objUpFile = new SC_UploadFile($now_dir, $now_dir);
// ファイル情報の初期化
lfInitFile();

switch($_POST['mode']) {

// ファイル表示
case 'view':
	// エラーチェック
	$arrErr = lfErrorCheck();
	if(!is_array($arrErr)) {
	
		// 選択されたファイルがディレクトリなら移動
		if(is_dir($_POST['select_file'])) {
			///$now_dir = $_POST['select_file'];
			// ツリー遷移用のjavascriptを埋め込む
			$arrErr['select_file'] = "※ ディレクトリを表示することは出来ません。";
			
		} else {
			// javascriptで別窓表示(テンプレート側に渡す)
			$file_url = ereg_replace(USER_PATH, "", $_POST['select_file']);
			$tpl_onload = "win02('./file_view.php?file=". $file_url ."', 'user_data', '600', '400');";
		}
	}
	break;
// ファイルダウンロード
case 'download':

	// エラーチェック
	$arrErr = lfErrorCheck();
	if(!is_array($arrErr)) {
		if(is_dir($_POST['select_file'])) {
			// ディレクトリの場合はjavascriptエラー
			$arrErr['select_file'] = "※ ディレクトリをダウンロードすることは出来ません。";
		} else {
			// ファイルダウンロード
			sfDownloadFile($_POST['select_file']);
			exit;			
		}
	}
	break;
// ファイル削除
case 'delete':
	// エラーチェック
	$arrErr = lfErrorCheck();
	if(!is_array($arrErr)) {
		sfDeleteDir($_POST['select_file']);
	}
	break;
// ファイル作成
case 'create':
	// エラーチェック
	$arrErr = lfCreateErrorCheck();
	if(!is_array($arrErr)) {
		$create_dir = ereg_replace("/$", "", $now_dir);
		// ファイル作成
		if(!sfCreateFile($create_dir."/".$_POST['create_file'], 0755)) {
			// 作成エラー
			$arrErr['create_file'] = "※ ".$_POST['create_file']."の作成に失敗しました。";
		} else {
			$tpl_onload .= "alert('フォルダを作成しました。');";
		}
	}
	break;
// ファイルアップロード
case 'upload':
	// 画像保存処理
	$ret = $objUpFile->makeTempFile('upload_file', false);
	if($ret != "") {
		$arrErr['upload_file'] = $ret;
	} else {
		$tpl_onload .= "alert('ファイルをアップロードしました。');";
	}
	break;
// フォルダ移動
case 'move':
	$now_dir = $_POST['tree_select_file'];
	break;
// 初期表示
default :
	break;
}


// 現在のディレクトリ配下のファイル一覧を取得
$objPage->arrFileList = sfGetFileList($now_dir);
$objPage->tpl_now_dir = $now_dir;
$objPage->tpl_url_dir = ereg_replace(USER_PATH, "", $_POST['select_file']);

sfprintr($objPage->tpl_url_dir);

$objPage->tpl_now_file = basename($now_dir);
$objPage->arrErr = $arrErr;
$objPage->arrParam = $_POST;

// ツリーを表示する divタグid, ツリー配列変数名, 現在ディレクトリ
$objPage->tpl_onload .= "fnTreeView('tree', arrTree, '$now_dir');$tpl_onload";
// ツリー配列作成用 javascript
$arrTree = sfGetFileTree($top_dir, $_POST['tree_status']);
$objPage->tpl_javascript .= "arrTree = new Array();\n";
foreach($arrTree as $arrVal) {
	$objPage->tpl_javascript .= "arrTree[".$arrVal['count']."] = new Array(".$arrVal['count'].", '".$arrVal['type']."', '".$arrVal['path']."', ".$arrVal['rank'].",";
	if ($arrVal['open']) {
		$objPage->tpl_javascript .= "true);\n";
	} else {
		$objPage->tpl_javascript .= "false);\n";
	}
}

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

/* 
 * 関数名：lfErrorCheck()
 * 説明　：エラーチェック
 */
function lfErrorCheck() {
	$objErr = new SC_CheckError($_POST);
	$objErr->doFunc(array("ファイル", "select_file"), array("SELECT_CHECK"));
	
	return $objErr->arrErr;
}

/* 
 * 関数名：lfCreateErrorCheck()
 * 説明　：ファイル作成処理エラーチェック
 */
function lfCreateErrorCheck() {
	$objErr = new SC_CheckError($_POST);
	$objErr->doFunc(array("作成ファイル名", "create_file"), array("EXIST_CHECK", "FILE_NAME_CHECK_BY_NOUPLOAD"));
	
	return $objErr->arrErr;
}

/* 
 * 関数名：lfInitFile()
 * 説明　：ファイル情報の初期化
 */
function lfInitFile() {
	global $objUpFile;
	$objUpFile->addFile("ファイル", 'upload_file', array(), FILE_SIZE, true, 0, 0, false);
}
?>