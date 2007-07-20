<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../../require.php");

// ソースとして表示するファイルを定義(直接実行しないファイル)
$arrViewFile = array(
					 'html',
					 'htm',
					 'tpl',
					 'php',
					 'css',
					 'js',
);

// 拡張子取得
$arrResult = split('\.', $_GET['file']);
$ext = $arrResult[count($arrResult)-1];

// ファイル内容表示
if(in_array($ext, $arrViewFile)) {
	// ファイルを読み込んで表示
	header("Content-type: text/plain\n\n");
	print(sfReadFile(USER_PATH.$_GET['file']));
} else {
	header("Location: ".USER_URL.$_GET['file']);
}
?>