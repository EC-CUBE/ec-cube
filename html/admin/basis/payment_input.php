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
		$this->tpl_mainpage = 'basis/payment_input.tpl';
		$this->tpl_subtitle = '��ʧ��ˡ����';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �ե�����������饹
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
// �ե��������ν����
$objUpFile = lfInitFile($objUpFile);
// Hidden����Υǡ���������Ѥ�
$objUpFile->setHiddenFileList($_POST);

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
	$objPage->charge_flg = $_POST["charge_flg"];
	if(count($objPage->arrErr) == 0) {
		lfRegistData($_POST['payment_id']);
		// ����ե���������֥ǥ��쥯�ȥ�˰�ư����
		$objUpFile->moveTempFile();
		// �ƥ�����ɥ��򹹿�����褦�˥��åȤ��롣
		$objPage->tpl_onload="fnUpdateParent('".URL_PAYMENT_TOP."'); window.close();";
	}
	
	break;
// �����Υ��åץ���
case 'upload_image':
	// �ե�����¸�ߥ����å�
	$objPage->arrErr = array_merge($objPage->arrErr, $objUpFile->checkEXISTS($_POST['image_key']));
	// ������¸����
	$objPage->arrErr[$_POST['image_key']] = $objUpFile->makeTempFile($_POST['image_key']);
	break;
// �����κ��
case 'delete_image':
	$objUpFile->deleteFile($_POST['image_key']);
	break;
default:
	break;
}

if($_POST['mode'] == "") {
	switch($_GET['mode']) {
	case 'pre_edit':
		if(sfIsInt($_GET['payment_id'])) {
			$arrRet = lfGetData($_GET['payment_id']);
			$objFormParam->setParam($arrRet);
			$objPage->charge_flg = $arrRet["charge_flg"];
			// DB�ǡ�����������ե�����̾���ɹ�
			$objUpFile->setDBFileList($arrRet);
			$objPage->tpl_payment_id = $_GET['payment_id'];
		}
		break;
	default:
		break;
	}
} else {
	$objPage->tpl_payment_id = $_POST['payment_id'];
}

$objPage->arrDelivList = sfGetIDValueList("dtb_deliv", "deliv_id", "service_name");
$objPage->arrForm = $objFormParam->getFormParamList();

// FORMɽ����������Ϥ���
$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
// HIDDEN�Ѥ�������Ϥ���
$objPage->arrHidden = array_merge((array)$objPage->arrHidden, (array)$objUpFile->getHiddenFileList());

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
/* �ե��������ν���� */
function lfInitFile($objUpFile) {
	$objUpFile->addFile("������", 'payment_image', array('gif'), IMAGE_SIZE, false, CLASS_IMAGE_WIDTH, CLASS_IMAGE_HEIGHT);
	return $objUpFile;
}

/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("��ʧ��ˡ", "payment_method", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�����", "charge", PRICE_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("���Ѿ��(���߰ʾ�)", "rule", PRICE_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("���Ѿ��(���߰ʲ�)", "upper_rule", PRICE_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("���������ӥ�", "deliv_id", INT_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����", "fix");
}

/* DB����ǡ������ɤ߹��� */
function lfGetData($payment_id) {
	$objQuery = new SC_Query();
	$where = "payment_id = ?";
	$arrRet = $objQuery->select("*", "dtb_payment", $where, array($payment_id));
	return $arrRet[0];
}

/* DB�إǡ�������Ͽ���� */
function lfRegistData($payment_id = "") {
	global $objFormParam;
	global $objUpFile;
	
	$objQuery = new SC_Query();
	$sqlval = $objFormParam->getHashArray();
	$arrRet = $objUpFile->getDBFileList();	// �ե�����̾�μ���
	$sqlval = array_merge($sqlval, $arrRet);	
	$sqlval['update_date'] = 'Now()';
	
	if($sqlval['fix'] != '1') {
		$sqlval['fix'] = 2;	// ��ͳ����
	}
	
	// ������Ͽ
	if($payment_id == "") {
		// INSERT�μ¹�
		$sqlval['creator_id'] = $_SESSION['member_id'];
		$sqlval['rank'] = $objQuery->max("dtb_payment", "rank") + 1;
		$sqlval['create_date'] = 'Now()';
		$objQuery->insert("dtb_payment", $sqlval);
	// ��¸�Խ�
	} else {
		$where = "payment_id = ?";
		$objQuery->update("dtb_payment", $sqlval, $where, array($payment_id));
	}
}

/*�����Ѿ��ο��ͥ����å� */

/* �������ƤΥ����å� */
function lfCheckError() {
	global $objFormParam;
	
	// DB�Υǡ��������
	$arrPaymentData = lfGetData($_POST['payment_id']);
	
	// �����������Ǥ��ʤ����ˤϡ��������0�ˤ���
	if($arrPaymentData["charge_flg"] == 2) $objFormParam->setValue("charge", "0");
	
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	// ���Ѿ��(����)�����å�
	if($arrRet["rule"] < $arrPaymentData["rule_min"] and $arrPaymentData["rule_min"] != ""){
		$objErr->arrErr["rule"] = "���Ѿ��(����)��" . $arrPaymentData["rule_min"] ."�߰ʾ�ˤ��Ƥ���������<br>";
	}
	
	// ���Ѿ��(���)�����å�
	if($arrRet["upper_rule"] > $arrPaymentData["upper_rule_max"] and $arrPaymentData["upper_rule_max"] != ""){
		$objErr->arrErr["upper_rule"] = "���Ѿ��(���)��" . $arrPaymentData["upper_rule_max"] ."�߰ʲ��ˤ��Ƥ���������<br>";
	}
	
	// ���Ѿ������å�
	$objErr->doFunc(array("���Ѿ��(���߰ʾ�)", "���Ѿ��(���߰ʲ�)", "rule", "upper_rule"), array("GREATER_CHECK"));
	
	return $objErr->arrErr;
}


?>