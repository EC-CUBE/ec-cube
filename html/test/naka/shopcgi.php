<?php

require_once("../../require.php");
require_once(DATA_PATH . "module/Request.php");

$entry_url = "http://mod-i.ccsware.net/ohayou/EntryTran.php";
$exec_url = "https://mod-i.ccsware.net/ohayou/ExecTran.php";
// 受注番号の取得
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
$arrRet = lfGetPostArray($response);

// 決済情報の送信
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

$req = new HTTP_Request($exec_url);
$req->setMethod(HTTP_REQUEST_METHOD_POST);

$req->addPostDataArray($arrData);

if (!PEAR::isError($req->sendRequest())) {
	$response = $req->getResponseBody();
} else {
	$response = "";
}
$req->clearPostData();

$arrRet = lfGetPostArray($response);

sfPrintR($arrRet);

//---------------------------------------------------------------------------------------------------------------------------------
function lfGetPostArray($text) {
	if($text != "") {
		$text = ereg_replace("[\n\r]", "", $text);
		$arrTemp = split("&", $text);
		foreach($arrTemp as $ret) {
			list($key, $val) = split("=", $ret);
			$arrRet[$key] = $val;
		}
	}
	return $arrRet;
}
?>