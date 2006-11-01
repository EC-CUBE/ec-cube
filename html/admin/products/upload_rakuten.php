<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./upload_csv.inc");

// 1�Ԥ�����κ���ʸ����
define("CSV_LINE_MAX", 10000);

class LC_Page {
	var $arrSession;
	var $arrCSVErr;
	function LC_Page() {
		$this->tpl_mainpage = 'products/upload_csv.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';
		$this->tpl_subno = 'upload_rakuten';
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
lfInitFile();
// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
$colmax = $objFormParam->getCount();
$objFormParam->setHtmlDispNameArray();
$objPage->arrTitle = $objFormParam->getHtmlDispNameArray();

switch($_POST['mode']) {
case 'csv_upload':
	$err = false;
	// ���顼�����å�
	$objPage->arrErr['csv_file'] = $objUpFile->makeTempFile('csv_file');
	
	if($objPage->arrErr['css_file'] == "") {
		$objPage->arrErr = $objUpFile->checkEXISTS();
	}
	
	if($objPage->arrErr['csv_file'] == "") {
		// ����ե�����̾�μ���
		$filepath = $objUpFile->getTempFilePath('csv_file');
		// ���󥳡���
		$enc_filepath = sfEncodeFile($filepath, CHAR_CODE, CSV_TEMP_DIR);
		$fp = fopen($enc_filepath, "r");
		$line = 0;		// �Կ�
		$regist = 0;	// ��Ͽ��
		
		$objQuery = new SC_Query();
		$objQuery->begin();
		
		while(!feof($fp) && !$err) {
			$arrCSV = fgetcsv($fp, CSV_LINE_MAX);
						
			// �ԥ������
			$line++;
			
			if($line <= 1) {
				continue;
			}			
							
			// ���ܿ��������
			$max = count($arrCSV);
			
			// ���ܿ���1�ʲ��ξ���̵�뤹��
			if($max <= 1) {
				continue;			
			}
			
			// ���ܿ������å�
			if($max != $colmax) {
				$objPage->arrCSVErr['blank'] = "�� ���ܿ���" . $max . "�ĸ��Ф���ޤ��������ܿ���" . $colmax . "�Ĥˤʤ�ޤ���";
				$err = true;
			} else {
				// ��������������Ǽ���롣
				$objFormParam->setParam($arrCSV, true);
				$arrRet = $objFormParam->getHashArray();
				$objFormParam->setParam($arrRet);
				// �����ͤ��Ѵ�
				$objFormParam->convParam();
				// <br>�ʤ��ǥ��顼�������롣
				$objPage->arrCSVErr = lfCheckError();
			}
			
			// ���ϥ��顼�����å�
			if(count($objPage->arrCSVErr) > 0) {
				$objPage->tpl_errtitle = "��" . $line . "���ܤǥ��顼��ȯ�����ޤ�����";
				$objPage->arrParam = $objFormParam->getHashArray();
				$err = true;
			}
			
			if(!$err) {
				lfRegistProduct($objQuery);
				$regist++;
			}
		}
		fclose($fp);
		
		if(!$err) {
			$objQuery->commit();
			$objPage->tpl_oktitle = "��" . $regist . "��Υ쥳���ɤ���Ͽ���ޤ�����";
			// ���ʷ��������ȴؿ��μ¹�
			sfCategory_Count($objQuery);
		} else {
			$objQuery->rollback();
		}
	}
	break;
default:
	break;
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//--------------------------------------------------------------------------------------------------------------------------

/* �ե��������ν���� */
function lfInitFile() {
	global $objUpFile;
	$objUpFile->addFile("CSV�ե�����", 'csv_file', array('csv'), CSV_SIZE, true, 0, 0, false);
}

/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	
	$objFormParam->addParam("�ե饰(�б��ʤ�)", "dummy1");
	$objFormParam->addParam("����̾", "name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��Х����Ѿ���̾(�б��ʤ�)", "dummy2");
	$objFormParam->addParam("���ʥ�����", "product_code", STEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����ID(�б��ʤ�)", "dummy3");
	$objFormParam->addParam("���ʥڡ���ID(�б��ʤ�)", "dummy1");
	$objFormParam->addParam("�������", "price01", PRICE_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("ɽ������", "price02", PRICE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�����ǥե饰(�б��ʤ�)", "dummy4");
	$objFormParam->addParam("����(�б��ʤ�)", "dummy5");
	$objFormParam->addParam("��������(�б��ʤ�)", "dummy6");
	$objFormParam->addParam("��ʸ�ܥ���(�б��ʤ�)", "dummy7");
	$objFormParam->addParam("��������ܥ���(�б��ʤ�)", "dummy8");
	$objFormParam->addParam("�䤤��碌�ܥ���(�б��ʤ�)", "dummy9");
	$objFormParam->addParam("������ܥ���(�б��ʤ�)", "dummy10");
	$objFormParam->addParam("�Τ��б��ե饰(�б��ʤ�)", "dummy11");
	$objFormParam->addParam("�߸˿�", "stock", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("���������(�б��ʤ�)", "dummy12");
	$objFormParam->addParam("���ָ�������(�б��ʤ�)", "dummy13");
	$objFormParam->addParam("����ʸ", "main_comment", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("��Х�������ʸ(�б��ʤ�)", "dummy14");
	$objFormParam->addParam("����(�б��ʤ�)", "dummy15");
	$objFormParam->addParam("��ŷ�ǥ��쥯�ȥ�ID(�б��ʤ�)", "dummy16");
	$objFormParam->addParam("��Х���(�б��ʤ�)", "dummy17");
}
?>