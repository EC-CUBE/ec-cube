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
	'TdTenantName' => '',		// 3D認証時表示用店舗名
	'TdFlag' => '',				// 3Dフラグ
	'ShopId' => 'test000003087',// ショップID
	'ShopPass' => 'lockon',		// ショップパスワード
	'Currency' => 'JPN',		// 通貨コード
	'Amount' => '1000',			// 金額
	'Tax' => '50',				// 消費税
	'JobCd' => 'CHECK',			// 処理区分
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

sfPrintR($arrRet);

// 決済情報の送信
$arrData = array(
	'AccessId' => $arrRet['ACCESS_ID'],
	'AccessPass' => $arrRet['ACCESS_PASS'],
	'OrderId' => $order_id,
	'RetURL' => 'http://test.ec-cube.net/ec-cube/test/naka/recv.php',
	// プロパーカードを扱わない場合はVISA固定でOK
	'CardType' => 'VISA, 11111, 111111111111111111111111111111111111, 1111111111',
	// 支払い方法
	/*
		1:一括
		2:分割
		3:ボーナス一括
		4:ボーナス分割
		5:リボ払い
	 */
	'Method' => '2',
	// 支払回数
	'PayTimes' => '4',
	'CardNo1' => '4444',
	'CardNo2' => '4444',
	'CardNo3' => '4444',
	'CardNo4' => '5780',
    'ExpireMM' => '06',
    'ExpireYY' => '07',
	// 加盟店自由項目返却フラグ
    'ClientFieldFlag' => '1',
    'ClientField1' => 'f1',
    'ClientField2' => 'f2',
    'ClientField3' => 'f3',
	// リダイレクトページでの応答を受け取らない
	'ModiFlag' => '1',	
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