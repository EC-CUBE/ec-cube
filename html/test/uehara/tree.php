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
$objView = new SC_UserView("./templates/");
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
	        while (($file = readdir($dh)) !== false) { 
				// ./ と ../を除くディレクトリのみを取得
				//if(filetype($dir . $file) == 'dir' && $file != "." && $file != "..") {
				if($file != "." && $file != "..") {
					$arrFileList[]['file_name'] = $file;
					$arrFileList[]['file_path'] = $dir.$file;
					$arrFileList[]['file_size'] = filesize($dir.$file);
				}
	        } 
	        closedir($dh); 
	    }
	} 
	
	return $arrFileList;
}
?>