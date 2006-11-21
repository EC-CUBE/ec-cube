<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../../require.php");

// 直接表示しないファイルを定義
$arrViewFile = array(
					 'html',
					 'htm',
					 'tpl',
					 'php',
);

// 拡張子取得
$arrResult = split('\.', $_GET['file']);
$ext = $arrResult[count($arrResult)-1];

// ファイル内容表示
if(in_array($ext, $arrViewFile)) {
	header("Content-type: text/plain\n\n");
	print(sfReadFile(USER_PATH.$_GET['file']));
} else {
	header("Location: ".USER_URL.$_GET['file']);
}
?>