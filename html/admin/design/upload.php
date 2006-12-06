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

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// �ե�����������饹
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
// �ե��������ν����
lfInitFile();
// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();

switch($_POST['mode']) {
case 'upload':
	$objPage->arrErr = lfErrorCheck();
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
function lfErrorCheck() {

	global $objQuery;
	global $objFormParam;
	
	$arrRet = $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
sfprintr($arrRet);
	return $objErr->arrErr;
}
