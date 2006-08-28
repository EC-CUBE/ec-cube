<?php
require_once("../../../require.php");
$file = ROOT_DIR . "html/index.php";

sfprintr($file);

chmod($file, 0777);

?>