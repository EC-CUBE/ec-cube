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
	var $arrAddr;
	function LC_Page() {
		$this->tpl_mainpage = 'shopping/deliv.tpl';
		$this->tpl_css = URL_DIR.'css/layout/shopping/index.css';
		global $arrPref;
		$this->arrPref = $arrPref;
		$this->tpl_title = "���Ϥ������";		// �����ȥ�

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
// ���å����������饹
$objCookie = new SC_Cookie(COOKIE_EXPIRE);
// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

$objLoginFormParam = new SC_FormParam();	// ������ե�������
lfInitLoginFormParam();						// �������
$objLoginFormParam->setParam($_POST);		// POST�ͤμ���

// �桼����ˡ���ID�μ����ȹ������֤�������������å�
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);
$objPage->tpl_uniqid = $uniqid;

// ����������å�
if($_POST['mode'] != 'login' && !$objCustomer->isLoginSuccess()) {
	// �������������Ȥߤʤ�
	sfDispSiteError(CUSTOMER_ERROR);
}

switch($_POST['mode']) {
case 'login':
	$objLoginFormParam->toLower('login_email');
	$objPage->arrErr = $objLoginFormParam->checkError();
	$arrForm =  $objLoginFormParam->getHashArray();
	// ���å�����¸Ƚ��
	if($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
		$objCookie->setCookie('login_email', $_POST['login_email']);
	} else {
		$objCookie->setCookie('login_email', '');
	}

	if(count($objPage->arrErr) == 0) {
		// ������Ƚ��
		if(!$objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
			// ����Ͽ��Ƚ��
			$objQuery = new SC_Query;
			$where = "email = ? AND status = 1 AND del_flg = 0";
			$ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));
			
			if($ret > 0) {
				sfDispSiteError(TEMP_LOGIN_ERROR);
			} else {
				sfDispSiteError(SITE_LOGIN_ERROR);
			}
		} 
	} else {
		// ������ڡ��������
		header("Location: " . URL_SHOP_TOP);
		exit;	
	}
	break;
// ���
case 'delete':
	if (sfIsInt($_POST['other_deliv_id'])) {
		$objQuery = new SC_Query();
		$where = "other_deliv_id = ?";
		$arrRet = $objQuery->delete("dtb_other_deliv", $where, array($_POST['other_deliv_id']));
		$objFormParam->setValue('select_addr_id', '');
	}
	break;
// �����Ͽ���������
case 'customer_addr':
	// �����Ͽ���꤬�����å�����Ƥ�����
	if ($_POST['deliv_check'] == '-1') {
		// �������ν����������ơ��֥�˽񤭹���
		lfRegistDelivData($uniqid, $objCustomer);
		// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
		$objSiteSess->setRegistFlag();
		// ����ʧ����ˡ����ڡ����ذ�ư
		header("Location: " . URL_SHOP_PAYMENT);
		exit;
	// �̤Τ��Ϥ��褬�����å�����Ƥ�����
	} elseif($_POST['deliv_check'] >= 1) {
		if (sfIsInt($_POST['deliv_check'])) {
			$deliv_count = $objQuery->count("dtb_other_deliv","customer_id=? and other_deliv_id = ?" ,array($objCustomer->getValue('customer_id'), $_POST['deliv_check']));
			if ($deliv_count != 1) {
            	sfDispSiteError(CUSTOMER_ERROR);
            }
			// ��Ͽ�Ѥߤ��̤Τ��Ϥ����������ơ��֥�˽񤭹���
			lfRegistOtherDelivData($uniqid, $objCustomer, $_POST['deliv_check']);
			// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
			$objSiteSess->setRegistFlag();
			// ����ʧ����ˡ����ڡ����ذ�ư
			header("Location: " . URL_SHOP_PAYMENT);
			exit;

		}
	}else{
		// ���顼���֤�
		$arrErr['deli'] = '�� ���Ϥ�������򤷤Ƥ���������';
	}	
	break;
// ���Υڡ��������
case 'return':
	// ��ǧ�ڡ����ذ�ư
	header("Location: " . URL_CART_TOP);
	exit;
	break;
default:
	$objQuery = new SC_Query();
	$where = "order_temp_id = ?";
	$arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($uniqid));
	$objFormParam->setParam($arrRet[0]);
	break;
}

/** ɽ������ **/

// �����Ͽ����μ���
$col = "name01, name02, pref, addr01, addr02";
$where = "customer_id = ?";
$objQuery = new SC_Query();
$arrCustomerAddr = $objQuery->select($col, "dtb_customer", $where, array($_SESSION['customer']['customer_id']));
// �̤Τ��Ϥ��轻��μ���
$col = "other_deliv_id, name01, name02, pref, addr01, addr02";
$objQuery->setorder("other_deliv_id DESC");
$objOtherAddr = $objQuery->select($col, "dtb_other_deliv", $where, array($_SESSION['customer']['customer_id']));
$objPage->arrAddr = $arrCustomerAddr;
$objPage->tpl_addrmax = count($objOtherAddr);
$cnt = 1;
foreach($objOtherAddr as $val) {
	$objPage->arrAddr[$cnt] = $val;
	$cnt++;
}

// �����ͤμ���
$objPage->arrForm = $objFormParam->getFormParamList();
$objPage->arrErr = $arrErr;

