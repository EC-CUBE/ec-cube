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

// ���Υڡ�������������Ͽ��³�����Ԥ�줿��Ͽ�����뤫Ƚ��
sfIsPrePage($objSiteSess);

// ������������������Ƚ��
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// ���ץ����ڡ���������äƤ������˥��顼����򤹤뤿�ᡢnow_page �˳�ǧ���̤򥻥åȤ���
$_SESSION['site']['now_page'] = URL_DIR . "shopping/confirm.php";

// �����Ƚ��׽���
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
// �������ơ��֥���ɹ�
$arrData = sfGetOrderTemp($uniqid);
// �����Ƚ��פ򸵤˺ǽ��׻�
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

// ��ɽ���ʾ���
$arrMainProduct = $objPage->arrProductsClass[0];

// ��ʧ����������
$arrPayment = $objQuery->getall("SELECT memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ? ", array($arrData["payment_id"]));

sfprintr($arrPayment);

// �ǡ���������CGI
$order_url = $arrPayment[0]["memo02"];

// �����ǡ�������
$arrData = array(
	'contract_code' => $arrPayment[0]["memo01"],						// ���󥳡���
	'user_id' => $arrData["customer_id"],								// �桼��ID
	'user_name' => $arrData["order_name01"].$arrData["order_name02"],	// �桼��̾
	'user_mail_add' => $arrData["order_email"],							// �᡼�륢�ɥ쥹
	'order_number' => $arrData["order_id"],								// ���������ֹ�
	'item_code' => $arrMainProduct["product_code"],						// ���ʥ�����(��ɽ)
	'item_name' => $arrMainProduct["name"],								// ����̾(��ɽ)
	'item_price' => $arrData["payment_total"],							// ���ʲ���(�ǹ������)
	'st_code' => $arrPayment[0]["memo04"],								// ��Ѷ�ʬ
	'mission_code' => 'ddd1',												// �ݶ��ʬ(����)
	'process_code' => 'ddd1',												// ������ʬ(����)
	'xml' => '1',														// ��������(����)
	'memo1' => ECCUBE_PAYMENT,											// ͽ��01
	'memo2' => ''														// ͽ��02
);

// �������󥹥�������
$req = new HTTP_Request($order_url);
$req->setMethod(HTTP_REQUEST_METHOD_POST);

// POST�ǡ�������
$req->addPostDataArray($arrData);

// ���顼��̵����С�����������������
if (!PEAR::isError($req->sendRequest())) {
	$response = $req->getResponseBody();
} else {
	// ���顼���̤�ɽ�����롣
	sfDispSiteError(FREE_ERROR_MSG, "", true, "���쥸�åȥ����ɷ�ѽ�����˥��顼��ȯ�����ޤ�����<br>���μ�³����̵���Ȥʤ�ޤ�����");
}

// POST�ǡ������ꥢ
$req->clearPostData();

// XML�ѡ������������롣
$parser = xml_parser_create();

// ����ʸ�����ɤ����Ф���XML���ɤ߼��
xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);

// �����XML�Υǡ������Ǽ����
xml_parse_into_struct($parser,$response,$arrVal,$idx);

// ��������
xml_parser_free($parser);

// ���顼�����뤫�����å�����
$err_code = lfGetXMLValue($arrVal,'RESULT','ERR_CODE');

if($err_code != "") {
	$err_detail = lfGetXMLValue($arrVal,'RESULT','ERR_DETAIL');
	sfDispSiteError(FREE_ERROR_MSG, "", true, "���쥸�åȥ����ɷ�ѽ�����˰ʲ��Υ��顼��ȯ�����ޤ�����<br /><br /><br />��" . $err_detail . "<br /><br /><br />���μ�³����̵���Ȥʤ�ޤ�����");
} else {
	$url = lfGetXMLValue($arrVal,'RESULT','REDIRECT');
	header("Location: " . $url);	
}

//---------------------------------------------------------------------------------------------------------------------------------------------------------

/**************************************************************************************************************
 * �ؿ�̾	��lfGetXMLValue
 * ��������	��XML���������Ƥ��������
 * ����1	��$arrVal	������ Value�ǡ���
 * ����2	��$tag		������ Tag�ǡ���
 * ����3	��$att		������ �оݥ���̾
 * �����	���������
 **************************************************************************************************************/
function lfGetXMLValue($arrVal, $tag, $att) {
	$ret = "";
	foreach($arrVal as $array) {
		if($tag == $array['tag']) {
			if(!is_array($array['attributes'])) {
				continue;
			}
			foreach($array['attributes'] as $key => $val) {
				if($key == $att) {
					$ret = mb_convert_encoding(urldecode($val), 'EUC-JP', 'auto');
					break;
				}
			}			
		}
	}
	
	return $ret;
}

?>