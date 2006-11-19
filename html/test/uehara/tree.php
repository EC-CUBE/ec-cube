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

switch($_POST['mode']) {

case 'view':
case 'download':	
case 'delete':
	// 初期表示以外は現在選択中のディレクトリを取得
	$now_dir = $_POST['select_file'];
	
case 'view':
	break;

case 'download':
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

sfprintr($objPage->arrFileList);

sfprintr($now_dir);
sfprintr($arrFileList);

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
sfprintr($file);	// 行末の/を取り除く
					$dir = ereg_replace("\/$", "", $dir);
					$arrFileList[$cnt]['file_name'] = $file;
					$arrFileList[$cnt]['file_path'] = $dir."/".$file;
					$arrFileList[$cnt]['file_size'] = getDirSize($dir."/".$file);
					$arrFileList[$cnt]['file_time'] = date("Y/m/d", filemtime($dir."/".$file)); 
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
 * 引数1 ：ディレクトリパス格納配列
 */
function getDirSize($dir) { 
    $handle = opendir($dir); 
    while ($file = readdir($handle)) { 
        if ($file != '..' && $file != '.' && !is_dir($dir.'/'.$file)) { 
            $bytes += filesize($dir.'/'.$file); 
        } else if (is_dir($dir.'/'.$file) && $file != '..' && $file != '.') { 
            $bytes += getDirSize($dir.'/'.$file); 
        } 
    } 
    return $bytes; 
} 

/* 
 * 関数名：lfErrorCheck()
 * 説明　：エラーチェック
 */
function lfErrorCheck($array) {

	
	return $arrErr;
}
?>