$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
$objCampaignSess->pageView($objView);
//--------------------------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("��̾��1", "deliv_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��̾��2", "deliv_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ��1", "deliv_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�եꥬ��2", "deliv_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("͹���ֹ�1", "deliv_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("͹���ֹ�2", "deliv_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objFormParam->addParam("��ƻ�ܸ�", "deliv_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("����1", "deliv_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����2", "deliv_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�����ֹ�1", "deliv_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�����ֹ�2", "deliv_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("�����ֹ�3", "deliv_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
	$objFormParam->addParam("", "deliv_check");	
}

function lfInitLoginFormParam() {
	global $objLoginFormParam;
	$objLoginFormParam->addParam("��������", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objLoginFormParam->addParam("�᡼�륢�ɥ쥹", "login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objLoginFormParam->addParam("�ѥ����", "login_pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}

/* DB�إǡ�������Ͽ */
function lfRegistNewAddrData($uniqid, $objCustomer) {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();
	$sqlval = $objFormParam->getDbArray();
	// ��Ͽ�ǡ����κ���
	$sqlval['deliv_check'] = '1';
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['update_date'] = 'Now()';
	$sqlval['customer_id'] = $objCustomer->getValue('customer_id');
	$sqlval['order_birth'] = $objCustomer->getValue('birth');
	
	sfRegistTempOrder($uniqid, $sqlval);
}

/* �������ν����������ơ��֥�� */
function lfRegistDelivData($uniqid, $objCustomer) {
	// ��Ͽ�ǡ����κ���
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['update_date'] = 'Now()';
	$sqlval['customer_id'] = $objCustomer->getValue('customer_id');
    $sqlval['deliv_check'] = '-1';
	$sqlval['deliv_name01'] = $objCustomer->getValue('name01');
    $sqlval['deliv_name02'] = $objCustomer->getValue('name02');
    $sqlval['deliv_kana01'] = $objCustomer->getValue('kana01');
    $sqlval['deliv_kana02'] = $objCustomer->getValue('kana02');
    $sqlval['deliv_zip01'] = $objCustomer->getValue('zip01');
    $sqlval['deliv_zip02'] = $objCustomer->getValue('zip02');
    $sqlval['deliv_pref'] = $objCustomer->getValue('pref');
    $sqlval['deliv_addr01'] = $objCustomer->getValue('addr01');
    $sqlval['deliv_addr02'] = $objCustomer->getValue('addr02');
    $sqlval['deliv_tel01'] = $objCustomer->getValue('tel01');
    $sqlval['deliv_tel02'] = $objCustomer->getValue('tel02');
	$sqlval['deliv_tel03'] = $objCustomer->getValue('tel03');

    $sqlval['deliv_fax01'] = $objCustomer->getValue('fax01');
    $sqlval['deliv_fax02'] = $objCustomer->getValue('fax02');
	$sqlval['deliv_fax03'] = $objCustomer->getValue('fax03');

	sfRegistTempOrder($uniqid, $sqlval);
}

/* �̤Τ��Ϥ��轻���������ơ��֥�� */
function lfRegistOtherDelivData($uniqid, $objCustomer, $other_deliv_id) {
	// ��Ͽ�ǡ����κ���
	$sqlval['order_temp_id'] = $uniqid;
	$sqlval['update_date'] = 'Now()';
	$sqlval['customer_id'] = $objCustomer->getValue('customer_id');
	$sqlval['order_birth'] = $objCustomer->getValue('birth');
		
	$objQuery = new SC_Query();
	$where = "other_deliv_id = ?";
	$arrRet = $objQuery->select("*", "dtb_other_deliv", $where, array($other_deliv_id));
	
	$sqlval['deliv_check'] = $other_deliv_id;
    $sqlval['deliv_name01'] = $arrRet[0]['name01'];
    $sqlval['deliv_name02'] = $arrRet[0]['name02'];
    $sqlval['deliv_kana01'] = $arrRet[0]['kana01'];
    $sqlval['deliv_kana02'] = $arrRet[0]['kana02'];
    $sqlval['deliv_zip01'] = $arrRet[0]['zip01'];
    $sqlval['deliv_zip02'] = $arrRet[0]['zip02'];
    $sqlval['deliv_pref'] = $arrRet[0]['pref'];
    $sqlval['deliv_addr01'] = $arrRet[0]['addr01'];
    $sqlval['deliv_addr02'] = $arrRet[0]['addr02'];
    $sqlval['deliv_tel01'] = $arrRet[0]['tel01'];
    $sqlval['deliv_tel02'] = $arrRet[0]['tel02'];
	$sqlval['deliv_tel03'] = $arrRet[0]['tel03'];
	sfRegistTempOrder($uniqid, $sqlval);
}

/* �������ƤΥ����å� */
function lfCheckError() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	// ʣ�����ܥ����å�
	if ($_POST['mode'] == 'login'){
	$objErr->doFunc(array("�᡼�륢�ɥ쥹", "login_email", STEXT_LEN), array("EXIST_CHECK"));
	$objErr->doFunc(array("�ѥ����", "login_pass", STEXT_LEN), array("EXIST_CHECK"));
	}
	$objErr->doFunc(array("TEL", "deliv_tel01", "deliv_tel02", "deliv_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
	return $objErr->arrErr;
}
?>