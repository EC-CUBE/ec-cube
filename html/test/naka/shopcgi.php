<?php

require_once("../../require.php");
require_once(DATA_PATH . "module/Request.php");

/*

from index2.html

Array
(
    [TenantNo] => 11111111
    [OrderId] => 2006-11-08-11-39-53-966
    [JobCd] => CHECK
    [Amount] => 
    [Tax] => 
    [Currency] => 
    [Memo] => 
)

*/

/*

センタへの送信データ

OrderId=>2006-11-08-12-43-21-935
TdTenantName=>
TdFlag=>
ShopId=>test
Amount=>0
ShopPass=>test
Currency=>
Tax=>0
JobCd=>CHECK
TenantNo=>TenantNo

*/

$order_url = "http://mod-i.ccsware.net/ohayou/EntryTran.php";

$arrData = array(
	'OrderId' => '2006-11-08-12-43-21-936',
	'TdTenantName' => '',
	'TdFlag' => '',
	'ShopId' => 'test',
	'Amount' => '0',
	'ShopPass' => 'test',
	'Currency' => '',
	'Tax' => '0',
	'JobCd' => 'CHECK',
	'TenantNo' => 'TenantNo',
);

$req = new HTTP_Request($order_url);
$req->setMethod(HTTP_REQUEST_METHOD_POST);
		
$arrSendData = array();
$req->addPostDataArray($arrData);

if (!PEAR::isError($req->sendRequest())) {
	$response = $req->getResponseBody();
} else {
	$response = "";
}
$req->clearPostData();

print($response);


?>