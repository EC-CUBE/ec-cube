<?php
require_once("../../require.php");
require_once(DATA_PATH . "module/Request.php");

$strmask = "/home/web/test.ec-cube.net/cgi-bin/ShopCGI/common/strmask/FreeBSD_4.4/strmask.exe";
$cmd = $strmask . " -d " . $_GET['SendData'];

$tmpResult = popen($cmd, "r");

// 結果取得
while( ! FEOF ( $tmpResult ) ) {
	$result .= FGETS($tmpResult);
}
pclose($tmpResult);				// 	パイプを閉じる

gfDebugLog($result);

?>