<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");
require_once(DATA_PATH. "module/Tar.php");

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

// ���åץ��ɤ����ե������ե����
$new_file_dir = USER_TEMPLATE_PATH.$_POST['template_code'];

// �ե�����������饹
$objUpFile = new SC_UploadFile(TEMPLATE_TEMP_DIR, $new_file_dir);
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
		$ret = @mkdir($new_file_dir);
		// ����ե����������¸�ǥ��쥯�ȥ�ذ�ư
		$objUpFile->moveTempFile();
		// ����
		lfUnpacking($new_file_dir, $_FILES['template_file']['name'], $new_file_dir."/");
		// DB�˥ƥ�ץ졼�Ⱦ������¸
		lfRegistTemplate($arrRet);
		// ��λɽ��javascript
		$objPage->tpl_onload = "alert('�ƥ�ץ졼�ȥե�����򥢥åץ��ɤ��ޤ�����');";
		// �ե������ͤ򥯥ꥢ
		$objFormParam->setParam(array('template_code' => "", 'template_name' => ""));
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

	$objUpFile->addFile("�ƥ�ץ졼�ȥե�����", 'template_file', array(), TEMPLATE_SIZE, true, 0, 0, false);
}

/* 
 * �ؿ�̾��lfInitParam()
 * ���������ѥ�᡼������ν����
 */
function lfInitParam() {
	global $objFormParam;
		
	$objFormParam->addParam("�ƥ�ץ졼�ȥ�����", "template_code", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "ALNUM_CHECK"));
	$objFormParam->addParam("�ƥ�ץ졼��̾", "template_name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
}

/* 
 * �ؿ�̾��lfErrorCheck()
 * ����1 ���ե��������
 * �����������顼�����å�
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
		// �ե�����γ�ĥ�ҥ����å�(.tar/tar.gz�Τߵ���)
		$errFlag = true;
		$array_ext = explode(".", $_FILES['template_file']['name']);
		$ext = $array_ext[ count ( $array_ext ) - 1 ];
		$ext = strtolower($ext);
		// .tar�����å�
		if ($ext == 'tar') {
			$errFlag = false;
		}

		$ext = $array_ext[ count ( $array_ext ) - 2 ].".".$ext;
		$ext = strtolower($ext);
		// .tar.gz�����å�
		if ($ext== 'tar.gz') {
			$errFlag = false;
		}
		
		if($errFlag) {
			$objErr->arrErr['template_file'] = "�� ���åץ��ɤ���ƥ�ץ졼�ȥե�����ǵ��Ĥ���Ƥ�������ϡ�tar/tar.gz�Ǥ���<br />";		
		}
	}
	
	return $objErr->arrErr;
}

/* 
 * �ؿ�̾��lfErrorCheck()
 * ����1 ���ѥ�᡼��
 * ���������ƥ�ץ졼�ȥǡ�����Ͽ
 */
function lfRegistTemplate($arrList) {
	global $objQuery;
	
	// INSERT�����ͤ�������롣
	$sqlval['template_code'] = $arrList['template_code'];
	$sqlval['template_name'] = $arrList['template_name'];
	$sqlval['create_date'] = "now()";
	$sqlval['update_date'] = "now()";

	$objQuery->insert("dtb_templates", $sqlval);
}

/* 
 * �ؿ�̾��lfUnpacking
 * ����1 ���ǥ��쥯�ȥ�
 * ����2 ���ե�����͡���
 * ����3 ������ǥ��쥯�ȥ�
 * ���������ƥ�ץ졼�ȥǡ�����Ͽ
 */
function lfUnpacking($dir, $file_name, $unpacking_dir) {

	// ���̥ե饰TRUE��gzip����򤪤��ʤ�
	$tar = new Archive_Tar("$dir/$file_name", TRUE);

	// ��ĥ�Ҥ��ڤ���
	$unpacking_name = ereg_replace("\.tar$", "", $file_name);
	$unpacking_name = ereg_replace("\.tar\.gz$", "", $file_name);

	// ���ꤵ�줿�ե������˲��ह��
	$err = $tar->extractModify($unpacking_dir, $unpacking_name);

	// �ե�������
	@sfDelFile("$dir/$unpacking_name");
	// ���̥ե�������
	@unlink("$dir/$file_name");

	return $err;
}