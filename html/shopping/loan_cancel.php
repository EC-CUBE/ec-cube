<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'shopping/loan.tpl';
		$this->tpl_css = URL_DIR.'css/layout/shopping/pay.css';
		// �ۡ��ॢ�ɥ쥹
		$this->tpl_homeaddr = CF_HOMEADDR;
		// ����ߥ졼�����ƤӽФ�
		$this->tpl_simulate = CF_SIMULATE;
		// ����Ź������
		$this->tpl_storecode = CF_STORECODE;
		// �����
		$this->tpl_returnurl = CF_RETURNURL;
		// �ƤӽФ���ʬ(0:����ߥ졼�����Τߡ�1:����ߥ졼�����+����)
		$this->tpl_continue = CF_CONTINUE;
		// ��̵̳ͭ��ʬ(0:̵��1:ͭ)
		$this->tpl_labor = CF_LABOR;
		// ��̱���(1:��̤��ꡢ2:��̤ʤ�)
		$this->tpl_result = CF_RESULT;
		// ����󥻥�URL
		$this->tpl_cancelurl = CF_CANCELURL;
		
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
$objCampaignSess = new SC_CampaignSession();
$objCustomer = new SC_Customer();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// ��ʸ���ID�μ���
$uniqid = $objSiteSess->getUniqId();

// �����Ѥ�����ͤ�����å����롣
if($_GET['tranno'] == $uniqid) {
	// �����Ѽ����ֹ��DB�˽񤭹���
	$sqlval['loan_result'] = $_GET['receiptno'];
	$objQuery = new SC_Query();
	$objQuery->update("dtb_order_temp", $sqlval, "order_temp_id = ?", array($uniqid));
	
	$objPage->tpl_message = "����åԥ󥰥���μ�³���ϡ�����󥻥뤵��ޤ�����";	
}

switch($_POST['mode']) {
// ���Υڡ��������
case 'return':
	// ����ʿ�ܤǤ��뤳�Ȥ�Ͽ���Ƥ���
	$objSiteSess->setRegistFlag();
	header("Location: " . URL_SHOP_CONFIRM);
	exit;
	break;
default:
	break;
}

// �����Ƚ��׽���
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
// �������ơ��֥���ɹ�
$arrData = sfGetOrderTemp($uniqid);
// �����Ƚ��פ򸵤˺ǽ��׻�
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

// ��ʧ�����
$objPage->tpl_amount = $arrData['payment_total'];
// �����ֹ�
$objPage->tpl_tranno = $uniqid;
// ���ܾ�����Ϥ�
$objPage->arrInfo = $arrInfo;

$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
$objCampaignSess->pageView($objView);
//--------------------------------------------------------------------------------------------------------------------------
?>
