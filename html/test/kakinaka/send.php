<?php

require_once("../../require.php");
require_once(DATA_PATH . "module/Request.php");

//$order_url = "http://test.ec-cube.net/ec-cube/load_module.php?module_id=4";
$order_url = "http://test.ec-cube.net/ec-cube/test/kakinaka/epsilon_check.php";

$arrData = array(
	'order_number' => '1',
	'trans_code' => '1',
	'paid' => 1	
);

$req = new HTTP_Request($order_url);
$req->setMethod(HTTP_REQUEST_METHOD_POST);
		
$arrSendData = array();
$req->addPostDataArray($arrData);
if (!PEAR::isError($req->sendRequest())) {
	echo("送信完了");
} else {
	echo("送信失敗");
}

sfprintr($req->getResponseBody());
sfprintr($req->getResponseHeader());


$body = "Content-Type: text/plain

1";

?>