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
	function LC_Page() {
		$this->tpl_mainpage = 'basis/delivery_input.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'delivery';
		$this->tpl_mainno = 'basis';
		global $arrPref;
		$this->arrPref = $arrPref;
		$this->tpl_subtitle = '�����ȼ�����';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

switch($_POST['mode']) {
case 'edit':
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError();
	if(count($objPage->arrErr) == 0) {
		$objPage->tpl_deliv_id = lfRegistData();
		$objPage->tpl_onload = "window.alert('�����ȼ����꤬��λ���ޤ�����');";
	}
	break;
case 'pre_edit':
	if($_POST['deliv_id'] != "") {
		lfGetDelivData($_POST['deliv_id']);
		$objPage->tpl_deliv_id = $_POST['deliv_id'];
	}
	break;
default:
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//--------------------------------------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("�����ȼ�̾", "name", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��ɼNo.��ǧURL", "confirm_url", STEXT_LEN, "n", array("URL_CHECK", "MAX_LENGTH_CHECK"), "http://");
	
	for($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
		$objFormParam->addParam("��������$cnt", "deliv_time$cnt", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
	}
	
	if(INPUT_DELIV_FEE) {
		for($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
			$objFormParam->addParam("��������$cnt", "fee$cnt", PRICE_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
		}
	}
}

/* DB����Ͽ���� */
function lfRegistData() {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();
	$objQuery = new SC_Query();
	$objQuery->begin();

	// ���ϥǡ������Ϥ���
	$sqlval['name'] = $arrRet['name'];
	$sqlval['service_name'] = $arrRet['name'];
	$sqlval['confirm_url'] = $arrRet['confirm_url'];
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['update_date'] = 'Now()';
	
	if($_POST['deliv_id'] != "") {
		$deliv_id = $_POST['deliv_id'];
		$where = "deliv_id = ?";
		$objQuery->update("dtb_deliv", $sqlval, $where, array($deliv_id));
		$objQuery->delete("dtb_delivfee", $where, array($deliv_id));
		$objQuery->delete("dtb_delivtime", $where, array($deliv_id));
	} else {
		// ��Ͽ���������ȼ�ID�μ���

		if (DB_TYPE == "pgsql") {
			$deliv_id = $objQuery->nextval('dtb_deliv', 'deliv_id');
			$sqlval['deliv_id'] = $deliv_id;
		}
		
		$sqlval['rank'] = $objQuery->max("dtb_deliv", "rank") + 1;
		$sqlval['create_date'] = 'Now()';
		// INSERT�μ¹�
		$objQuery->insert("dtb_deliv", $sqlval);
		
		if (DB_TYPE == "mysql") {
			$deliv_id = $objQuery->nextval('dtb_deliv', 'deliv_id');			
		}
	}
	
	$sqlval = array();
	// �������֤�����
	for($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
		$keyname = "deliv_time$cnt";
		if($arrRet[$keyname] != "") {
			$sqlval['deliv_id'] = $deliv_id;
			$sqlval['deliv_time'] = $arrRet[$keyname];
			// INSERT�μ¹�
			$objQuery->insert("dtb_delivtime", $sqlval);
		}
	}
	
	if(INPUT_DELIV_FEE) {
		$sqlval = array();
		// �������������
		for($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
			$keyname = "fee$cnt";
			if($arrRet[$keyname] != "") {
				$sqlval['deliv_id'] = $deliv_id;
				$sqlval['fee'] = $arrRet[$keyname];
				$sqlval['pref'] = $cnt;
				// INSERT�μ¹�
				$objQuery->insert("dtb_delivfee", $sqlval);
			}
		}
	}
	$objQuery->commit();
	return $deliv_id;
}

/* �����ȼԾ���μ��� */
function lfGetDelivData($deliv_id) {
	global $objFormParam;
	$objQuery = new SC_Query();
	// �����ȼ԰����μ���
	$col = "deliv_id, name, service_name, confirm_url";
	$where = "deliv_id = ?";
	$table = "dtb_deliv";
	$arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
	$objFormParam->setParam($arrRet[0]);
	// �������֤μ���
	$col = "deliv_time";
	$where = "deliv_id = ?  ORDER BY time_id";
	$table = "dtb_delivtime";
	$arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
	$objFormParam->setParamList($arrRet, 'deliv_time');
	// ��������μ���
	$col = "fee";
	$where = "deliv_id = ? ORDER BY pref";
	$table = "dtb_delivfee";
	$arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
	$objFormParam->setParamList($arrRet, 'fee');
}

/* �������ƤΥ����å� */
function lfCheckError() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	if(!isset($objErr->arrErr['name']) && $_POST['deliv_id'] == "") {
		// ��¸�����å�
		$ret = sfIsRecord("dtb_deliv", "service_name", array($arrRet['service_name']));
		if ($ret) {
			$objErr->arrErr['name'] = "�� Ʊ��̾�Τ��Ȥ߹�碌����Ͽ�Ǥ��ޤ���<br>";
		}
	}
	
	return $objErr->arrErr;
}
