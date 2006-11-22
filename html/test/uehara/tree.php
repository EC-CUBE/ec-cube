<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

class LC_Page{
	function LC_Page() {
	}
}

sfprintr($_POST);



$top_dir = USER_PATH;

$objPage = new LC_Page();
$objView = new SC_UserView("./templates");
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
			$now_dir = $_POST['select_file'];
		} else {
			// javascriptで別窓表示(テンプレート側に渡す)
			$file_url = ereg_replace(USER_PATH, "", $_POST['select_file']);
			$objPage->tpl_javascript = "win02('./file_view.php?file=". $file_url ."', 'user_data', '600', '400');";
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
			$arrErr['download'] = "※ ディレクトリをダウンロードすることは出来ません。";
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
	$create_dir = ereg_replace("/$", "", $now_dir);
	// ファイル作成
	if(!sfCreateFile($create_dir."/".$_POST['create_file'], 0755)) {
		// 作成エラー
		$arrErr['create'] = "※ ".$_POST['create_file']."の作成に失敗しました。";
	} else {
		$objPage->tpl_javascript = "alert('フォルダを作成しました。');";
	}
	break;
// ファイルアップロード
case 'upload':
	// 画像保存処理
	$ret = $objUpFile->makeTempFile('upload_file', false);
	if($ret != "") {
		$arrErr['upload_file'] = $ret;
	} else {
		$objPage->tpl_javascript = "alert('ファイルをアップロードしました。');";
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
$objPage->tpl_now_file = $now_dir;
$objPage->arrErr = $arrErr;
$objPage->arrParam = $_POST;
$objPage->arrTree = sfGetFileTree($top_dir, $_POST['tree_status']);

$objView->assignobj($objPage);
$objView->display("tree.tpl");

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
 * 関数名：lfInitFile()
 * 説明　：ファイル情報の初期化
 */
function lfInitFile() {
	global $objUpFile;
	$objUpFile->addFile("ファイル", 'upload_file', array(), FILE_SIZE, true, 0, 0, false);
}
?>