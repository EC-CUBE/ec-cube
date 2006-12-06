<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

class LC_Page {

	function LC_Page() {
		$this->tpl_mainpage = 'design/upload.tpl';
		$this->tpl_subnavi = 'design/subnavi.tpl';
		$this->tpl_subno = 'template';
		$this->tpl_subno_template = 'upload';
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = '���åץ���';
		$this->template_name = '���åץ���';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// �ե�����������饹
$objUpFile = new SC_UploadFile(TEMPLATE_TEMP_DIR, USER_TEMPLATE_PATH.$_POST['template_code']);
// �ե��������ν����
lfInitFile();
// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();

switch($_POST['mode']) {
case 'upload':
	$objFormParam->setParam($_POST);
	$arrRet = $objFormParam->getHashArray();
	
	$objPage->arrErr = lfErrorCheck($arrRet);

	// �ե���������ե��������¸
	$ret = $objUpFile->makeTempFile('template_file', false);
	if($ret != "") {
		$objPage->arrErr['template_file'] = $ret;
	} else if(count($objPage->arrErr) <= 0) {
		// �ե��������
		$ret = @mkdir(USER_TEMPLATE_PATH.$arrRet['template_code']);
		// ����ե����������¸�ǥ��쥯�ȥ�ذ�ư
		$objUpFile->moveTempFile();
		$objPage->tpl_onload = "alert('�ƥ�ץ졼�ȥե�����򥢥åץ��ɤ��ޤ�����');";
	}
	break;
default:
	break;
}
// ���̤�ɽ��
$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

/* 
 * �ؿ�̾��lfInitFile()
 * ���������ե��������ν����
 */
function lfInitFile() {
	global $objUpFile;

	$objUpFile->addFile("�ƥ�ץ졼�ȥե�����", 'template_file', array('tar.gz', 'tgz', 'tar.bz2'), TEMPLATE_SIZE, true, 0, 0, false);
}

/* 
 * �ؿ�̾��lfInitParam()
 * ���������ѥ�᡼������ν����
 */
function lfInitParam() {
	global $objFormParam;
		
	$objFormParam->addParam("�ƥ�ץ졼�ȥ�����", "template_code", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ƥ�ץ졼��̾", "template_name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
}

/* 
 * �ؿ�̾��lfErrorCheck()
 * ���������ѥ�᡼������ν����
 */
function lfErrorCheck($arrList) {
	global $objQuery;
	global $objFormParam;
	
	$objErr = new SC_CheckError($arrList);
	$objErr->arrErr = $objFormParam->checkError();
	
	if(count($objErr->arrErr) <= 0) {
		// Ʊ̾�Υե������¸�ߤ�����ϥ��顼
		if(file_exists(USER_TEMPLATE_PATH.$arrList['template_code'])) {
			$objErr->arrErr['template_code'] = "�� Ʊ̾�Υե����뤬���Ǥ�¸�ߤ��ޤ���<br/>";
		}
		// DB�ˤ��Ǥ���Ͽ����Ƥ��ʤ��������å�
		$ret = $objQuery->get("dtb_templates", "template_code", "template_code = ?", array($arrList['template_code']));
		if($ret != "") {
			$objErr->arrErr['template_code'] = "�� ���Ǥ���Ͽ����Ƥ���ƥ�ץ졼�ȥ����ɤǤ���<br/>";
		}
	}
	
	return $objErr->arrErr;
}

function lfRegistTemplate($arrList) {
	global $objQuery;
	
	// INSERT�����ͤ�������롣
	$sqlval['name'] = $arrList['template_code'];
	$sqlval['category_id'] = $arrList['template_name'];
	$sqlval['create_date'] = "now()";
	$sqlval['update_date'] = "now()";

	$objQuery->insert("dtb_templates", $sqlval);
}

