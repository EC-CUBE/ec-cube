<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../../require.php");


// ファイル内容表示
header("Content-type: text/plain\n\n");
print(lfReadFile(USER_PATH.$_GET['file']));

?>