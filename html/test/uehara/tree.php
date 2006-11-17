<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");


$dir = USER_PATH;

$objView = new SC_UserView("./templates/");
$objQuery = new SC_Query();

$arrTree = array();

getDir($arrTree, $dir);

sfprintr($arrTree);
//$objView->assignobj($objPage);
$objView->display("tree.tpl");

//-----------------------------------------------------------------------------------------------------------------------------------

/* 
 * 関数名：getDir()
 * 説明　：指定パス配下のディレクトリ取得
 * 引数1 ：ツリーを格納配列
 * 引数2 ：取得するディレクトリパス
 */
function getDir(&$arrTree, $dir) {
	if (is_dir($dir)) { 
	    if ($dh = opendir($dir)) { 
	        while (($file = readdir($dh)) !== false) { 
				// ./ と ../を除くディレクトリのみを取得
				if(filetype($dir . $file) == 'dir' && $file != "." && $file != "..") {
					$arrTree[] = dir.file;
				} 
	        } 
	        closedir($dh); 
	    }
	} 
}

?>