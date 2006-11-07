<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'mdl_epsilon/card.tpl';			// �ᥤ��ƥ�ץ졼��
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');		
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// ������������������Ƚ��
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// �����Ƚ��׽���
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
// �������ơ��֥���ɹ�
$arrData = sfGetOrderTemp($uniqid);
// �����Ƚ��פ򸵤˺ǽ��׻�
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

sfprintr($arrData);
sfprintr($objPage);

/*

$order_url = "http://beta.epsilon.jp/cgi-bin/order/receive_order3.cgi";

$arrData = array(
	'order_number' => '93963928111111111',
	'st_code' => '10000-0000-00000',
	'memo1' => '��ѥ�����������',
	'user_mail_add' => 'naka@lockon.co.jp',
	'item_name' => '�ץ��',
	'contract_code' => '13094800',
	'user_name' => 'naka',
	'process_code' => '1',
	'mission_code' => '1',
	'item_price' => '34800',
	'xml' => '1',		
	'item_code' => 'abc12345',
	'memo2' => '',
	'user_id' => 'ktest'
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

$err_code = lfGetXMLValue($arrVal,'RESULT','ERR_CODE');

if($err_code != "") {
	$err_detail = lfGetXMLValue($arrVal,'RESULT','ERR_DETAIL');
	print($err_detail);
} else {
	$url = lfGetXMLValue($arrVal,'RESULT','REDIRECT');
	header("Location: " . $url);	
}


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

*/
?>
