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

$entry_url = "http://mod-i.ccsware.net/ohayou/EntryTran.php";
$exec_url = "https://mod-i.ccsware.net/ohayou/ExecTran.php";
$order_id = sfGetUniqRandomId();

// 店舗情報の送信
$arrData = array(
	'OrderId' => $order_id,		// 店舗ごとに一意な注文IDを送信する。
	'TdTenantName' => '',
	'TdFlag' => '',
	'ShopId' => 'test',
	'Amount' => '100',
	'ShopPass' => 'test',
	'Currency' => 'JPN',
	'Tax' => '5',
	'JobCd' => 'CHECK',
	'TenantNo' => '111111111',	// cgi-4で作成した店舗IDを送信する。
);

$req = new HTTP_Request($entry_url);
$req->setMethod(HTTP_REQUEST_METHOD_POST);
		
$req->addPostDataArray($arrData);

if (!PEAR::isError($req->sendRequest())) {
	$response = $req->getResponseBody();
} else {
	$response = "";
}
$req->clearPostData();

if($response != "") {
	$response = ereg_replace("[\n\r]", "", $response);
	$arrTemp = split("&", $response);
	foreach($arrTemp as $ret) {
		list($key, $val) = split("=", $ret);
		$arrRet[$key] = $val;
	}
}

sfPrintR($arrRet);

// 決済情報の送信
/*
    [AccessId] => 60f511479544541eb1ec9dc6f700b8c1
    [AccessPass] => 925a413ce991e7b9230fbc575b9dc430
    [OrderId] => 2006-11-08-13-08-07-732
    [RetURL] => http://test.ec-cube.net/cgi-bin/ShopCGI/cgi-1/ShopBuy.pl
    [CardType] => VISA,     11111, 111111111111111111111111111111111111, 1111111111
    [Method] => 2
    [PayTimes] => 4
    [CardNo1] => 4111
    [CardNo2] => 1111
    [CardNo3] => 1111
    [CardNo4] => 1111
    [ExpireMM] => 06
    [ExpireYY] => 07
    [ClientFieldFlag] => 1
    [ClientField1] => f1
    [ClientField2] => f2
    [ClientField3] => f3
*/
$arrData = array(
	'AccessId' => $arrRet['ACCESS_ID'],
	'AccessPass' => $arrRet['ACCESS_PASS'],
	'OrderId' => sfGetUniqRandomId(),
	'RetURL' => 'http://test.ec-cube.net/ec-cube/test/naka/recv.php',
	'CardType' => 'VISA,     11111, 111111111111111111111111111111111111, 1111111111',
	'Method' => '2',
	'PayTimes' => '4',
	'CardNo1' => '4111',
	'CardNo2' => '1111',
	'CardNo3' => '1111',
	'CardNo4' => '1111',
    'ExpireMM' => '06',
    'ExpireYY' => '07',
    'ClientFieldFlag' => '1',
    'ClientField1' => 'f1',
    'ClientField2' => 'f2',
    'ClientField3' => 'f3',
	'ModiFlag' => '1',	// リダイレクトページでの応答を受け取らない。
);

sfPrintR($arrData);

$req = new HTTP_Request($exec_url);
$req->setMethod(HTTP_REQUEST_METHOD_POST);

$req->addPostDataArray($arrData);

if (!PEAR::isError($req->sendRequest())) {
	$response = $req->getResponseBody();
} else {
	$response = "";
}
$req->clearPostData();

print("<!--");
sfPrintR($response);
print("-->");

?>