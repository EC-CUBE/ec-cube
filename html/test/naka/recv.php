<?php
require_once("../../require.php");
require_once(DATA_PATH . "module/Request.php");

$strmask = "/home/web/test.ec-cube.net/cgi-bin/ShopCGI/common/strmask/FreeBSD/strmask.exe";
$cmd = $strmask . " -d " . $_GET['SendData'];

$tmpResult = popen($cmd, "r");

// ��̼���
while( ! FEOF ( $tmpResult ) ) {
	$result .= FGETS($tmpResult);
}
pclose($tmpResult);				// 	�ѥ��פ��Ĥ���

gfDebugLog($cmd);

?>