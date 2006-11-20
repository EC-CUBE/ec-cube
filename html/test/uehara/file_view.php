<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../../require.php");


// ファイル内容表示
header("Content-type: application/octet-stream \n\n");
print(nl2br(lfReadFile(USER_PATH.$_GET['file'])));

?>