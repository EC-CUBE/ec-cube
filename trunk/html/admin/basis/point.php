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
		$this->tpl_mainpage = 'basis/point.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'point';
		$this->tpl_mainno = 'basis';
		$this->tpl_subtitle = '�ݥ��������';
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

$cnt = $objQuery->count("dtb_baseinfo");

if ($cnt > 0) {
	$objPage->tpl_mode = "update";
} else {
	$objPage->tpl_mode = "insert";
}

if($_POST['mode'] != "") {
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = $objFormParam->checkError();
	
	if(count($objPage->arrErr) == 0) {
		switch($_POST['mode']) {
		case 'update':
			lfUpdateData(); // ��¸�Խ�
			break;
		case 'insert':
			lfInsertData(); // ��������
			break;
		default:
			break;
		}
		// ��ɽ��
		//sfReload();
		$objPage->tpl_onload = "window.alert('�ݥ�������꤬��λ���ޤ�����');";
	}
} else {
	$arrCol = $objFormParam->getKeyList(); // ����̾���������
	$col	= sfGetCommaList($arrCol);
	$arrRet = $objQuery->select($col, "dtb_baseinfo");
	// POST�ͤμ���
	$objFormParam->setParam($arrRet[0]);
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//--------------------------------------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("�ݥ������ͿΨ", "point_rate", PERCENTAGE_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�����Ͽ����Ϳ�ݥ����", "welcome_point", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
}

function lfUpdateData() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$sqlval = $objFormParam->getHashArray();
	$sqlval['update_date'] = 'Now()';
	$objQuery = new SC_Query();
	// UPDATE�μ¹�
	$ret = $objQuery->update("dtb_baseinfo", $sqlval);
}

function lfInsertData() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$sqlval = $objFormParam->getHashArray();
	$sqlval['update_date'] = 'Now()';
	$objQuery = new SC_Query();
	// INSERT�μ¹�
	$ret = $objQuery->insert("dtb_baseinfo", $sqlval);
}

