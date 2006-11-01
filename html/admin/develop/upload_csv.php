<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $arrCSVErr;
	function LC_Page() {
		$this->tpl_mainpage = 'develop/upload_csv.tpl';
		$this->tpl_subnavi = '';
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

if(ADMIN_MODE != 1) {
	print("���Υڡ����ˤϡ����������Ǥ��ޤ���");
	exit;
}

// �ե�����������饹
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
// �ե��������ν����
lfInitFile();
// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
$colmax = $objFormParam->getCount();
$objPage->arrTitle = $objFormParam->getTitleArray();

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
			$arrCSV = fgetcsv($fp, 10000);
			// �ԥ������
			$line++;
	
			// ���ܿ��������
			$max = count($arrCSV);
			
			// ���ܿ���1�ʲ��ξ���̵�뤹��
			if($max <= 1) {
				continue;			
			}
			
			// ���ܿ������å�
			if($max != $colmax) {
				$objPage->arrCSVErr['blank'] = "�� ���ܿ���" . $max . "�ĸ��Ф���ޤ��������ܿ���" . $colmax . "�Ĥˤʤ�ޤ���";
				
				ob_start();
				print_r($arrCSV);
				$objPage->tpl_debug = ob_get_contents();
				ob_end_clean();	
				
				$err = true;
			} else {
				// ��������������Ǽ���롣
				$objFormParam->setParam($arrCSV, true);
				$arrRet = $objFormParam->getHashArray();
				// �ͤ�ե����ޥå��Ѵ����Ƴ�Ǽ���롣
				$arrRet = lfConvFormat($arrRet);
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
				gfPrintLog("write $line");
				lfInsertProduct($objQuery);
				$regist++;
			}
		}
		fclose($fp);
		
		if(!$err) {
			$objQuery->commit();
			
			gfPrintLog("commit csv:$regist");
						
			$objPage->tpl_oktitle = "��" . $regist . "��Υ쥳���ɤ���Ͽ���ޤ�����";
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
	
	$objFormParam->addParam("����̾", "name", MTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("���ƥ���ID", "category_id", INT_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("���ʥ�����", "product_code", STEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("���ʲ���", "price02", PRICE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("���ʲ���", "price01", PRICE_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�߸˿�", "stock", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("��������", "sale_limit", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�᡼����URL", "comment1", LTEXT_LEN, "KVa", array("URL_CHECK", "SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("���ʥ��ơ�����", "product_flag", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�ݥ������ͿΨ", "point_rate", PERCENTAGE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�ᥤ�����������", "main_list_comment", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�ᥤ�󥳥���", "main_comment", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	
	for($i = 1; $i <= PRODUCTSUB_MAX; $i++) {
		$objFormParam->addParam("�ܺ�-���֥����ȥ�($i)", "sub_title$i", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
		$objFormParam->addParam("�ܺ�-���֥�����($i)", "sub_comment$i", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
		$objFormParam->addParam("�ܺ�-���ֲ���($i)", "sub_image$i", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK","FIND_FILE"));
		$objFormParam->addParam("�ܺ�-���ֲ�������($i)", "sub_large_image$i", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK","FIND_FILE"));
	}
		
	$objFormParam->addParam("�ᥤ���������", "main_list_image", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK","FIND_FILE"));
	$objFormParam->addParam("�ᥤ��ܺٲ���", "main_image", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK","FIND_FILE"));
	$objFormParam->addParam("�ᥤ��ܺٳ������", "main_large_image", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK","FIND_FILE"));
	$objFormParam->addParam("��Ӳ���", "file1", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK","FIND_FILE"));
	$objFormParam->addParam("���ʾܺ٥ե�����", "file2", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����", "deliv_fee", PRICE_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�߸�̵����", "stock_unlimited", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("����̵����", "sale_unlimited", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
}

/* �ü���ܤ��Ѵ� */
function lfConvFormat($array) {
	global $arrDISP;
	foreach($array as $key => $val) {
		switch($key) {
		case 'status':
			$arrRet[$key] = sfSearchKey($arrDISP, $val, 1);
			break;
		default:
			$arrRet[$key] = $val;
			break;
		}
	}
	return $arrRet;
}

/* ���ʤο����ɲ� */
function lfInsertProduct($objQuery) {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();
	
	// ���ʤ���Ͽ������ͤ�������롣
	foreach($arrRet as $key => $val) {
		switch($key) {
		case 'product_code':
		case 'price01':
		case 'price02':
		case 'stock':
		case 'stock_unlimited':
			break;
		default:
			$sqlval[$key] = $val;
			break;
		}
	}

	if (DB_TYPE == "pgsql") {
		$product_id = $objQuery->nextval("dtb_products", "product_id");
		$sqlval['product_id'] = $product_id;
	}

	$sqlval['status'] = 1;	// ɽ�������ꤹ�롣
	$sqlval['update_date'] = "Now()";
	$sqlval['create_date'] = "Now()";
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['rank'] = $objQuery->max("dtb_products", "rank", "del_flg = 0 AND category_id = ?", array($sqlval['category_id'])) + 1;
	
	// ������Ͽ
	sfInsertProductClass($objQuery, $arrRet, $product_id);
	
	gfPrintLog("insert productclass end");
	
	// INSERT�μ¹�
	$objQuery->insert("dtb_products", $sqlval);
	
	if (DB_TYPE == "mysql") {
		$product_id = $objQuery->nextval("dtb_products", "product_id");	
	}
	
	gfPrintLog("insert product end");
}

/* �������ƤΥ����å� */
function lfCheckError() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError(false);
	
	if(!isset($objErr->arrErr['category_id'])) {
		$objQuery = new SC_Query();
		$col = "level";
		$table = "dtb_category";
		$where = "category_id = ?";
		$level = $objQuery->get($table, $col, $where, array($arrRet['category_id']));
		if($level != LEVEL_MAX) {
			$objErr->arrErr['category_id'] = "�� ���Υ��ƥ���ID�ˤϾ��ʤ���Ͽ�Ǥ��ޤ���";
		}
	}
	return $objErr->arrErr;
}
?>