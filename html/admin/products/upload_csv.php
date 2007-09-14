<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
mb_language('Japanese');

require_once("../require.php");

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
	$arrErr['csv_file'] = $objUpFile->makeTempFile('csv_file');

	if($arrErr['css_file'] == "") {
		$arrErr = $objUpFile->checkEXISTS();
	}

	// �¹Ի��֤����¤��ʤ�
	set_time_limit(0);

	// ���Ϥ�Хåե���󥰤��ʤ�(==���ܸ켫ư�Ѵ��⤷�ʤ�)
	ob_end_clean();

	// IE�Τ����256�Х��ȶ�ʸ������
	echo str_pad('',256);

	if($arrErr['csv_file'] == "") {
		// ����ե�����̾�μ���
		$filepath = $objUpFile->getTempFilePath('csv_file');
		// ���󥳡���
		$enc_filepath = sfEncodeFile($filepath, CHAR_CODE, CSV_TEMP_DIR);

		// �쥳���ɿ�������
		$rec_count = lfCSVRecordCount($enc_filepath);

		$fp = fopen($enc_filepath, "r");
		$line = 0;		// �Կ�
		$regist = 0;	// ��Ͽ��

		$objQuery = new SC_Query();
		$objQuery->begin();

		echo "����CSV��Ͽ��Ľ���� <br/><br/>\n";

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
				echo "�� ���ܿ���" . $max . "�ĸ��Ф���ޤ��������ܿ���" . $colmax . "�Ĥˤʤ�ޤ���</br>\n";
				$err = true;
			} else {
				// ��������������Ǽ���롣
				$objFormParam->setParam($arrCSV, true);
				$arrRet = $objFormParam->getHashArray();
				$objFormParam->setParam($arrRet);
				// �����ͤ��Ѵ�
				$objFormParam->convParam();
				// <br>�ʤ��ǥ��顼�������롣
				$arrCSVErr = lfCheckError();
			}

			// ���ϥ��顼�����å�
			if(count($arrCSVErr) > 0) {
				echo "<font color=\"red\">��" . $line . "���ܤǥ��顼��ȯ�����ޤ�����</font></br>\n";
				foreach($arrCSVErr as $val) {
					echo "<font color=\"red\">" . htmlspecialchars($val, ENT_QUOTES) . "</font></br>\n";
				}
				$err = true;
			}

			if(!$err) {
				lfRegistProduct($objQuery, $line);
				$regist++;
			}
			$arrParam = $objFormParam->getHashArray();

			if(!$err) echo $line." / ".$rec_count. "���ܡ��ʾ���ID��".$arrParam['product_id']." / ����̾��".$arrParam['name'].")\n<br />";
			flush();
		}
		fclose($fp);

		if(!$err) {
			$objQuery->commit();
			echo "��" . $regist . "��Υ쥳���ɤ���Ͽ���ޤ�����";
			// ���ʷ��������ȴؿ��μ¹�
			sfCategory_Count($objQuery);
		} else {
			$objQuery->rollback();
		}
	} else {
		foreach($arrErr as $val) {
			echo "<font color=\"red\">$val</font></br>\n";
		}
	}
	echo "<br/><a href=\"javascript:window.close()\">���Ĥ���</a>";
	flush();
	exit;
	break;
default:
	break;
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//--------------------------------------------------------------------------------------------------------------------------

/*
 * �ؿ�̾��lfInitFile
 * ���������ե��������ν����
 */function lfInitFile() {
	global $objUpFile;
	$objUpFile->addFile("CSV�ե�����", 'csv_file', array('csv'), CSV_SIZE, true, 0, 0, false);
}

/*
 * �ؿ�̾��lfInitParam
 * �����������Ͼ���ν����
 */
