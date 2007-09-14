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
	function LC_Page() {
		$this->tpl_mainpage = 'basis/control.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_mainno = 'basis';
		$this->tpl_subno = 'control';
		$this->tpl_subtitle = '�����ȴ�������';
	}
}
$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

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
	
		// ���顼�����å�
		$objPage->arrErr = lfCheckError();
		if(count($objPage->arrErr) == 0) {
			lfSiteControlData($_POST['control_id']);
			// javascript�¹�
			$objPage->tpl_onload = "alert('��������λ���ޤ�����');";
		}
		
		break;
	default:
		break;
}

// �����ȴ�������μ���
$arrSiteControlList = lfGetControlList();

// �ץ������κ���
for ($i = 0; $i < count($arrSiteControlList); $i++) {	
	switch ($arrSiteControlList[$i]["control_id"]) {
		// �ȥ�å��Хå�
		case SITE_CONTROL_TRACKBACK:
			$arrSiteControlList[$i]["control_area"] = $arrSiteControlTrackBack;
			break;
		// ���ե��ꥨ����
		case SITE_CONTROL_AFFILIATE:
			$arrSiteControlList[$i]["control_area"] = $arrSiteControlAffiliate;
			break;
		default:
			break;
	}
}

$objPage->arrControlList = $arrSiteControlList;
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
// �����ȴ�������μ���
function lfGetControlList() {
	$objQuery = new SC_Query();
	// �����ȴ�������μ���
	$sql = "SELECT * FROM dtb_site_control ";
	$sql .= "WHERE del_flg = 0";
	$arrRet = $objQuery->getall($sql);
	return $arrRet;
}

/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("�������", "control_flg", INT_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
}

/* �������ƤΥ����å� */
function lfCheckError() {
	global $objFormParam;
	
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	return $objErr->arrErr;
}

/* DB�إǡ�������Ͽ���� */
function lfSiteControlData($control_id = "") {
	global $objFormParam;
	
	$objQuery = new SC_Query();
	$sqlval = $objFormParam->getHashArray();	
	$sqlval['update_date'] = 'Now()';
	
	// ������Ͽ
	if($control_id == "") {
		// INSERT�μ¹�
		//$sqlval['creator_id'] = $_SESSION['member_id'];
		$sqlval['create_date'] = 'Now()';
		$objQuery->insert("dtb_site_control", $sqlval);
	// ��¸�Խ�
	} else {
		$where = "control_id = ?";
		$objQuery->update("dtb_site_control", $sqlval, $where, array($control_id));
	}
}

?>