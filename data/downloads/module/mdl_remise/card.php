<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");
require_once(MODULE_PATH . "mdl_remise/mdl_remise.inc");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = MODULE_PATH . 'mdl_remise/card.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_title = "�����ɷ��";
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
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

// �桼����ˡ���ID�μ����ȹ������֤�������������å�
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// �����Ƚ��׽���
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);

// �������ơ��֥���ɹ�
$arrData = sfGetOrderTemp($uniqid);

// �����Ƚ��פ򸵤˺ǽ��׻�
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

$sql = "SELECT module_id, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 ".
	"FROM dtb_payment WHERE payment_id = ? ";

// ��ʧ����������
$arrPayment = $objQuery->getall($sql, array($arrData["payment_id"]));

// ��������Ƚ��
switch($_POST["mode"]){
	//���
	case 'return':
		// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
		$objSiteSess->setRegistFlag();
		// ��ǧ�ڡ����ذ�ư
		header("Location: " . URL_SHOP_CONFIRM);
		exit;
		break;
}

// ��ߡ���������ֿ������ä����
if (isset($_POST["X-R_CODE"])) {
	
	$err_detail = "";
	
	// �̿������顼
	if ($_POST["X-R_CODE"] != $arrRemiseErrorWord["OK"]) {
		$err_detail = $_POST["X-R_CODE"];
		sfDispSiteError(FREE_ERROR_MSG, "", false, "����������˰ʲ��Υ��顼��ȯ�����ޤ�����<br /><br /><br />��" . $err_detail);
		
	// �̿��������
	} else {
		
		$log_path = DATA_PATH . "logs/remise_card_finish.log";
		gfPrintLog("remise card finish start----------", $log_path);
		foreach($_POST as $key => $val){
			gfPrintLog( "\t" . $key . " => " . $val, $log_path);
		}
		gfPrintLog("remise card finish end  ----------", $log_path);
	
		// ��ۤ������������å�
		if ($arrData["payment_total"] != $_POST["X-TOTAL"] && $arrData["credit_result"] != $_POST["X-TRANID"]) {
			sfDispSiteError(FREE_ERROR_MSG, "", false, "����������˰ʲ��Υ��顼��ȯ�����ޤ�����<br /><br /><br />�������ۤȻ�ʧ����ۤ��㤤�ޤ���");
		}
		
		// ����ʿ�ܤǤ��뤳�Ȥ�Ͽ���Ƥ���
		$objSiteSess->setRegistFlag();
		
		// POST�ǡ�������¸
		$arrVal["credit_result"] = $_POST["X-TRANID"];
		$arrVal["memo01"] = PAYMENT_CREDIT_ID;
		$arrVal["memo03"] = $arrPayment[0]["module_id"];
		$arrVal["memo04"] = $_POST["X-TRANID"];
		
		// �ȥ�󥶥�����󥳡���
		$arrMemo["trans_code"] = array("name"=>"Remise�ȥ�󥶥�����󥳡���", "value" => $_POST["X-TRANID"]);
		$arrVal["memo02"] = serialize($arrMemo);
		
		// ��������ǡ�������
		$arrModule['module_id'] = MDL_REMISE_ID;
		$arrModule['payment_total'] = $arrData["payment_total"];
		$arrModule['payment_id'] = PAYMENT_CREDIT_ID;
		$arrVal['memo05'] = serialize($arrModule);
		
		// �������ơ��֥�˹���
		sfRegistTempOrder($uniqid, $arrVal);

		// ��λ���̤�
		header("Location: " .  URL_SHOP_COMPLETE);
	}
}

// EC-CUBE¦��������URL
$retUrl = SITE_URL . 'shopping/load_payment_module.php?module_id=' . MDL_REMISE_ID;
$exitUrl = SITE_URL . 'shopping/load_payment_module.php';

$arrSendData = array(
	'SEND_URL' => $arrPayment[0]["memo04"],	// ��³��URL
	'S_TORIHIKI_NO' => $arrData["order_id"],	// ���������ֹ�
	'MAIL' => $arrData["order_email"],			// �᡼�륢�ɥ쥹
	'AMOUNT' => $arrData["subtotal"],			// ���
	'TAX' => $arrData["deliv_fee"],				// ���� + ��
	'TOTAL' => $arrData["payment_total"],		// ��׶��
	'SHOPCO' => $arrPayment[0]["memo01"],		// Ź�ޥ�����
	'HOSTID' => $arrPayment[0]["memo02"],		// �ۥ���ID
	'JOB' => REMISE_PAYMENT_JOB_CODE,			// ����֥����� 
	'ITEM' => '0000120',						// ���ʥ�����(��ߡ�������)
	'RETURL' => $retUrl,						// ��λ����URL
	'NG_RETURL' => $retUrl,					// NG��λ����URL
	'EXITURL' => $exitUrl,						// �����URL
	'REMARKS3' => MDL_REMISE_POST_VALUE
);

// ��ʧ����ˡɽ������
$objFormParam->setValue("credit_method", $arrPayment[0]["memo08"]);
$objFormParam->splitParamCheckBoxes("credit_method");
$arrUseCreMet = $objFormParam->getValue("credit_method");

foreach($arrUseCreMet as $key => $val) {
	$arrCreMet[$val] = $arrCredit[$val];
}

// ʬ����ɽ������(�������̤Ǥ�����������ޤ�ɽ��)
foreach($arrCreditDivide as $key => $val) {
	if ($arrPayment[0]["memo09"] >= $val) {
		$arrCreDiv[$val] = $val;
	}
}

$objPage->arrCreMet = $arrCreMet;
$objPage->arrCreDiv = $arrCreDiv;
$objPage->arrSendData = $arrSendData;

$objView->assignobj($objPage);

// �������Ƥ�SJIS�ˤ���(��ߡ����б�)
mb_http_output(REMISE_SEND_ENCODE);
$objView->display(MODULE_PATH . "mdl_remise/card.tpl");

//---------------------------------------------------------------------------------------------------------------------------------------------------------

//�ѥ�᡼���ν����
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("��ʧ����ˡ", "credit_method");
}

?>