function lfInitParam() {
	global $objFormParam;

	$objFormParam->addParam("����ID", "product_id", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("���ʵ���ID", "product_class_id", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));

	$objFormParam->addParam("����̾1", "dummy1");
	$objFormParam->addParam("����̾2", "dummy2");

	$objFormParam->addParam("����̾", "name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�����ե饰(1:���� 2:�����)", "status", INT_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("���ʥ��ơ�����", "product_flag", INT_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("���ʥ�����", "product_code", STEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam(NORMAL_PRICE_TITLE, "price01", PRICE_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam(SALE_PRICE_TITLE, "price02", PRICE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�߸˿�", "stock", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("����", "deliv_fee", PRICE_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�ݥ������ͿΨ", "point_rate", PERCENTAGE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("��������", "sale_limit", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("�᡼����URL", "comment1", URL_LEN, "KVa", array("SPTAB_CHECK","URL_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�������", "comment3", LLTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
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

    for ($cnt = 1; $cnt <= RECOMMEND_PRODUCT_MAX; $cnt++) {
        $objFormParam->addParam("�������ᾦ��($cnt)", "recommend_product_id$cnt", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
        $objFormParam->addParam("�ܺ�-���֥�����($cnt)", "recommend_comment$cnt", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
    }

	$objFormParam->addParam("���ʥ��ƥ���", "category_id", STEXT_LEN, "n", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
}

/*
 * �ؿ�̾��lfRegistProduct
 * ����1 ��SC_Query���֥�������
 * ��������������Ͽ
 */
function lfRegistProduct($objQuery, $line = "") {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();

	// dtb_products�ʳ�����Ͽ������ͤ�������롣
	foreach($arrRet as $key => $val) {
		switch($key) {
		case 'product_code':
		case 'price01':
		case 'price02':
		case 'stock':
		case 'product_class_id':
		case 'recommend_product_id1':
		case 'recommend_product_id2':
		case 'recommend_product_id3':
        case 'recommend_product_id4':
        case 'recommend_product_id5':
        case 'recommend_product_id6':
		case 'recommend_comment1':
		case 'recommend_comment2':
		case 'recommend_comment3':
		case 'recommend_comment4':
        case 'recommend_comment5':
        case 'recommend_comment6':
			break;
		default:
			if(!ereg("^dummy", $key)) {
				$sqlval[$key] = $val;
			}
			break;
		}
	}
	// ��Ͽ���֤�����(DB��now()����commit�����ݡ����٤�Ʊ��λ��֤ˤʤäƤ��ޤ�)
	$time = date("Y-m-d H:i:s");
	// �ðʲ�������
	if($line != "") {
		$microtime = sprintf("%06d", $line);
		$time .= ".$microtime";
	}
	$sqlval['update_date'] = $time;
	$sqlval['creator_id'] = $_SESSION['member_id'];

	if($sqlval['sale_limit'] == "") {
		$sqlval['sale_unlimited'] = '1';
	} else {
		$sqlval['sale_unlimited'] = '0';
	}

	if($sqlval['status'] == "") {
		$sqlval['status'] = 2;
	}

	if($arrRet['product_id'] != "" && $arrRet['product_class_id'] != "") {
		// ���ƥ������󥯤�Ĵ������
		$old_catid = $objQuery->get("dtb_products", "category_id", "product_id = ?", array($arrRet['product_id']));
		sfMoveCatRank($objQuery, "dtb_products", "product_id", "category_id", $old_catid, $arrRet['category_id'], $arrRet['product_id']);

		// UPDATE�μ¹�
		$where = "product_id = ?";
		$objQuery->update("dtb_products", $sqlval, $where, array($sqlval['product_id']));
	} else {

        // postgresql��mysql�Ȥǽ�����ʬ����
        if (DB_TYPE == "pgsql") {
            $product_id = $objQuery->nextval("dtb_products","product_id");
        }elseif (DB_TYPE == "mysql") {
            $product_id = $objQuery->get_auto_increment("dtb_products");
        }
		$sqlval['product_id'] = $product_id;
		// ������Ͽ
        // postgresql��mysql�Ȥǽ�����ʬ����
        if (DB_TYPE == "pgsql") {
            $product_id = $objQuery->nextval("dtb_products","product_id");
        }elseif (DB_TYPE == "mysql") {
            $product_id = $objQuery->get_auto_increment("dtb_products");
        }

        $sqlval['product_id'] = $product_id;
		$sqlval['create_date'] = $time;

		// ���ƥ�����Ǻ���Υ�󥯤������Ƥ�
		$sqlval['rank'] = $objQuery->max("dtb_products", "rank", "category_id = ?", array($arrRet['category_id'])) + 1;

		// INSERT�μ¹�
		$objQuery->insert("dtb_products", $sqlval);
	}

	// ������Ͽ
	lfRegistProductClass($objQuery, $arrRet, $sqlval['product_id'], $arrRet['product_class_id']);

	// �������ᾦ����Ͽ
	$objQuery->delete("dtb_recommend_products", "product_id = ?", array($sqlval['product_id']));
	for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
		$keyname = "recommend_product_id" . $i;
		$comment_key = "recommend_comment" . $i;
		if($arrRet[$keyname] != "") {
			$arrProduct = $objQuery->select("product_id", "dtb_products", "product_id = ?", array($arrRet[$keyname]));
			if($arrProduct[0]['product_id'] != "") {
				$arrval['product_id'] = $sqlval['product_id'];
				$arrval['recommend_product_id'] = $arrProduct[0]['product_id'];
				$arrval['comment'] = $arrRet[$comment_key];
				$arrval['update_date'] = "Now()";
				$arrval['create_date'] = "Now()";
				$arrval['creator_id'] = $_SESSION['member_id'];
				$arrval['rank'] = RECOMMEND_PRODUCT_MAX - $i + 1;
				$objQuery->insert("dtb_recommend_products", $arrval);
			}
		}
	}
}

/*
 * �ؿ�̾��lfRegistProductClass
 * ����1 ��SC_Query���֥�������
 * ����2 �����ʵ��ʾ�������
 * ����3 ������ID
 * ����4 �����ʵ���ID
 * �����������ʵ�����Ͽ
 */
function lfRegistProductClass($objQuery, $arrList, $product_id, $product_class_id) {
	$sqlval['product_code'] = $arrList["product_code"];
	$sqlval['stock'] = $arrList["stock"];
	if($sqlval['stock'] == "") {
		$sqlval['stock_unlimited'] = '1';
	} else {
		$sqlval['stock_unlimited'] = '0';
	}
	$sqlval['price01'] = $arrList['price01'];
	$sqlval['price02'] = $arrList['price02'];
	$sqlval['creator_id'] = $_SESSION['member_id'];
	if($sqlval['member_id'] == "") {
		$sqlval['creator_id'] = '0';
	}

	if($product_class_id == "") {
		// ������Ͽ
		$where = "product_id = ?";
		// ǰ�Τ���˴�¸�ε��ʤ���
		$objQuery->delete("dtb_products_class", $where, array($product_id));
		$sqlval['product_id'] = $product_id;
		$sqlval['classcategory_id1'] = '0';
		$sqlval['classcategory_id2'] = '0';
		$sqlval['create_date'] = "now()";
		$objQuery->insert("dtb_products_class", $sqlval);
	} else {
		// ��¸�Խ�
		$where = "product_id = ? AND product_class_id = ?";
		$objQuery->update("dtb_products_class", $sqlval, $where, array($product_id, $product_class_id));
	}
}

/*
 * �ؿ�̾��lfCheckError
 * �����������ϥ����å�
 */
function lfCheckError() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError(false);

	if(count($objErr->arrErr) == 0) {
		$objQuery = new SC_Query();
		// ����ID������ID��¸�ߥ����å�
		if($arrRet['product_id'] != "") {
			$count = $objQuery->count("dtb_products", "product_id = ?", array($arrRet['product_id']));
			if($count == 0) {
				$objErr->arrErr['product_id'] = "�� ����ξ���ID�ϡ���Ͽ����Ƥ��ޤ���";
			}
		}

		if($arrRet['product_class_id'] != "") {
			$count = 0;
			if($arrRet['product_id'] != "") {
				$count = $objQuery->count("dtb_products_class", "product_id = ? AND product_class_id = ?", array($arrRet['product_id'], $arrRet['product_class_id']));
			}
			if($count == 0) {
				$objErr->arrErr['product_class_id'] = "�� ����ε���ID�ϡ���Ͽ����Ƥ��ޤ���";
			}
		}

		// ¸�ߤ��륫�ƥ���ID�������å�
		$count = $objQuery->count("dtb_category", "category_id = ?", array($arrRet['category_id']));
		if($count == 0) {
			$objErr->arrErr['product_id'] = "�� ����Υ��ƥ���ID�ϡ���Ͽ����Ƥ��ޤ���";
		}
	}
	return $objErr->arrErr;
}

/*
 * �ؿ�̾��lfCSVRecordCount
 * ��������CSV�Υ�����ȿ�������
 * ����1 ���ե�����ѥ�
 */
function lfCSVRecordCount($file_name) {

	$count = 0;
	$fp = fopen($file_name, "r");
	while(!feof($fp)) {
		$arrCSV = fgetcsv($fp, CSV_LINE_MAX);
		$count++;
	}

	return $count-1;
}
?>