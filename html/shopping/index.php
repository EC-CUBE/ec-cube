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
	var $tpl_login_email;
	function LC_Page() {
		$this->tpl_mainpage = 'shopping/index.tpl';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrSex;
		$this->arrSex = $arrSex;
		global $arrJob;
		$this->arrJob = $arrJob;
		$this->tpl_onload = 'fnCheckInputDeliv();';
		
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');				
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCampaignSess = new SC_CampaignSession();
$objCustomer = new SC_Customer();
$objCookie = new SC_Cookie();
$objFormParam = new SC_FormParam();			// �ե�������
lfInitParam();								// �ѥ�᡼������ν����
$objFormParam->setParam($_POST);			// POST�ͤμ���

// �桼����ˡ���ID�μ����ȹ������֤�������������å�
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

$objPage->tpl_uniqid = $uniqid;

// ����������å�
if($objCustomer->isLoginSuccess()) {
	// ���Ǥ˥����󤵤�Ƥ�����ϡ����Ϥ���������̤�ž��
	header("Location: ./deliv.php");
	exit;
}

switch($_POST['mode']) {
case 'nonmember_confirm':
	$objPage = lfSetNonMember($objPage);
	// ��break�ʤ�
case 'confirm':
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objFormParam->toLower('order_mail');
	$objFormParam->toLower('order_mail_check');
	
	$objPage->arrErr = lfCheckError();

	// ���ϥ��顼�ʤ�
	if(count($objPage->arrErr) == 0) {
		// DB�ؤΥǡ�����Ͽ
		lfRegistData($uniqid);
		
		// ���Ϥ���Υ��ԡ�
		lfCopyDeliv($uniqid, $_POST);
		
		// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
		$objSiteSess->setRegistFlag();
		// ����ʧ����ˡ����ڡ����ذ�ư
		header("Location: " . URL_SHOP_PAYMENT);
		exit;		
	}
	
	break;
// ���Υڡ��������
case 'return':
	// ��ǧ�ڡ����ذ�ư
	header("Location: " . URL_CART_TOP);
	exit;
	break;
case 'nonmember':
	$objPage = lfSetNonMember($objPage);
	// ��break�ʤ�
default:
	if($_GET['from'] == 'nonmember') {
		$objPage = lfSetNonMember($objPage);
	}
	// �桼����ˡ���ID�μ���
	$uniqid = $objSiteSess->getUniqId();
	$objQuery = new SC_Query();
	$where = "order_temp_id = ?";
	$arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($uniqid));
	// DB�ͤμ���
	$objFormParam->setParam($arrRet[0]);
	$objFormParam->setValue('order_email_check', $arrRet[0]['order_email']);
	$objFormParam->setDBDate($arrRet[0]['order_birth']);
	break;
}

// ���å���Ƚ��
$objPage->tpl_login_email = $objCookie->getCookie('login_email');
if($objPage->tpl_login_email != "") {
	$objPage->tpl_login_memory = "1";
}

// ���������դμ���
$objDate = new SC_Date(START_BIRTH_YEAR);
$objPage->arrYear = $objDate->getYear('', 1950);	//�����եץ����������
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

if($objPage->year == '') {
	$objPage->year = '----';
}

// �����ͤμ���
$objPage->arrForm = $objFormParam->getFormParamList();

if($objPage->arrForm['year']['value'] == ""){
	$objPage->arrForm['year']['value'] = '----';	
}

$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
$objCampaignSess->pageView($objView);
//--------------------------------------------------------------------------------------------------------------------------
/* �������ϥڡ����Υ��å� */
function lfSetNonMember($objPage) {
	$objPage->tpl_mainpage = 'shopping/nonmember_input.tpl';
	$objPage->tpl_css = array();
	$objPage->tpl_css[] = URL_DIR.'css/layout/login/nonmember.css';
	return $objPage;
}

