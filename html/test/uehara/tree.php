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

$top_dir = USER_PATH;

$objPage = new LC_Page();
$objView = new SC_UserView("./templates");
$objQuery = new SC_Query();

// 現在の階層を取得
if($_POST['mode'] != "") {
	$now_dir = $_POST['now_file'];
}

switch($_POST['mode']) {

case 'view':
	// エラーチェック
	if(!is_array(lfErrorCheck())) {
	
		// 選択されたファイルがディレクトリなら移動
		if(is_dir($_POST['select_file'])) {
			$now_dir = $_POST['select_file'];
		} else {
			// javascriptで別窓表示(テンプレート側に渡す)
			$file_url = ereg_replace(USER_PATH, USER_URL, $_POST['select_file']);
			$objPage->tpl_javascript = "win02('". $file_url ."', 'user_data', '600', '400');";
		}
	}
	break;

case 'download':

	// エラーチェック
	if(!is_array(lfErrorCheck())) {
		if(is_dir($_POST['select_file'])) {
			// ディレクトリの場合はjavascriptエラー
			$objPage->tpl_javascript = "alert('※　ディレクトリをダウンロードすることは出来ません。');";
		} else {
			// ファイルの場合はダウンロードさせる
			header('Content-Disposition: attachment; filename="'. basename($_POST['select_file']) .'"');
		}
	}
	break;
	
case 'delete':
	break;
	
default :
	// 初期表示はルートディレクトリ(user_data/upload/)を表示
	$now_dir = $top_dir;
	break;
}
// 現在のディレクトリ配下のファイル一覧を取得
$objPage->arrFileList = lfGetFileList($now_dir);
$objPage->tpl_now_file = $now_dir;

sfprintr($now_dir);

$objView->assignobj($objPage);
$objView->display("tree.tpl");

//-----------------------------------------------------------------------------------------------------------------------------------

/* 
 * 関数名：lfGetFileList()
 * 説明　：指定パス配下のディレクトリ取得
 * 引数1 ：ツリーを格納配列
 * 引数2 ：取得するディレクトリパス
 */
function lfGetFileList($dir) {
	$arrFileList = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) { 
			$cnt = 0;
			while (($file = readdir($dh)) !== false) { 
				// ./ と ../を除くファイルのみを取得
				if($file != "." && $file != "..") {
					// 行末の/を取り除く
					$dir = ereg_replace("\/$", "", $dir);
					$path = $dir."/".$file;
					$arrFileList[$cnt]['file_name'] = $file;
					$arrFileList[$cnt]['file_path'] = $path;
					$arrFileList[$cnt]['file_size'] = getDirSize($path);
					$arrFileList[$cnt]['file_time'] = date("Y/m/d", filemtime($path)); 
					$cnt++;
				}
	        }
	        closedir($dh); 
	    }
	} 
	
	return $arrFileList;
}

/* 
 * 関数名：getDirSize()
 * 説明　：指定したディレクトリのバイト数を取得
 * 引数1 ：ファイル格納配列
 */
function getDirSize($dir) {
	if(file_exists($dir)) {
		// ディレクトリの場合下層ファイルの総量を取得
		if (is_dir($dir)) {
		    $handle = opendir($dir); 
		    while ($file = readdir($handle)) {
				$path = $dir."/".$file;
		        if ($file != '..' && $file != '.' && !is_dir($path)) { 
		            $bytes += filesize($path); 
		        } else if (is_dir($path) && $file != '..' && $file != '.') { 
		            $bytes += getDirSize($path); 
		        } 
		    } 
		} else {
			// ファイルの場合
			$bytes = filesize($dir);
		}
	} else {
		// ディレクトリが存在しない場合は0byteを返す
		$bytes = 0;
	}
	
	if($bytes == "") $bytes = 0;
	
    return $bytes; 
} 

/* 
 * 関数名：lfErrorCheck()
 * 説明　：エラーチェック
 */
function lfErrorCheck() {

	if($_POST['select_file'] == '') {
		$arrErr['select_file'] = "※　ファイルが選択されていません。";
	}
	return $arrErr;
}
?>