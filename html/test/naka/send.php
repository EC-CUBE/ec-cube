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
	'contract_code' => '13094800',
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

$parser = xml_parser_create();
xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
xml_parse_into_struct($parser,$response,$arrVal,$idx);
xml_parser_free($parser);

sfPrintR($arrVal);

$url = lfGetXMLValue($arrVal,'RESULT','REDIRECT');

print($url);

$err_detail = lfGetXMLValue($arrVal,'RESULT','ERR_DETAIL');

print($err_detail);

/*
$decode = urldecode($arrVal[3]['attributes']['ERR_DETAIL']);

print(mb_convert_encoding($decode, 'EUC-JP', 'Shift_JIS'));

$decode = urldecode($arrVal[4]['attributes']['MEMO1']);

print(mb_convert_encoding($decode, 'EUC-JP', 'Shift_JIS'));
*/


function lfGetXMLValue($arrVal, $tag, $att) {
	$ret = "";
	foreach($arrVal as $array) {
		if($tag == $array['tag']) {
			if(!is_array($array['attributes'])) {
				continue;
			}
			foreach($array['attributes'] as $key => $val) {
				if($key == $att) {
					$ret = $val;
					break;
				}
			}			
		}		
	}
	$dec = urldecode($ret);
	$enc = mb_convert_encoding($dec, 'EUC-JP', 'auto');
	return $enc;
}

?>