/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("��̾��������", "order_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��̾����̾��", "order_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ�ʡʥ�����", "order_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ�ʡʥᥤ��", "order_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("͹���ֹ�1", "order_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("͹���ֹ�2", "order_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("��ƻ�ܸ�", "order_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("����1", "order_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����2", "order_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�����ֹ�1", "order_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�����ֹ�2", "order_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�����ֹ�3", "order_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX�ֹ�1", "order_fax01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX�ֹ�2", "order_fax02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("FAX�ֹ�3", "order_fax03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�᡼�륢�ɥ쥹", "order_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"));
	$objFormParam->addParam("�᡼�륢�ɥ쥹�ʳ�ǧ��", "order_email_check", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"), "", false);
	$objFormParam->addParam("ǯ", "year", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
	$objFormParam->addParam("��", "month", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
	$objFormParam->addParam("��", "day", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
	$objFormParam->addParam("����", "order_sex", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("����", "order_job", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�̤Τ��Ϥ���", "deliv_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��̾��������", "deliv_name01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��̾����̾��", "deliv_name02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ�ʡʥ�����", "deliv_kana01", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ�ʡʥᥤ��", "deliv_kana02", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("͹���ֹ�1", "deliv_zip01", ZIP01_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("͹���ֹ�2", "deliv_zip02", ZIP02_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("��ƻ�ܸ�", "deliv_pref", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("����1", "deliv_addr01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����2", "deliv_addr02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�����ֹ�1", "deliv_tel01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�����ֹ�2", "deliv_tel02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�����ֹ�3", "deliv_tel03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�᡼��ޥ�����", "mail_flag", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
}

/* DB�إǡ�������Ͽ */
function lfRegistData($uniqid) {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();
	$sqlval = $objFormParam->getDbArray();
	// ��Ͽ�ǡ����κ���
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['order_birth'] = sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);
	$sqlval['update_date'] = 'Now()';
	$sqlval['customer_id'] = '0';
	
	// ��¸�ǡ����Υ����å�
	$objQuery = new SC_Query();
	$where = "order_temp_id = ?";
	$cnt = $objQuery->count("dtb_order_temp", $where, array($uniqid));
	// ��¸�ǡ������ʤ����
	if ($cnt == 0) {
		$sqlval['create_date'] = 'Now()';
		$objQuery->insert("dtb_order_temp", $sqlval);
	} else {
		$objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
	}
	
}

/* �������ƤΥ����å� */
function lfCheckError() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
		
	// �̤Τ��Ϥ�������å�
	if($_POST['deliv_check'] == "1") { 
		$objErr->doFunc(array("��̾��������", "deliv_name01"), array("EXIST_CHECK"));
		$objErr->doFunc(array("��̾����̾��", "deliv_name02"), array("EXIST_CHECK"));
		$objErr->doFunc(array("�եꥬ�ʡʥ�����", "deliv_kana01"), array("EXIST_CHECK"));
		$objErr->doFunc(array("�եꥬ�ʡʥᥤ��", "deliv_kana02"), array("EXIST_CHECK"));
		$objErr->doFunc(array("͹���ֹ�1", "deliv_zip01"), array("EXIST_CHECK"));
		$objErr->doFunc(array("͹���ֹ�2", "deliv_zip02"), array("EXIST_CHECK"));
		$objErr->doFunc(array("��ƻ�ܸ�", "deliv_pref"), array("EXIST_CHECK"));
		$objErr->doFunc(array("����1", "deliv_addr01"), array("EXIST_CHECK"));
		$objErr->doFunc(array("����2", "deliv_addr02"), array("EXIST_CHECK"));
		$objErr->doFunc(array("�����ֹ�1", "deliv_tel01"), array("EXIST_CHECK"));
		$objErr->doFunc(array("�����ֹ�2", "deliv_tel02"), array("EXIST_CHECK"));
		$objErr->doFunc(array("�����ֹ�3", "deliv_tel03"), array("EXIST_CHECK"));
	}
	
	// ʣ�����ܥ����å�
	$objErr->doFunc(array("TEL", "order_tel01", "order_tel02", "order_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("FAX", "order_fax01", "order_fax02", "order_fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("͹���ֹ�", "order_zip01", "order_zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("TEL", "deliv_tel01", "deliv_tel02", "deliv_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("FAX", "deliv_fax01", "deliv_fax02", "deliv_fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
	$objErr->doFunc(array("͹���ֹ�", "deliv_zip01", "deliv_zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("��ǯ����", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("�᡼�륢�ɥ쥹", "�᡼�륢�ɥ쥹�ʳ�ǧ��", "order_email", "order_email_check"), array("EQUAL_CHECK"));
	
	// ���Ǥ˥��ޥ��ơ��֥�˲���Ȥ��ƥ᡼�륢�ɥ쥹����Ͽ����Ƥ�����
	if(sfCheckCustomerMailMaga($arrRet['order_email'])) {
		$objErr->arrErr['order_email'] = "���Υ᡼�륢�ɥ쥹�Ϥ��Ǥ���Ͽ����Ƥ��ޤ���<br />";
	}
		
	return $objErr->arrErr;
}

// �������ơ��֥�Τ��Ϥ���򥳥ԡ�����
function lfCopyDeliv($uniqid, $arrData) {
	$objQuery = new SC_Query();
	
	// �̤Τ��Ϥ������ꤷ�Ƥ��ʤ���硢���������Ͽ����򥳥ԡ����롣
	if($arrData["deliv_check"] != "1") {
		$sqlval['deliv_name01'] = $arrData['order_name01'];
		$sqlval['deliv_name02'] = $arrData['order_name02'];
		$sqlval['deliv_kana01'] = $arrData['order_kana01'];
		$sqlval['deliv_kana02'] = $arrData['order_kana02'];
		$sqlval['deliv_pref'] = $arrData['order_pref'];
		$sqlval['deliv_zip01'] = $arrData['order_zip01'];
		$sqlval['deliv_zip02'] = $arrData['order_zip02'];
		$sqlval['deliv_addr01'] = $arrData['order_addr01'];
		$sqlval['deliv_addr02'] = $arrData['order_addr02'];
		$sqlval['deliv_tel01'] = $arrData['order_tel01'];
		$sqlval['deliv_tel02'] = $arrData['order_tel02'];
		$sqlval['deliv_tel03'] = $arrData['order_tel03'];
		$where = "order_temp_id = ?";
		$objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
	}
}


?>