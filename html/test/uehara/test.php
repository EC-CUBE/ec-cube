<?php
require_once("../../require.php");
require_once(DATA_PATH. "module/Tar.php");

//圧縮フラグTRUEはgzip解凍をおこなう
$tar = new Archive_Tar(USER_TEMPLATE_PATH."bbb/eccube-1.0.2beta.tar.gz", TRUE);
//指定されたフォルダ内に解凍する
$err = $tar->extractModify(USER_TEMPLATE_PATH."bbb/", "eccube-1.0.2beta");

	// 拡張子を切り取る
	$file_name = ereg_replace("\.tar$", "", "bbb/eccube-1.0.2beta.tar.gz");
	$file_name = ereg_replace("\.tar\.gz$", "", $file_name);

echo $file_name;	
?>