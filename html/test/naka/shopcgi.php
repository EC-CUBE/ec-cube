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

// 店舗情報の送信
$arrData = array(
	'OrderId' => sfGetUniqRandomId(),	// 店舗ごとに一意な注文IDを送信する。
	'TdTenantName' => '',
	'TdFlag' => '',
	'ShopId' => 'test',
	'Amount' => '100',
	'ShopPass' => 'test',
	'Currency' => 'JPN',
	'Tax' => '5',
	'JobCd' => 'CHECK',
	'TenantNo' => '111111111',			// cgi-4で作成した店舗IDを送信する。
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

if($response != "") {
	$arrTemp = split("&", $response);
	foreach($arrTemp as $ret) {
		list($key, $val) = split("=", $ret);
		$arrRet[$key] = $val;
	}
}

// 応答パターン
/*
	Array
	(
	    [ERR_CODE] => 0
	    [ERR_INFO] => OK
	    [ACCESS_ID] => dfc9c975e1791cb2d79a2bbe4684f3df
	    [ACCESS_PASS] => fbab58b057608871808800f5ff6a0d23
	
	)
*/

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
	'AccessId' => $arrRet['ACCESS_ID'],		// 店舗ごとに一意な注文IDを送信する。
	'AccessPass' => $arrRet['ACCESS_PASS'],	// 店舗ごとに一意な注文IDを送信する。
	
	'TdTenantName' => '',
	'TdFlag' => '',
	'ShopId' => 'test',
	'Amount' => '100',
	'ShopPass' => 'test',
	'Currency' => 'JPN',
	'Tax' => '5',
	'JobCd' => 'CHECK',
	'TenantNo' => '111111111',			// cgi-4で作成した店舗IDを送信する。
);


?>