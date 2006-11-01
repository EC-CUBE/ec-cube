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
		$this->tpl_subno = 'upload_csv';
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
		
	$objFormParam->addParam("����ID", "product_id", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("����ID", "product_class_id", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	
	$objFormParam->addParam("����̾1(�ѹ��Բ�)", "dummy1");
	$objFormParam->addParam("����̾2(�ѹ��Բ�)", "dummy2");
	
	$objFormParam->addParam("����̾", "name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�����ե饰(1:���� 2:�����)", "status", INT_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("���ʥ��ơ�����", "product_flag", INT_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("���ʥ�����", "product_code", STEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("���ͻԾ����", "price01", PRICE_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("����", "price02", PRICE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�߸˿�", "stock", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("����", "deliv_fee", PRICE_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�ݥ������ͿΨ", "point_rate", PERCENTAGE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("��������", "sale_limit", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�᡼����URL", "comment1", STEXT_LEN, "KVa", array("SPTAB_CHECK","URL_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�������", "comment3", STEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����-�ᥤ�󥳥���", "main_list_comment", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����-�ᥤ�����", "main_list_image", LTEXT_LEN, "KVa", array("EXIST_CHECK","FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ᥤ�󥳥���", "main_comment", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ᥤ�����", "main_image", LTEXT_LEN, "KVa", array("EXIST_CHECK","FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ᥤ��������", "main_large_image", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("���顼��Ӳ���", "file1", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("���ʾܺ٥ե�����", "file2", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���֥����ȥ�(1)", "sub_title1", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���֥�����(1)", "sub_comment1", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���ֲ���(1)", "sub_image1", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���ֳ������(1)", "sub_large_image1", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	
	$objFormParam->addParam("�ܺ�-���֥����ȥ�(2)", "sub_title2", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���֥�����(2)", "sub_comment2", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���ֲ���(2)", "sub_image2", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���ֳ������(2)", "sub_large_image2", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	
	$objFormParam->addParam("�ܺ�-���֥����ȥ�(3)", "sub_title3", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���֥�����(3)", "sub_comment3", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���ֲ���(3)", "sub_image3", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���ֳ������(3)", "sub_large_image3", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
		
	$objFormParam->addParam("�ܺ�-���֥����ȥ�(4)", "sub_title4", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���֥�����(4)", "sub_comment4", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���ֲ���(4)", "sub_image4", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���ֳ������(4)", "sub_large_image4", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
		
	$objFormParam->addParam("�ܺ�-���֥����ȥ�(5)", "sub_title5", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���֥�����(5)", "sub_comment5", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���ֲ���(5)", "sub_image5", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ܺ�-���ֳ������(5)", "sub_large_image5", LTEXT_LEN, "KVa", array("FILE_EXISTS","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	
	$objFormParam->addParam("ȯ�����ܰ�", "deliv_date_id", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	
	$objFormParam->addParam("�������ᾦ��(1)", "recommend_product_id1", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�ܺ�-���֥�����(1)", "recommend_comment1", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�������ᾦ��(2)", "recommend_product_id2", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�ܺ�-���֥�����(2)", "recommend_comment2", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�������ᾦ��(3)", "recommend_product_id3", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�ܺ�-���֥�����(3)", "recommend_comment3", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�������ᾦ��(4)", "recommend_product_id4", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�ܺ�-���֥�����(4)", "recommend_comment4", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
}
?>