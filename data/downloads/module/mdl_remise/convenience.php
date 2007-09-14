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
		$this->tpl_mainpage = MODULE_PATH . "mdl_remise/convenience.tpl";
		$this->tpl_title = "����ӥ˷��";
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');		
	}
}

global $arrConvenience;
global $arrConveni_message;

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();

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

// ��ɽ���ʾ���
$arrMainProduct = $objPage->arrProductsClass[0];

// ��ʧ����������
$arrPayment = $objQuery->getall("SELECT module_id, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ? ", array($arrData["payment_id"]));

// ��ǧ���̤����
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

		$log_path = DATA_PATH . "logs/remise_cv_finish.log";
		gfPrintLog("remise conveni finish start----------", $log_path);
		foreach($_POST as $key => $val){
			gfPrintLog( "\t" . $key . " => " . $val, $log_path);
		}
		gfPrintLog("remise conveni finish end  ----------", $log_path);

		// ��ۤ������������å�
		if ($arrData["payment_total"] != $_POST["X-TOTAL"]) {
			sfDispSiteError(FREE_ERROR_MSG, "", false, "����������˰ʲ��Υ��顼��ȯ�����ޤ�����<br /><br /><br />�������ۤȻ�ʧ����ۤ��㤤�ޤ���");
		}
		
		// ����ʿ�ܤǤ��뤳�Ȥ�Ͽ���Ƥ���
		$objSiteSess->setRegistFlag();
		
		// ��ߡ���������ͤμ���
		$job_id = lfSetConvMSG("�����ID(REMISE)", $_POST["X-JOB_ID"]);
		$payment_limit = lfSetConvMSG("��ʧ������", $_POST["X-PAYDATE"]);
		$conveni_type = lfSetConvMSG("��ʧ������ӥ�", $arrConvenience[$_POST["X-PAY_CSV"]]);
		$payment_total = lfSetConvMSG("��׶��", $_POST["X-TOTAL"]);
		$receipt_no = lfSetConvMSG("����ӥ�ʧ���Ф��ֹ�", $_POST["X-PAY_NO1"]);

		// �ե��ߥ꡼�ޡ��ȤΤ�URL���ʤ�
		if ($_POST["X-PAY_CSV"] != "D030") {
			$payment_url = lfSetConvMSG("����ӥ�ʧ���Ф�URL", $_POST["X-PAY_NO2"]);
		} else {
			$payment_url = lfSetConvMSG("��ʸ�ֹ�", $_POST["X-PAY_NO2"]);
		}
		
		$arrRet['cv_type'] = $conveni_type;				// ����ӥˤμ���
		$arrRet['cv_payment_url'] = $payment_url;		// ʧ��ɼURL(PC)
		$arrRet['cv_receipt_no'] = $receipt_no;			// ʧ��ɼ�ֹ�
		$arrRet['cv_payment_limit'] = $payment_limit;	// ��ʧ������
		$arrRet['title'] = lfSetConvMSG("����ӥ˷��", true);
		
		// ��������ǡ�������
		$arrModule['module_id'] = MDL_REMISE_ID;
		$arrModule['payment_total'] = $arrData["payment_total"];
		$arrModule['payment_id'] = PAYMENT_CONVENIENCE_ID;
		
		// ���ơ�������̤����ˤ���
		$sqlval['status'] = 2;
		
		// ����ӥ˷�Ѿ�����Ǽ
		$sqlval['conveni_data'] = serialize($arrRet);
		$sqlval['memo01'] = PAYMENT_CONVENIENCE_ID;
		$sqlval['memo02'] = serialize($arrRet);
		$sqlval['memo03'] = $arrPayment[0]["module_id"];
		$sqlval['memo04'] = $_POST["X-JOB_ID"];
		$sqlval['memo05'] = serialize($arrModule);

		// �������ơ��֥�˹���
		sfRegistTempOrder($uniqid, $sqlval);

		header("Location: " . URL_SHOP_COMPLETE);
	}
}

// EC-CUBE¦��������URL
$retUrl = SITE_URL . 'shopping/load_payment_module.php?module_id=' . MDL_REMISE_ID;
$exitUrl = SITE_URL . 'shopping/load_payment_module.php';
$tel = $arrData["order_tel01"].$arrData["order_tel02"].$arrData["order_tel03"];

// ��������
$pref = $arrPref[$arrData["order_pref"]];
$address1 = mb_convert_kana($arrData["order_addr01"], "ASKHV");
$address2 = mb_convert_kana($arrData["order_addr02"], "ASKHV");

// ����̾����(����7�ĤΤ��ᡢ�������Ȥ������Τǽ��Ϥ���)
$itemName = "�������";
$itemPlace = $arrData["payment_total"] - $arrData["deliv_fee"];

$arrSendData = array(
	'SEND_URL' => $arrPayment[0]["memo05"],		// ��³��URL
	'S_TORIHIKI_NO' => $arrData["order_id"],		// �����ֹ�(EC-CUBE)
	'MAIL' => $arrData["order_email"],				// �᡼�륢�ɥ쥹
	'NAME1' => $arrData["order_name01"],			// �桼����̾1
	'NAME2' => $arrData["order_name02"],			// �桼����̾2
	'KANA1' => $arrData["order_kana01"],			// �桼����̾(����)1
	'KANA2' => $arrData["order_kana02"],			// �桼����̾(����)2
	'TEL' => $tel,									// �����ֹ�
	'YUBIN1' => $arrData["order_zip01"],			// ͹���ֹ�1
	'YUBIN2' => $arrData["order_zip02"],			// ͹���ֹ�2
	'ADD1' => $pref,								// ����1
	'ADD2' => $address1,							// ����2
	'ADD3' => $address2,							// ����3
	'MSUM_01' => $arrData["subtotal"],				// ���
	'TAX' => $arrData["deliv_fee"],					// ���� + ��
	'TOTAL' => $arrData["payment_total"],			// ��׶��
	'SHOPCO' => $arrPayment[0]["memo01"],			// Ź�ޥ�����
	'HOSTID' => $arrPayment[0]["memo02"],			// �ۥ���ID
	'RETURL' => $retUrl,							// ��λ����URL
	'NG_RETURL' => $retUrl,						// NG��λ����URL
	'EXITURL' => $exitUrl,							// �����URL
	'MNAME_01' => $itemName,						// ����̾
	'MSUM_01' => $itemPlace,						// ���������(����+�ǰʳ�)
	'REMARKS3' => MDL_REMISE_POST_VALUE
);

$objPage->arrSendData = $arrSendData;
$objPage->arrForm =$objFormParam->getHashArray();
$objView->assignobj($objPage);

// �������Ƥ�SJIS�ˤ���(��ߡ����б�)
mb_http_output(REMISE_SEND_ENCODE);
$objView->display(MODULE_PATH . "mdl_remise/convenience.tpl");

//---------------------------------------------------------------------------------------------------------------------------------------------------------

function lfSetConvMSG($name, $value){
	return array("name" => $name, "value" => $value);
}

?>