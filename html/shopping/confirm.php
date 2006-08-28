<?php

require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	var $tpl_total_deliv_fee;
	function LC_Page() {
		$this->tpl_mainpage = 'shopping/confirm.tpl';
		$this->tpl_css = '/css/layout/shopping/confirm.css';
		$this->tpl_title = "���������ƤΤ���ǧ";
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrSex;
		$this->arrSex = $arrSex;
		global $arrMAILMAGATYPE;
		$this->arrMAILMAGATYPE = $arrMAILMAGATYPE;
		global $arrReminder;
		$this->arrReminder = $arrReminder;
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
$objCartSess = new SC_CartSession();
$objSiteInfo = new SC_SiteInfo();
$objSiteSess = new SC_SiteSession();
$objCustomer = new SC_Customer();
$arrInfo = $objSiteInfo->data;

// ���Υڡ�������������Ͽ��³�����Ԥ�줿��Ͽ�����뤫Ƚ��
sfIsPrePage($objSiteSess);

// �桼����ˡ���ID�μ����ȹ������֤�������������å�
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);
$objPage->tpl_uniqid = $uniqid;

// �����Ƚ��׽���
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
// �������ơ��֥���ɹ�
$arrData = sfGetOrderTemp($uniqid);
// �����Ƚ��פ򸵤˺ǽ��׻�
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo, $objCustomer);

// �������������å�
if($objCustomer->isLoginSuccess()) {
	$objPage->tpl_login = '1';
	$objPage->tpl_user_point = $objCustomer->getValue('point');
}

switch($_POST['mode']) {
// ���Υڡ��������
case 'return':
	// ����ʿ�ܤǤ��뤳�Ȥ�Ͽ���Ƥ���
	$objSiteSess->setRegistFlag();
	header("Location: " . URL_SHOP_PAYMENT);
	exit;
	break;
case 'confirm':
	// ���׷�̤�������ơ��֥��ȿ��
	sfRegistTempOrder($uniqid, $arrData);
	// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
	$objSiteSess->setRegistFlag();
	
	// �����ˡ�ˤ���������
	switch($arrData['payment_id']) {
/* ���쥸�åȡ����󡢥���ӥ˷�Ѥϼ�����ȯ
	case PAYMENT_CREDIT_ID:
		header("Location: " . URL_SHOP_CREDIT);
		break;
	case PAYMENT_LOAN_ID:
		header("Location: " . URL_SHOP_LOAN);
		break;
	case PAYMENT_CONVENIENCE_ID:
		header("Location: " . URL_SHOP_CONVENIENCE);
		break;
*/
	default:
		header("Location: " . URL_SHOP_COMPLETE);
		break;
	}
	break;
default:
	break;
}

$objPage->arrData = $arrData;
$objPage->arrInfo = $arrInfo;
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//--------------------------------------------------------------------------------------------------------------------------
?>