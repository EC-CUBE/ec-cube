<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = MODULE_PATH . "mdl_epsilon/convenience.tpl";
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');		
	}
}

global $arrConvenience;
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

// ��ɽ���ʾ���
$arrMainProduct = $objPage->arrProductsClass[0];

// ��ʧ����������
$arrPayment = $objQuery->getall("SELECT module_id, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ? ", array($arrData["payment_id"]));

// �ǡ���������CGI
$order_url = $arrPayment[0]["memo02"];

// trans_code���ͤ������ġ����ｪλ�ΤȤ��ϥ���������ǧ��Ԥ���
if($_GET["trans_code"] != ""){
	
	// ����ʿ�ܤǤ��뤳�Ȥ�Ͽ���Ƥ���
	$objSiteSess->setRegistFlag();
	
	// GET�ǡ�������¸
	//$arrVal["credit_result"] = $_GET["result"];
	$arrVal["memo01"] = PAYMENT_CONVENIENCE_ID;
	$arrVal["memo03"] = $arrPayment[0]["module_id"];
	
	// �ȥ�󥶥�����󥳡���
	$arrMemo["trans_code"] = array("name"=>"Epsilon�ȥ�󥶥�����󥳡���", "value" => $_GET["trans_code"]);
	$arrVal["memo02"] = serialize($arrMemo);

	// �������ơ��֥�˹���
	sfRegistTempOrder($uniqid, $arrVal);

	// ��λ���̤�
	header("Location: " .  URL_SHOP_COMPLETE);
}

