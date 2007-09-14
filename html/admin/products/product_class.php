<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrCatList;
	var $arrSRANK;
	var $arrForm;
	var $arrSubList;
	var $arrHidden;
	var $arrTempImage;
	var $arrSaveImage;
	var $tpl_mode;
	var $arrSearchHidden;
	function LC_Page() {
		$this->tpl_mainpage = 'products/product_class.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'product';
		$this->tpl_subtitle = '������Ͽ';
		global $arrSRANK;
		$this->arrSRANK = $arrSRANK;
		global $arrDISP;
		$this->arrDISP = $arrDISP;
		global $arrCLASS;
		$this->arrCLASS = $arrCLASS;
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		$this->tpl_onload = "";
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// �����ѥ�᡼���ΰ����Ѥ�
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrSearchHidden[$key] = $val;	
	}
}

$objPage->tpl_product_id = $_POST['product_id'];
$objPage->tpl_pageno = $_POST['pageno'];

switch($_POST['mode']) {
// ���ʺ���׵�
case 'delete':
	$objQuery = new SC_Query();
	
	$objQuery->setLimitOffset(1);
	$where = "product_id = ? AND NOT (classcategory_id1 = 0 AND classcategory_id2 = 0)";
	$objQuery->setOrder("rank1 DESC, rank2 DESC");
	$arrRet = $objQuery->select("*", "vw_cross_products_class AS crs_prd", $where, array($_POST['product_id']));
	
	if(count($arrRet) > 0) {

		$sqlval['product_id'] = $arrRet[0]['product_id'];
		$sqlval['classcategory_id1'] = '0';
		$sqlval['classcategory_id2'] = '0';
		$sqlval['product_code'] = $arrRet[0]['product_code'];
		$sqlval['stock'] = $arrRet[0]['stock'];
		$sqlval['price01'] = $arrRet[0]['price01'];
		$sqlval['price02'] = $arrRet[0]['price02'];
		$sqlval['creator_id'] = $_SESSION['member_id'];
		$sqlval['create_date'] = "now()";
		$sqlval['update_date'] = "now()";

		$objQuery->begin();
		$where = "product_id = ?";
		$objQuery->delete("dtb_products_class", $where, array($_POST['product_id']));		
		$objQuery->insert("dtb_products_class", $sqlval);
		
		$objQuery->commit();
	}
	
	lfProductClassPage();	// ������Ͽ�ڡ���	
	break;
	
// �Խ��׵�
case 'pre_edit':
	$objQuery = new SC_Query();
	$where = "product_id = ? AND NOT(classcategory_id1 = 0 AND classcategory_id2 = 0) ";
	$ret = $objQuery->count("dtb_products_class", $where, array($_POST['product_id']));
	
	if($ret > 0) {
		// �����Ȥ߹�碌�����μ���(DB���ͤ�ͥ�褹�롣)
		$objPage->arrClassCat = lfGetClassCatListEdit($_POST['product_id']);	
	}
	
	lfProductClassPage();	// ������Ͽ�ڡ���
	break;
// �����Ȥ߹�碌ɽ��
case 'disp':
	$objPage->arrForm['select_class_id1'] = $_POST['select_class_id1'];
	$objPage->arrForm['select_class_id2'] = $_POST['select_class_id2'];

	$objPage->arrErr = lfClassError();
	if (count($objPage->arrErr) == 0) {
		// �����Ȥ߹�碌�����μ���
		$objPage->arrClassCat = lfGetClassCatListDisp($_POST['select_class_id1'], $_POST['select_class_id2']);
	}
	
	lfProductClassPage();	// ������Ͽ�ڡ���
	break;
// ������Ͽ�׵�
case 'edit':
	// �����ͤ��Ѵ�
	$objPage->arrForm = lfConvertParam($_POST);
	// ���顼�����å�
	$objPage->arrErr = lfProductClassError($objPage->arrForm);
	
	if(count($objPage->arrErr) == 0) {
		// ��ǧ�ڡ�������
		$objPage->tpl_mainpage = 'products/product_class_confirm.tpl';
		lfProductConfirmPage(); // ��ǧ�ڡ���ɽ��
	} else {
		// �����Ȥ߹�碌�����μ���
		$objPage->arrClassCat = lfGetClassCatListDisp($_POST['class_id1'], $_POST['class_id2'], false);
		lfProductClassPage();	// ������Ͽ�ڡ���
	}
	break;
// ��ǧ�ڡ�����������
case 'confirm_return':
	// �ե�����ѥ�᡼���ΰ����Ѥ�
	$objPage->arrForm = $_POST;
	// ���ʤ��������ϰ����Ѥ��ʤ���
	$objPage->arrForm['select_class_id1'] = "";
	$objPage->arrForm['select_class_id2'] = "";
	// �����Ȥ߹�碌�����μ���(�ǥե�����ͤϽ��Ϥ��ʤ�)
	$objPage->arrClassCat = lfGetClassCatListDisp($_POST['class_id1'], $_POST['class_id2'], false);
	lfProductClassPage();	// ������Ͽ�ڡ���
	break;
case 'complete':
	// ��λ�ڡ�������	
	$objPage->tpl_mainpage = 'products/product_class_complete.tpl';
	// ���ʵ��ʤ���Ͽ
	lfInsertProductClass($_POST, $_POST['product_id']);
	break;
default:
	lfProductClassPage();	// ������Ͽ�ڡ���
	break;
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------
/* ������Ͽ�ڡ���ɽ���� */
function lfProductClassPage() {
	global $objPage;
	$objPage->arrHidden = $_POST;
	$objPage->arrHidden['select_class_id1'] = "";
	$objPage->arrHidden['select_class_id2'] = "";
	$arrClass = sfGetIDValueList("dtb_class", 'class_id', 'name');
	
	// ����ʬ�ब��Ͽ����Ƥ��ʤ����ʤ�ɽ�����ʤ��褦�ˤ��롣
	$arrClassCatCount = sfGetClassCatCount();
	
	foreach($arrClass as $key => $val) {
		if($arrClassCatCount[$key] > 0) {
			$objPage->arrClass[$key] = $arrClass[$key];
		}
	}
	
	// ����̾�����
	$objQuery = new SC_Query();
	$product_name = $objQuery->getOne("SELECT name FROM dtb_products WHERE product_id = ?", array($_POST['product_id']));
	$objPage->arrForm['product_name'] = $product_name;
}

function lfSetDefaultClassCat($objQuery, $product_id, $max) {
	global $objPage;
	
	// �ǥե�����ͤ��ɹ�
	$col = "product_code, price01, price02, stock, stock_unlimited";
	$arrRet = $objQuery->select($col, "dtb_products_class", "product_id = ? AND classcategory_id1 = 0 AND classcategory_id2 = 0", array($product_id));;
	
	if(count($arrRet) > 0) {
		$no = 1;
		for($cnt = 0; $cnt < $max; $cnt++) {
			$objPage->arrForm["product_code:".$no] = $arrRet[0]['product_code'];
			$objPage->arrForm['stock:'.$no] = $arrRet[0]['stock'];
			$objPage->arrForm['price01:'.$no] = $arrRet[0]['price01'];
			$objPage->arrForm['price02:'.$no] = $arrRet[0]['price02'];
			$objPage->arrForm['stock_unlimited:'.$no] = $arrRet[0]['stock_unlimited'];
			$no++;
		}
	}
}

/* �����Ȥ߹�碌�����μ��� */
function lfGetClassCatListDisp($class_id1, $class_id2, $default = true) {
	global $objPage;
	$objQuery = new SC_Query();
		
	if($class_id2 != "") {
		// ����1�ȵ���2
		$sql = "SELECT * ";
		$sql.= "FROM vw_cross_class AS crs_cls ";
		$sql.= "WHERE class_id1 = ? AND class_id2 = ? ORDER BY rank1 DESC, rank2 DESC;";
		$arrRet = $objQuery->getall($sql, array($class_id1, $class_id2));
	} else {
		// ����1�Τ�
		$sql = "SELECT * ";
		$sql.= "FROM vw_cross_class AS crs_cls ";
		$sql.= "WHERE class_id1 = ? AND class_id2 = 0 ORDER BY rank1 DESC;";
		$arrRet = $objQuery->getall($sql, array($class_id1));
		
	}
	
	$max = count($arrRet);
	
	if($default) {
		// �ǥե�����ͤ�����
		lfSetDefaultClassCat($objQuery, $_POST['product_id'], $max);
	}
	
	$objPage->arrForm["class_id1"] = $arrRet[0]['class_id1'];
	$objPage->arrForm["class_id2"] = $arrRet[0]['class_id2'];
	$objPage->tpl_onload.= "fnCheckAllStockLimit('$max', '" . DISABLED_RGB . "');";
	
	return $arrRet;
}

/* �����Ȥ߹�碌�����μ���(�Խ�����) */
function lfGetClassCatListEdit($product_id) {
	global $objPage;
	// ��¸�Խ��ξ��
	$objQuery = new SC_Query();
	
	$col = "class_id1, class_id2, name1, name2, rank1, rank2, ";
	$col.= "product_class_id, product_id, T1_classcategory_id AS classcategory_id1, T2_classcategory_id AS classcategory_id2, ";
	$col.= "product_code, stock, stock_unlimited, sale_limit, price01, price02, status";
	
	$sql = "SELECT $col FROM ";
	$sql.= "( ";
	$sql.= "SELECT T1.class_id AS class_id1, T2.class_id AS class_id2, T1.classcategory_id AS T1_classcategory_id, T2.classcategory_id AS T2_classcategory_id, T1.name AS name1, T2.name AS name2, T1.rank AS rank1, T2.rank AS rank2 ";
	$sql.= "FROM dtb_classcategory AS T1, dtb_classcategory AS T2 ";
	$sql.= "WHERE T1.class_id IN (SELECT class_id1 FROM vw_cross_products_class AS crs_prd WHERE product_id = ? GROUP BY class_id1, class_id2) AND T2.class_id IN (SELECT class_id2 FROM vw_cross_products_class AS crs_prd WHERE product_id = ? GROUP BY class_id1, class_id2)";
	$sql.= ") AS T1 ";
			
	$sql.= "LEFT JOIN (SELECT * FROM dtb_products_class WHERE product_id = ?) AS T3 ";
	$sql.= "ON T1_classcategory_id = T3.classcategory_id1 AND T2_classcategory_id = T3.classcategory_id2 ";
	$sql.= "ORDER BY rank1 DESC, rank2 DESC";
	
	$arrList =  $objQuery->getAll($sql, array($product_id, $product_id, $product_id));
	
	$objPage->arrForm["class_id1"] = $arrList[0]['class_id1'];
	$objPage->arrForm["class_id2"] = $arrList[0]['class_id2'];
	
	$max = count($arrList);
	
	// �ǥե�����ͤ�����
	lfSetDefaultClassCat($objQuery, $product_id, $max);
	
	$no = 1;
	
	for($cnt = 0; $cnt < $max; $cnt++) {
		$objPage->arrForm["classcategory_id1:".$no] = $arrList[$cnt]['classcategory_id1'];
		$objPage->arrForm["classcategory_id2:".$no] = $arrList[$cnt]['classcategory_id2'];
		if($arrList[$cnt]['product_id'] != "") {
			$objPage->arrForm["product_code:".$no] = $arrList[$cnt]['product_code'];
			$objPage->arrForm['stock:'.$no] = $arrList[$cnt]['stock'];
			$objPage->arrForm['stock_unlimited:'.$no] = $arrList[$cnt]['stock_unlimited'];
			$objPage->arrForm['price01:'.$no] = $arrList[$cnt]['price01'];
			$objPage->arrForm['price02:'.$no] = $arrList[$cnt]['price02'];
			// JavaScript�������ʸ����
			$line.= "'check:".$no."',";			
		}
		$no++;
	}
		
	$line = ereg_replace(",$", "", $line);
	$objPage->tpl_javascript = "list = new Array($line);";
	$color = DISABLED_RGB;
	$objPage->tpl_onload.= "fnListCheck(list); fnCheckAllStockLimit('$max', '$color');";

	return $arrList;
}

/* ���ʤ���Ͽ */
function lfInsertProductClass($arrList, $product_id) {
	$objQuery = new SC_Query();
	
	$objQuery->begin();
		
	// ��¸���ʤκ��
	$where = "product_id = ?";
	$objQuery->delete("dtb_products_class", $where, array($product_id));
	
	$cnt = 1;
	// ���٤Ƥε��ʤ���Ͽ���롣
	while($arrList["classcategory_id1:".$cnt] != "") {
		if($arrList["check:".$cnt] == 1) {
			$sqlval['product_id'] = $product_id;
			$sqlval['classcategory_id1'] = $arrList["classcategory_id1:".$cnt];
			$sqlval['classcategory_id2'] = $arrList["classcategory_id2:".$cnt];
			$sqlval['product_code'] = $arrList["product_code:".$cnt];
			$sqlval['stock'] = $arrList["stock:".$cnt];
			$sqlval['stock_unlimited'] = $arrList["stock_unlimited:".$cnt];
			$sqlval['price01'] = $arrList['price01:'.$cnt];
			$sqlval['price02'] = $arrList['price02:'.$cnt];
			$sqlval['creator_id'] = $_SESSION['member_id'];
			$sqlval['create_date'] = "now()";
			$sqlval['update_date'] = "now()";
			// INSERT�μ¹�
			$objQuery->insert("dtb_products_class", $sqlval);
		}
		$cnt++;
	}
	
	$objQuery->commit();
}

// �������򥨥顼�����å�
function lfClassError() {
	$objErr = new SC_CheckError();
	$objErr->doFunc(array("����1", "select_class_id1"), array("EXIST_CHECK"));
	$objErr->doFunc(array("����", "select_class_id1", "select_class_id2"), array("TOP_EXIST_CHECK"));
	$objErr->doFunc(array("����1", "����2", "select_class_id1", "select_class_id2"), array("DIFFERENT_CHECK"));
	return $objErr->arrErr;
}

/* ����ʸ������Ѵ� */
function lfConvertParam($array) {
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 */

	$no = 1;
	while($array["classcategory_id1:".$no] != "") {
		$arrConvList["product_code:".$no] = "KVa";
		$arrConvList["price01:".$no] = "n";
		$arrConvList["price02:".$no] = "n";
		$arrConvList["stock:".$no] = "n";
		$no++;
	}
	
	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

// ���ʵ��ʥ��顼�����å�
function lfProductClassError($array) {
	$objErr = new SC_CheckError($array);
	$no = 1;
		
	while($array["classcategory_id1:".$no] != "") {
		if($array["check:".$no] == 1) {
			$objErr->doFunc(array("���ʥ�����", "product_code:".$no, STEXT_LEN), array("MAX_LENGTH_CHECK"));
			$objErr->doFunc(array(NORMAL_PRICE_TITLE, "price01:".$no, PRICE_LEN), array("ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
			$objErr->doFunc(array(SALE_PRICE_TITLE, "price02:".$no, PRICE_LEN), array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

			if($array["stock_unlimited:".$no] != '1') {
				$objErr->doFunc(array("�߸˿�", "stock:".$no, AMOUNT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
			}
		}
		if(count($objErr->arrErr) > 0) {
			$objErr->arrErr["error:".$no] = $objErr->arrErr["product_code:".$no];
			$objErr->arrErr["error:".$no].= $objErr->arrErr["price01:".$no];
			$objErr->arrErr["error:".$no].= $objErr->arrErr["price02:".$no];
			$objErr->arrErr["error:".$no].= $objErr->arrErr["stock:".$no];
		}
		$no++;
	}
	return $objErr->arrErr;
}

/* ��ǧ�ڡ���ɽ���� */
function lfProductConfirmPage() {
	global $objPage;
	$objPage->arrForm['mode'] = 'complete';
	$objPage->arrClass = sfGetIDValueList("dtb_class", 'class_id', 'name');
	$cnt = 0;
	$check = 0;
	$no = 1;
	while($_POST["classcategory_id1:".$no] != "") {
		if($_POST["check:".$no] != "") {
			$check++;
		}
		$no++;
		$cnt++;
	}
	$objPage->tpl_check = $check;
	$objPage->tpl_count = $cnt;
}
?>