<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
		$this->tpl_css = URL_DIR.'css/layout/shopping/confirm.css';
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
$objCampaignSess = new SC_CampaignSession();
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
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo, $objCustomer, $objCampaignSess);
// �����ڡ��󤫤�����ܤ�������̵�����ä����ν���
if($objCampaignSess->getIsCampaign()) {
	$deliv_free_flg = $objQuery->get("dtb_campaign", "deliv_free_flg", "campaign_id = ?", array($objCampaignSess->getCampaignId()));
	// ����̵�������ꤵ��Ƥ������
	if($deliv_free_flg) {
		$arrData['payment_total'] -= $arrData['deliv_fee'];
		$arrData['deliv_fee'] = 0;
	}
}


// ��������ξ��ʤ�����ڤ�����å�
$objCartSess->chkSoldOut($objCartSess->getCartList());

// �������������å�
if($objCustomer->isLoginSuccess()) {
	$objPage->tpl_login = '1';
	$objPage->tpl_user_point = $objCustomer->getValue('point');
}

// ��Ѷ�ʬ���������
$payment_type = "";
$module_id = "";
if(sfColumnExists("dtb_payment", "memo01")){
	// module_id���ͤ����äƤ�����ˤϡ��⥸�塼���ɲä��줿��ΤȤߤʤ�
	$sql = "SELECT module_id,memo03 FROM dtb_payment WHERE payment_id = ?";
	$arrPayment = $objQuery->getall($sql, array($arrData['payment_id']));
	$payment_type = $arrPayment[0]["memo03"];
    $module_id =  $arrPayment[0]["module_id"];
}
$objPage->payment_type = $payment_type;



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
	// postgresql��mysql�Ȥǽ�����ʬ����
	if (DB_TYPE == "pgsql") {
		$order_id = $objQuery->nextval("dtb_order","order_id");
	}elseif (DB_TYPE == "mysql") {
		$order_id = $objQuery->get_auto_increment("dtb_order");
	}
	$arrData["order_id"] = $order_id;
    
    // ���å���������ݻ�
    $arrData['session'] = serialize($_SESSION);
    	
    // ���å���������ݻ�
    $arrData['session'] = serialize($_SESSION);
    
	// ���׷�̤�������ơ��֥��ȿ��
	sfRegistTempOrder($uniqid, $arrData);
	// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
	$objSiteSess->setRegistFlag();
	
	// �����ˡ�ˤ���������
	if(!empty($module_id)) {
		$_SESSION["payment_id"] = $arrData['payment_id'];
		header("Location: " . URL_SHOP_MODULE);
	}else{
		header("Location: " . URL_SHOP_COMPLETE);
	}
	break;
default:
	break;
}

$objPage->arrData = $arrData;
$objPage->arrInfo = $arrInfo;
$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
$objCampaignSess->pageView($objView);
//--------------------------------------------------------------------------------------------------------------------------
?>