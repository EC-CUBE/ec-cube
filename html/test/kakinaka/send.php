<?php

require_once("../../require.php");
require_once(DATA_PATH . "module/Request.php");

$order_url = "http://test.ec-cube.net/ec-cube/load_module.php?module_id=4";

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
	echo("������λ");
} else {
	echo("��������");
}

$req->clearPostData();

?>