<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");


$top_dir = USER_PATH;

$objView = new SC_UserView("./templates/");
$objQuery = new SC_Query();

switch($_POST['mode']) {

case 'view':
case 'download':	
case 'delete':
	$now_dir = $_POST['view_dir'];
	
case 'view':
	break;

case 'download':
	break;
	
case 'delete':
	break;
	
default :
	$now_dir = $top_dir;
	break;
}
// 現在のディレクトリ配下のファイル一覧を取得
$arrFileList = getFileList($now_dir);

sfprintr($now_dir);
sfprintr($arrFileList);

//$objView->assignobj($objPage);
$objView->display("tree.tpl");

//-----------------------------------------------------------------------------------------------------------------------------------

/* 
 * 関数名：getFileList()
 * 説明　：指定パス配下のディレクトリ取得
 * 引数1 ：ツリーを格納配列
 * 引数2 ：取得するディレクトリパス
 */
function getFileList($dir) {
	$arrFileList = array();
	if (is_dir($dir)) { 
	    if ($dh = opendir($dir)) { 
	        while (($file = readdir($dh)) !== false) { 
				// ./ と ../を除くディレクトリのみを取得
				//if(filetype($dir . $file) == 'dir' && $file != "." && $file != "..") {
				if($file != "." && $file != "..") {
					$arrFileList[] = $dir.$file;
				}
	        } 
	        closedir($dh); 
	    }
	} 
	
	return $arrFileList;
}

?>