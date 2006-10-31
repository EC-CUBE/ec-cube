<?php

require_once("../../require.php");
require_once(DATA_PATH . "module/Request.php");

$order_url = "http://beta.epsilon.jp/cgi-bin/order/receive_order3.cgi";

$arrData = array(
	'order_number' => '93963923',
	'st_code' => '10100-0000-00000',
	'memo1' => '試験用オーダー情報',
	'user_mail_add' => 'naka@lockon.co.jp',
	'item_name' => 'プリンタ',
	'contract_code' => '1309480x',
	'user_name' => 'naka',
	'process_code' => '2',
	'mission_code' => '1',
	'item_price' => '34800',
	'xml' => '1',		
	'item_code' => 'abc12345',
	'memo2' => '',
	'user_id' => 'test'
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

/*
$response = urldecode($response);
$response = mb_convert_encoding($response, 'EUC-JP', 'UTF-8');

print($response);
*/

$parser = xml_parser_create();
xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
xml_parse_into_struct($parser,$response,$arrVal,$idx);
xml_parser_free($parser);

$decode = urldecode($arrVal[3]['attributes']['ERR_DETAIL']);

print(mb_convert_encoding($decode, 'EUC-JP', 'Shift_JIS'));


?>