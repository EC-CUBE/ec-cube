<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./page_edit.inc");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'contents/page_edit.tpl';
		$this->tpl_mainno = 'contents';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "page_edit";
		global $arrPageList;
		$this->arrPageList = $arrPageList;
		$this->tpl_subtitle = '�ڡ����Խ�';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

$objFormParam = new SC_FormParam();			// �ե�������
lfInitParam();								// �ѥ�᡼������ν����
$objFormParam->setParam($_POST);			// POST�ͤμ���

switch($_POST['mode']) {
case 'edit':
	$objPage->arrErr = $objFormParam->checkError();
	if(count($objPage->arrErr) == 0) {
		$page = $_POST['page'];
		if($arrPageTpl[$page] != "") {
			// ����ե�����˽񤭹���
			$path = TEMPLATE_FTP_DIR . $arrPageTpl[$page] . ".tmp";
			$ret = lfWriteFile($path, $objFormParam->getValue('template'));
			// ���֥ե������ȿ��
			if($ret > 0) {
				$dst_path = TEMPLATE_FTP_DIR . $arrPageTpl[$page];
				if(!copy($path, $dst_path)) {
					print("�ե�����ν���ߤ˼��Ԥ��ޤ�����");
				}
			}
		}
	}	
	break;
case 'preview':
	$objPage->arrErr = $objFormParam->checkError();
	if(count($objPage->arrErr) == 0) {
		$page = $_POST['page'];
		if($arrPageTpl[$page] != "") {
			// ����ե�����˽񤭹���
			$path = TEMPLATE_FTP_DIR . $arrPageTpl[$page] . ".tmp";
			$ret = lfWriteFile($path, $objFormParam->getValue('template'));
			// �ץ�ӥ塼ɽ��
			$url = $arrPageURL[$page] . "tpl=" . $arrPageTpl[$page] . ".tmp";
			$objPage->tpl_onload ="window.open('$url', 'preview');";
		}
	}	
	break;
case 'select':
	$page = $_POST['page'];
	if($arrPageTpl[$page] != "") {
		// �ե��������Ȥ��ɤ��ʸ����˳�Ǽ����
		$path = TEMPLATE_FTP_DIR . $arrPageTpl[$page];
		if(file_exists($path)) {
	 		$fp = fopen($path, "r");
			$contents = fread($fp, filesize($path));
			$objFormParam->setValue('template', $contents);
			fclose($fp);
		}
	} else {
		$objFormParam->setValue('template', "");
	}
	break;
default:
	
	break;
}

/*

// �ե��������Ȥ��ɤ��ʸ����˳�Ǽ����
$path = TEMPLATE_FTP_DIR . "index.tpl";
$fp = fopen($path, "r");
$contents = fread($fp, filesize($path));

$objFormParam->setValue('template', $contents);

fclose($fp);

*/

// �����ͤμ���
$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//---------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("�ڡ�������", "page", INT_LEN, "n", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ƥ�ץ졼��", "template", LLTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
}

function lfWriteFile($path, $string) {
	$fp = fopen($path,"w+");
	flock($fp, LOCK_EX);
	$ret = fwrite($fp, $string);
	fclose($fp);
	return $ret;
}

?>