<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../../require.php");

			Header("Content-disposition: attachment; filename=".basename($_POST['select_file']));
			Header("Content-type: application/octet-stream; name=".basename($_POST['select_file']));
			Header("Cache-Control: ");
			Header("Pragma: ");

// ファイル内容表示
print("<pre>\n");
print(lfReadFile(USER_PATH.$_GET['file']));
print("</pre>\n");

/* 
 * 関数名：lfReadFile()
 * 引数1 ：ファイルパス
 * 説明　：ファイル読込
 */
function lfReadFile($filename) { 
    $str = ""; 
    // バイナリモードでオープン 
    $fp = @fopen($filename, "rb" ); 
    //ファイル内容を全て変数に読み込む 
    if($fp) { 
        $str = @fread($fp, filesize($filename)+1); 
    } 
    @fclose($fp); 
    // 改行コードの前に<br />を挿入 
    $str = nl2br($str); 
    return $str; 
} 
?>