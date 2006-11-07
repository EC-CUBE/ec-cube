<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

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
$objSiteInfo = $objView->objSiteInfo;
$objSiteSess = new SC_SiteSession();
$objCustomer = new SC_Customer();
$arrInfo = $objSiteInfo->data;
$objQuery = new SC_Query();

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

// ��������ξ��ʤ�����ڤ�����å�
$objCartSess->chkSoldOut($objCartSess->getCartList());

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
	// ���λ����ǥ�������ID����ݤ��Ƥ����ʥ��쥸�åȡ�����ӥ˷�Ѥ�ɬ�פʤ����
	if($arrData["order_id"] == ""){
		// postgresql��mysql�Ȥǽ�����ʬ����
		if (DB_TYPE == "pgsql") {
			$order_id = $objQuery->nextval("dtb_order","order_id");
			$objQuery->query("INSERT INTO dtb_order (order_id, customer_id, create_date, del_flg) VALUES (?, ?, now(), 1)", array($order_id, $arrData["customer_id"]));
		}elseif (DB_TYPE == "mysql") {
			$objQuery->query("INSERT INTO dtb_order (customer_id, create_date, del_flg) VALUES (?, now(), 1)", array($arrData["customer_id"]));
			$order_id = $objQuery->nextval("dtb_order","order_id");
		}
		$arrData["order_id"] = $order_id;
	}
	sfprintr($_SESSION);


	// ���׷�̤�������ơ��֥��ȿ��
	sfRegistTempOrder($uniqid, $arrData);
	// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
	$objSiteSess->setRegistFlag();
	
	// ��Ѷ�ʬ���������
	if(sfColumnExists("dtb_payment", "memo01")){
		$sql = "SELECT memo03 FROM dtb_payment WHERE payment_id = ?";
		$arrPayment = $objQuery->getall($sql, array($arrData['payment_id']));
	}
	
	// �����ˡ�ˤ���������
	switch($arrPayment[0]["memo03"]) {
	case PAYMENT_CREDIT_ID:
	case PAYMENT_CONVENIENCE_ID:
		//header("Location: " . URL_SHOP_CREDIT);
		//header("Location: " . URL_SHOP_CONVENIENCE);
		$_SESSION["payment_id"] = $arrData['payment_id'];
		header("Location: " . URL_SHOP_MODULE);
		break;
/*
	case PAYMENT_LOAN_ID:
		header("Location: " . URL_SHOP_LOAN);
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