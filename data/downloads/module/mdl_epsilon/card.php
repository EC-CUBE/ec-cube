<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");

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
$objCampaignSess = new SC_CampaignSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// �桼����ˡ���ID�μ����ȹ������֤�������������å�
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// �����Ƚ��׽���
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);

// �������ơ��֥���ɹ�
$arrData = sfGetOrderTemp($uniqid);

// �����Ƚ��פ򸵤˺ǽ��׻�
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

// ��ɽ���ʾ���
$arrMainProduct = $objPage->arrProductsClass[0];

// ��ʧ����������
$arrPayment = $objQuery->getall("SELECT module_id, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ? ", array($arrData["payment_id"]));

// trans_code���ͤ������ġ����ｪλ�ΤȤ��ϥ���������ǧ��Ԥ���
if($_GET["result"] == "1"){
	
	// ����ʿ�ܤǤ��뤳�Ȥ�Ͽ���Ƥ���
	$objSiteSess->setRegistFlag();
	
	// GET�ǡ�������¸
	$arrVal["credit_result"] = $_GET["result"];
	$arrVal["memo01"] = PAYMENT_CREDIT_ID;
	$arrVal["memo03"] = $arrPayment[0]["module_id"];
	$sqlval["memo04"] = sfGetXMLValue($arrXML,'RESULT','TRANS_CODE');

	// �ȥ�󥶥�����󥳡���
	$arrMemo["trans_code"] = array("name"=>"Epsilon�ȥ�󥶥�����󥳡���", "value" => $_GET["trans_code"]);
	$arrVal["memo02"] = serialize($arrMemo);

	// ��������ǡ�������
	$arrModule['module_id'] = MDL_EPSILON_ID;
	$arrModule['payment_total'] = $arrPayment[0]["payment_total"];
	$arrModule['payment_id'] = PAYMENT_CREDIT_ID;
	$arrVal["memo05"] = serialize($arrModule);

	// �������ơ��֥�˹���
	sfRegistTempOrder($uniqid, $arrVal);

	// ��λ���̤�
	if (is_callable(GC_MobileUserAgent) && GC_MobileUserAgent::isMobile()) {
		header("Location: " .  gfAddSessionId(URL_SHOP_COMPLETE));
	} else {
		header("Location: " .  URL_SHOP_COMPLETE);
	}
}

// �ǡ�������
lfSendCredit($arrData, $arrPayment, $arrMainProduct);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

// �ǡ�����������
function lfSendCredit($arrData, $arrPayment, $arrMainProduct, $again = true){
	global $objSiteSess;
	global $objCampaignSess;
	
	// �ǡ���������CGI
	$order_url = $arrPayment[0]["memo02"];

	// �����ΤȤ��� user_id �� not_member������
	($arrData["customer_id"] == 0) ? $user_id = "not_member" : $user_id = $arrData["customer_id"];	
	
	// �����ǡ�������
	$item_name = $arrMainProduct["name"] . "��" . $arrMainProduct["quantity"] . "�� (��ɽ)";
	$arrSendData = array(
		'contract_code' => $arrPayment[0]["memo01"],						// ���󥳡���
		'user_id' => $user_id ,												// �桼��ID
		'user_name' => $arrData["order_name01"].$arrData["order_name02"],	// �桼��̾
		'user_mail_add' => $arrData["order_email"],							// �᡼�륢�ɥ쥹
		'order_number' => $arrData["order_id"],								// ���������ֹ�
		'item_code' => $arrMainProduct["product_code"],						// ���ʥ�����(��ɽ)
		'item_name' => $item_name,											// ����̾(��ɽ)
		'item_price' => $arrData["payment_total"],							// ���ʲ���(�ǹ������)
		'st_code' => $arrPayment[0]["memo04"],								// ��Ѷ�ʬ
		'mission_code' => '1',												// �ݶ��ʬ(����)
		'process_code' => '1',												// ������ʬ(����)
		'xml' => '1',														// ��������(����)
		'memo1' => "",														// ͽ��01
		'memo2' => ECCUBE_PAYMENT . "_" . date("YmdHis"),					// ͽ��02
	);

	// �ǡ�������
	$arrXML = sfPostPaymentData($order_url, $arrSendData);
	
	// ���顼�����뤫�����å�����
	$err_code = sfGetXMLValue($arrXML,'RESULT','ERR_CODE');
	
	if($err_code != "") {
		$err_detail = sfGetXMLValue($arrXML,'RESULT','ERR_DETAIL');
		
		// ��Ѷ�ʬ���顼�ξ��ˤ� VISA,MASTER �ΤߤǺ��������ߤ�
		if($err_code == "909" and $again){
			$arrPayment[0]["memo04"] = "10000-0000-00000";
			lfSendCredit($arrData, $arrPayment, $arrMainProduct, false);
		}
		sfDispSiteError(FREE_ERROR_MSG, "", true, "����������˰ʲ��Υ��顼��ȯ�����ޤ�����<br /><br /><br />��" . $err_detail . "<br /><br /><br />���μ�³����̵���Ȥʤ�ޤ�����");
	} else {
		// ����ʿ�ܤǤ��뤳�Ȥ�Ͽ���Ƥ���
		$objSiteSess->setRegistFlag();
		
		// ����ü���ξ��ϡ����å����ID�����������ֹ桦��äƤ���URL����¸���Ƥ�����
		if (is_callable(GC_MobileUserAgent) && GC_MobileUserAgent::isMobile()) {
			sfMobileSetExtSessionId('order_number', $arrData['order_id'], 'shopping/load_payment_module.php');
			sfMobileSetExtSessionId('order_number', $arrData['order_id'], 'shopping/confirm.php');
		}

		$url = sfGetXMLValue($arrXML,'RESULT','REDIRECT');
		header("Location: " . $url);
	}
}

?>