switch($_POST["mode"]){
	//���
	case 'return':
		// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
		$objSiteSess->setRegistFlag();
		// ��ǧ�ڡ����ذ�ư
		header("Location: " . URL_SHOP_CONFIRM);
		exit;
		break;

	case "send":
		$arrErr = array();
		$arrErr = $objFormParam->checkError();
		$objPage->arrErr = $arrErr;
	
		if(count($arrErr) <= 0){
			// �����ǡ�������
			$arrSendData = array(
				'contract_code' => $arrPayment[0]["memo01"],						// ���󥳡���
				'user_id' => $arrData["customer_id"],								// �桼��ID
				'user_name' => $arrData["order_name01"].$arrData["order_name02"],	// �桼��̾
				'user_mail_add' => $arrData["order_email"],							// �᡼�륢�ɥ쥹
				'order_number' => $arrData["order_id"],								// ���������ֹ�
				'item_code' => $arrMainProduct["product_code"],						// ���ʥ�����(��ɽ)
				'item_name' => $arrMainProduct["name"],								// ����̾(��ɽ)
				'item_price' => $arrData["payment_total"],							// ���ʲ���(�ǹ������)
				'st_code' => $arrPayment[0]["memo04"],								// ��Ѷ�ʬ
				'mission_code' => '1',												// �ݶ��ʬ(����)
				'process_code' => '1',												// ������ʬ(����)
				'xml' => '1',														// ��������(����)
				
				'conveni_code' => $_POST["convenience"],							// ����ӥ˥�����
				'user_tel' => $_POST["order_tel01"].$_POST["order_tel02"].$_POST["order_tel03"],	// �����ֹ�
				'user_name_kana' => $_POST["order_kana01"].$_POST["order_kana02"],					// ��̾(����)
				'haraikomi_mail' => 1,												// ʧ���᡼��(�������ʤ�)
				
				'memo1' => ECCUBE_PAYMENT,											// ͽ��01
				'memo2' => ''														// ͽ��02
			);
			
			// �ǡ�������
			$arrXML = sfPostPaymentData($order_url, $arrSendData);
			
			// ���顼�����뤫�����å�����
			$err_code = sfGetXMLValue($arrVal,'RESULT','ERR_CODE');
			
			if($err_code != "") {
				$err_detail = sfGetXMLValue($arrVal,'RESULT','ERR_DETAIL');
				sfDispSiteError(FREE_ERROR_MSG, "", false, "����������˰ʲ��Υ��顼��ȯ�����ޤ�����<br /><br /><br />��" . $err_detail);
			} else {
				// ����ʿ�ܤǤ��뤳�Ȥ�Ͽ���Ƥ���
				$objSiteSess->setRegistFlag();
				
				$conveni_code = sfGetXMLValue($arrVal,'RESULT','CONVENI_CODE');	// ����ӥ˥�����
				$receipt_no   = sfGetXMLValue($arrVal,'RESULT','RECEIPT_NO');	// ʧ��ɼ�ֹ�
				$payment_url = sfGetXMLValue($arrVal,'RESULT','HARAIKOMI_URL');	// ʧ��ɼURL(PC)
				$company_code = sfGetXMLValue($arrVal,'RESULT','KIGYOU_CODE');	// ��ȥ�����
				$order_no = sfGetXMLValue($arrVal,'RESULT','ORDER_NUMBER');		// �����ֹ�
				$tel = $_POST["order_tel01"]."-".$_POST["order_tel02"]."-".$_POST["order_tel03"];	// �����ֹ�
				$payment_limit = sfGetXMLValue($arrVal,'RESULT','CONVENI_LIMIT');	// ��ʧ����
				
				//����ӥˤμ���
				switch($conveni_code) {
				//���֥󥤥�֥�
				case '11':
					$arrRet['cv_type'] = $conveni_code;			//����ӥˤμ���
					$arrRet['cv_payment_url'] = $payment_url;	//ʧ��ɼURL(PC)
					$arrRet['cv_receipt_no'] = $receipt_no;		//ʧ��ɼ�ֹ�
					break;
				//�ե��ߥ꡼�ޡ���
				case '21':
					$arrRet['cv_type'] = $conveni_code;			//����ӥˤμ���
					$arrRet['cv_company_code'] = $company_code;	//��ȥ�����
					$arrRet['cv_order_no'] = $receipt_no;		//�����ֹ�
					break;
				//������
				case '31':
					$arrRet['cv_type'] = $conveni_code;			//����ӥˤμ���
					$arrRet['cv_receipt_no'] = $receipt_no;		//ʧ��ɼ�ֹ�
					$arrRet['cv_tel'] = $tel;					//�����ֹ�
					break;
				//���������ޡ���
				case '32':
					$arrRet['cv_type'] = $conveni_code;			//����ӥˤμ���
					$arrRet['cv_receipt_no'] = $receipt_no;		//ʧ��ɼ�ֹ�
					$arrRet['cv_tel'] = $tel;					//�����ֹ�
					break;
				//�ߥ˥��ȥå�
				case '33':
					$arrRet['cv_type'] = $conveni_code;			//����ӥˤμ���
					$arrRet['cv_payment_url'] = $payment_url;	//ʧ��ɼURL
					break;
				//�ǥ��꡼��ޥ���
				case '34':
					$arrRet['cv_type'] = $conveni_code;			//����ӥˤμ���
					$arrRet['cv_payment_url'] = $payment_url;	//ʧ��ɼURL
					break;
				}
				
				//��ʧ����
				$arrRet['cv_payment_limit'] = $payment_limit;
				//����ӥ˷�Ѿ�����Ǽ
				$sqlval['conveni_data'] = serialize($arrRet);
				$sqlval['memo02'] = serialize($arrRet);
	
				// �������ơ��֥�˹���
				sfRegistTempOrder($uniqid, $sqlval);
					
				header("Location: " . URL_SHOP_COMPLETE);
			}
		}
		break;
		
	default:
		$objFormParam->setParam($arrData);
		break;
}

// ���Ѳ�ǽ����ӥ�
$objFormParam->setValue("convenience", $arrPayment[0]["memo05"]);
$objFormParam->splitParamCheckBoxes("convenience");
$arrUseConv = $objFormParam->getValue("convenience");
foreach($arrUseConv as $key => $val){
	$arrConv[$val] = $arrConvenience[$val];
}
$objPage->arrConv = $arrConv;

$objPage->arrForm =$objFormParam->getHashArray();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
//�ѥ�᡼���ν����
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("����ӥˤμ���", "convenience", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��̾��(����)", "order_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��̾��(�ᥤ)", "order_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�������ֹ�1", "order_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�������ֹ�2", "order_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�������ֹ�3", "order_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
}
	

?>