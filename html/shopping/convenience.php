<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = "shopping/convenience.tpl";
		$this->tpl_css = URL_DIR.'css/layout/shopping/pay.css';
		global $arrCONVENIENCE;
		$this->arrCONVENIENCE = $arrCONVENIENCE;
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');		
	}
}

$objPage = new LC_Page;
$objView = new SC_SiteView;
$objSiteSess = new SC_SiteSession;
$objCartSess = new SC_CartSession;
$objCampaignSess = new SC_CampaignSession();
$objSiteInfo = $objView->objSiteInfo;
$objCustomer = new SC_Customer;

$arrInfo = $objSiteInfo->data;

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

// ������������������Ƚ��
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

//����ӥˤμ���ǽ����ե�������ڤ��ؤ���
switch($_POST['mode']) {
//��λ
case 'complete':
	//���顼�����å�
	$objPage->arrErr = lfCheckError();
	if($objPage->arrErr == "") {
		// �ޡ������Ⱦ�������ե�����򥤥󥯥롼��
		//require("merchant.ini");
		// ��ѽ����ѥå������򥤥󥯥롼��
		require_once(DATA_PATH . "vtcvsmdk/mdk/lib/BSCVS/Transaction.php");
		require_once(DATA_PATH . "vtcvsmdk/mdk/lib/BSCVS/Config.php");
		require_once(DATA_PATH . "vtcvsmdk/mdk/lib/BSCVS/Log.php");
	
		// �ȥ�󥶥�����󥤥󥹥��󥹤����
		$objTran = new Transaction;
		
		// ����ե����� cvsgwlib.conf �ˤ�ꥤ�󥹥��󥹤�����
		$objTran->setServer(DATA_PATH . "vtcvsmdk/mdk/conf/cvsgwlib.conf");
		
		// �����Ƚ��׽���
		$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
		// �������ơ��֥���ɹ�
		$arrData = sfGetOrderTemp($uniqid);
		// �����Ƚ��פ򸵤˺ǽ��׻�
		$arrPrice = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo, $objCustomer);
		
		// �����ϥ��󥹥��󥹤����
		$logger = $objTran->getLogger();
		
		// ������(��������)
		$logger->logprint('DEBUG', '<<< ��ʧ��̲��̽�������... >>>');
		
		//����ӥˤμ��फ��CVS�����פ���ꤹ��
		switch($_POST['convenience']) {
		//���֥󥤥�֥�
		case '1':
			$cvs_type = '01';
			break;
		//�ե��ߥ꡼�ޡ���
		case '2':
			$cvs_type = '03';
			break;
		//��������K���󥯥�
		case '3':
			$cvs_type = '04';
			break;
		//����¾
		case '4':
		case '5':
			$cvs_type = '02';
			break;
		default:
			sfDispSiteError(PAGE_ERROR, "", "", "", $objCampaignSess);
			break;
		}
	
		//�ꥯ��������ʸ
		$arrRequest = array(
			// ��� ID
		    REQ_ORDER_ID => $uniqid,		
		    // CVS������
		    REQ_CVS_TYPE => $cvs_type,
		    // ���
		    REQ_AMOUNT => $arrPrice['payment_total'],
		    // ��ʧ����
		    REQ_PAY_LIMIT => lfGetPayLimit(),
		    // ��̾����ա��٥�ȥ�󥹥���ӥ˥����ȥ������� UTF-8 ��ʸ���Τߤ�
		    // �����դ��뤿�ᡢ�����ȥ�������³������ UTF-8 �����ɤ��Ѵ���
		    REQ_NAME1 => $objTran->jCode($arrData['order_name01'], ENCODE_UTF8),
		    REQ_NAME2 => $objTran->jCode($arrData['order_name02'], ENCODE_UTF8),
			REQ_KANA => $objTran->jCode($arrData['order_kana01'].$arrData['order_kana02'], ENCODE_UTF8),
		    // �����ֹ�
		    REQ_TEL_NO => $arrData['order_tel01']."-".$arrData['order_tel02']."-".$arrData['order_tel03']
		);

		//�٥�ȥ�󥹥���ӥ˥����ȥ������˥ꥯ��������ʸ���ꤲ�������̤��Ǽ
		$arrResult = $objTran->doTransaction(CMD_ENTRY, $arrRequest);
		//�������
		if($arrResult[RES_ACTION_CODE] = '010') {
			//����ӥˤμ���
			switch($_POST['convenience']) {
			//���֥󥤥�֥�
			case '1':
				$arrRet['cv_type'] = '1';										//����ӥˤμ���
				$arrRet['cv_payment_url'] = $arrResult[RES_HARAIKOMI_URL];		//ʧ��ɼURL(PC)
				$arrRet['cv_receipt_no'] = $arrResult[RES_RECEIPT_NO];			//ʧ��ɼ�ֹ�
				break;
			//�ե��ߥ꡼�ޡ���
			case '2':
				$company_code = substr($arrResult[RES_RECEIPT_NO], 0, 5);
				$order_no = substr($arrResult[RES_RECEIPT_NO], 6, 12);
				$arrRet['cv_type'] = '2';						//����ӥˤμ���
				$arrRet['cv_company_code'] = $company_code;	//��ȥ�����
				$arrRet['cv_order_no'] = $order_no;			//�����ֹ�
				break;
			//��������K���󥯥�
			case '3':
				$mobile_url = preg_replace("/https:\/\/.+?\/JLPcon/","https://w2.kessai.info/JLM/JLMcon", $arrResult[RES_HARAIKOMI_URL]);
				$arrRet['cv_type'] = '3';										//����ӥˤμ���
				$arrRet['cv_payment_url'] = $arrResult[RES_HARAIKOMI_URL];		//ʧ��ɼURL
				$arrRet['cv_payment_mobile_url'] = $mobile_url;					//ʧ��ɼURL(��Х���)
				break;
			//�����󡢥��������ޡ���
			case '4':
				$arrRet['cv_type'] = '4';									//����ӥˤμ���
				$arrRet['cv_receipt_no'] = $arrResult[RES_RECEIPT_NO];		//ʧ��ɼ�ֹ�
				break;
			//�ߥ˥��ȥåס��ǥ��꡼��ޥ�������ޥ����ǥ��꡼���ȥ�
			case '5':
				$arrRet['cv_type'] = '5';										//����ӥˤμ���
				$arrRet['cv_payment_url'] = $arrResult[RES_HARAIKOMI_URL];		//ʧ��ɼURL(PC)
				break;
			}
			//��ʧ����
			$arrRet['cv_payment_limit'] = lfGetPayLimit();
			//����ӥ˷�Ѿ�����Ǽ
			$sqlval['conveni_data'] = serialize($arrRet);
			$objQuery = new SC_Query;
			$objQuery->update("dtb_order_temp", $sqlval, "order_temp_id = ? ", array($uniqid));
			// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
			$objSiteSess->setRegistFlag();
			//������λ�ڡ�����
			header("Location: " . URL_SHOP_COMPLETE);
		//����
		} else {
			$objPage->arrErr = '���顼��ȯ�����ޤ�����';
		}
		
		# ������(�����ޤ�)
		$logger->logprint('DEBUG', '<<< ��ʧ��̲��̽�����λ. >>>');
	
	}
	break;
//���
case 'return':
	// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
	$objSiteSess->setRegistFlag();
	// ��ǧ�ڡ����ذ�ư
	header("Location: " . URL_SHOP_CONFIRM);
	exit;
	break;
}

$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
$objCampaignSess->pageView($objView);

//-------------------------------------------------------------------------------------------------------------

//��ʧ���¤�����
function lfGetPayLimit() {
    $date = sprintf("%10s",
                    date("Y/m/d",mktime(0,0,0,date("m"),
                    date("d")+CV_PAYMENT_LIMIT,date("Y"))));
    return $date;
}

//�ѥ�᡼���ν����
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("����ӥˤμ���", "convenience", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
}
	
// �������ƤΥ����å�
function lfCheckError() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	return $objErr->arrErr;
}

?>