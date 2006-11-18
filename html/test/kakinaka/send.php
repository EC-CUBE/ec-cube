<?php

require_once("../../require.php");
require_once(DATA_PATH . "module/Request.php");

$order_url = "http://ec-cube.net/ec-cube/test/kakinaka/epsilon_check.php";

$arrData = array(
	'order_number' => '1',
	'trans_code' => '00100-0000-00000',
	'paid' => 1	
);

$req = new HTTP_Request($order_url);
$req->setMethod(HTTP_REQUEST_METHOD_POST);
		
$arrSendData = array();
$req->addPostDataArray($arrData);


?>