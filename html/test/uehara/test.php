<?php
require_once("../../require.php");
require_once(DATA_PATH. "module/Tar.php");

//圧縮フラグTRUEはgzip解凍をおこなう
$tar = new Archive_Tar(USER_TEMPLATE_PATH."hhh/eccube-1.0.2beta.tar.gz", TRUE);
//指定されたフォルダ内に解凍する
$err = $tar->extract(USER_TEMPLATE_PATH."hhh/)


?>