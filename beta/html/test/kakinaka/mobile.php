<?php

require_once("../../require.php");
require_once(DATA_PATH . "module/Request.php");

//$order_url = "http://test.ec-cube.net/ec-cube/load_module.php?module_id=4";
$order_url = "http://beta.ec-cube.net/test/kakinaka/epsilon_check.php";

$arrData = array(
	'order_number' => '1',
	'trans_code' => '1',
	'paid' => 1	
);

// POSTデータを送信し、応答情報を取得する
$response = sfSendPostData($order_url, $arrData, array(200));
	
sfprintr($_SERVER);


?>