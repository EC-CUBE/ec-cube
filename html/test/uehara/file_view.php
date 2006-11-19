<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
 
// ファイル内容表示
print("<pre>\n");
lfReadFile($_GET['file']);
print("</pre>\n");

/* 
 * 関数名：lfReadFile()
 * 引数1 ：ファイルパス
 * 説明　：ファイル読込
 */
function lfReadFile($file) {
	$fp = fopen($file, "r");
	$read_file = fpassthru($fp); 
	fclose($fp); 
}
?>