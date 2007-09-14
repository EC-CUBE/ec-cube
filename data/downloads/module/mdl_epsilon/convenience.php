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
		if (is_callable(GC_MobileUserAgent) && GC_MobileUserAgent::isMobile()) {
			$this->tpl_mainpage = MODULE_PATH . "mdl_epsilon/convenience_mobile.tpl";
		} else {
			$this->tpl_mainpage = MODULE_PATH . "mdl_epsilon/convenience.tpl";
		}
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
$objCampaignSess = new SC_CampaignSession();
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

switch($_POST["mode"]){
	//���
	case 'return':
		// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
		$objSiteSess->setRegistFlag();
		// ��ǧ�ڡ����ذ�ư
		if (is_callable(GC_MobileUserAgent) && GC_MobileUserAgent::isMobile()) {
			header("Location: " . gfAddSessionId(URL_SHOP_CONFIRM));
		} else {
			header("Location: " . URL_SHOP_CONFIRM);
		}
		exit;
		break;

	case "send":
		$arrErr = array();
		$arrErr = $objFormParam->checkError();
		$objPage->arrErr = $arrErr;
		
		// �����ΤȤ��� user_id �� not_member������
		($arrData["customer_id"] == 0) ? $user_id = "not_member" : $user_id = $arrData["customer_id"];
		
		if(count($arrErr) <= 0){
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
				'conveni_code' => $_POST["convenience"],							// ����ӥ˥�����
				'user_tel' => $_POST["order_tel01"].$_POST["order_tel02"].$_POST["order_tel03"],	// �����ֹ�
				'user_name_kana' => $_POST["order_kana01"].$_POST["order_kana02"],					// ��̾(����)
				'haraikomi_mail' => 0,												// ʧ���᡼��(�������ʤ�)
				'memo1' => "",														// ͽ��01
				'memo2' => ECCUBE_PAYMENT . "_" . date("YmdHis"),					// ͽ��02
			);
			
			// �ǡ�������
			$arrXML = sfPostPaymentData($order_url, $arrSendData);
			
			// ���顼�����뤫�����å�����
			$err_code = sfGetXMLValue($arrXML,'RESULT','ERR_CODE');
			
			if($err_code != "") {
				$err_detail = sfGetXMLValue($arrXML,'RESULT','ERR_DETAIL');
				sfDispSiteError(FREE_ERROR_MSG, "", false, "����������˰ʲ��Υ��顼��ȯ�����ޤ�����<br /><br /><br />��" . $err_detail);
			} else {
				// ����ʿ�ܤǤ��뤳�Ȥ�Ͽ���Ƥ���
				$objSiteSess->setRegistFlag();

				$conveni_code = sfGetXMLValue($arrXML,'RESULT','CONVENI_CODE');	// ����ӥ˥�����
				$conveni_type = lfSetConvMSG("����ӥˤμ���",$arrConvenience[$conveni_code]);	// ����ӥˤμ���
				$receipt_no   = lfSetConvMSG("ʧ��ɼ�ֹ�",sfGetXMLValue($arrXML,'RESULT','RECEIPT_NO'));	// ʧ��ɼ�ֹ�
				$payment_url = lfSetConvMSG("ʧ��ɼURL",sfGetXMLValue($arrXML,'RESULT','HARAIKOMI_URL'));	// ʧ��ɼURL
				$company_code = lfSetConvMSG("��ȥ�����",sfGetXMLValue($arrXML,'RESULT','KIGYOU_CODE'));	// ��ȥ�����
				$order_no = lfSetConvMSG("�����ֹ�",sfGetXMLValue($arrXML,'RESULT','ORDER_NUMBER'));		// �����ֹ�
				$tel = lfSetConvMSG("�����ֹ�",$_POST["order_tel01"]."-".$_POST["order_tel02"]."-".$_POST["order_tel03"]);	// �����ֹ�
				$payment_limit = lfSetConvMSG("��ʧ����",sfGetXMLValue($arrXML,'RESULT','CONVENI_LIMIT'));	// ��ʧ����
				$trans_code =  sfGetXMLValue($arrXML,'RESULT','TRANS_CODE');	// �ȥ�󥶥�����󥳡���
				
				//����ӥˤμ���
				switch($conveni_code) {
				//���֥󥤥�֥�
				case '11':
					$arrRet['cv_type'] = $conveni_type;			//����ӥˤμ���
					$arrRet['cv_payment_url'] = $payment_url;	//ʧ��ɼURL(PC)
					$arrRet['cv_receipt_no'] = $receipt_no;		//ʧ��ɼ�ֹ�
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				//�ե��ߥ꡼�ޡ���
				case '21':
					$arrRet['cv_type'] = $conveni_type;			//����ӥˤμ���
					$arrRet['cv_company_code'] = $company_code;	//��ȥ�����
					$arrRet['cv_order_no'] = $receipt_no;		//�����ֹ�
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				//������
				case '31':
					$arrRet['cv_type'] = $conveni_type;			//����ӥˤμ���
					$arrRet['cv_receipt_no'] = $receipt_no;		//ʧ��ɼ�ֹ�
					$arrRet['cv_tel'] = $tel;					//�����ֹ�
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				//���������ޡ���
				case '32':
					$arrRet['cv_type'] =$conveni_type;			//����ӥˤμ���
					$arrRet['cv_receipt_no'] = $receipt_no;		//ʧ��ɼ�ֹ�
					$arrRet['cv_tel'] = $tel;					//�����ֹ�
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				//�ߥ˥��ȥå�
				case '33':
					$arrRet['cv_type'] = $conveni_type;			//����ӥˤμ���
					$arrRet['cv_payment_url'] = $payment_url;	//ʧ��ɼURL
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				//�ǥ��꡼��ޥ���
				case '34':
					$arrRet['cv_type'] = $conveni_type;			//����ӥˤμ���
					$arrRet['cv_payment_url'] = $payment_url;	//ʧ��ɼURL
					$arrRet['br1'] = lfSetConvMSG("","\n\n");
					$arrRet['cv_message'] = lfSetConvMSG("",$arrConveni_message[$conveni_code]);
					break;
				}

				//��ʧ����
				$arrRet['br2'] = lfSetConvMSG("","\n\n");
				$arrRet['cv_payment_limit'] = $payment_limit;
				$arrRet['br3'] = lfSetConvMSG("","\n\n");

				// �����ȥ�
				$arrRet['title'] = lfSetConvMSG("����ӥ˷��", true);

				// ��������ǡ�������
				$arrModule['module_id'] = MDL_EPSILON_ID;
				$arrModule['payment_total'] = $arrData["payment_total"];
				$arrModule['payment_id'] = PAYMENT_CONVENIENCE_ID;
				
				// ���ơ�������̤����ˤ���
				$sqlval['status'] = 2;

				//����ӥ˷�Ѿ�����Ǽ
				$sqlval['conveni_data'] = serialize($arrRet);
				$sqlval['memo01'] = PAYMENT_CONVENIENCE_ID;
				$sqlval['memo02'] = serialize($arrRet);
				$sqlval["memo03"] = $arrPayment[0]["module_id"];
				$sqlval["memo04"] = $trans_code;
				$sqlval['memo05'] = serialize($arrModule);

				// �������ơ��֥�˹���
				sfRegistTempOrder($uniqid, $sqlval);

				if (is_callable(GC_MobileUserAgent) && GC_MobileUserAgent::isMobile()) {
					header("Location: " . gfAddSessionId(URL_SHOP_COMPLETE));
				} else {
					header("Location: " . URL_SHOP_COMPLETE);
				}
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

// ������ۤ�30������礭����Х��֥󥤥�֥�������Բ�
if($arrData["payment_total"] > SEVEN_CHARGE_MAX){
	unset($arrConv[11]);
}

$objPage->arrConv = $arrConv;

$objPage->arrForm =$objFormParam->getHashArray();

$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
$objCampaignSess->pageView($objView);

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

function lfSetConvMSG($name, $value){
	return array("name" => $name, "value" => $value);
}

?>
