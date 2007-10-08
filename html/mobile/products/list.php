<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *
 * ��Х��륵����/���ʰ���
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrSTATUS_IMAGE;
		$this->arrSTATUS_IMAGE = $arrSTATUS_IMAGE;
		global $arrDELIVERYDATE;
		$this->arrDELIVERYDATE = $arrDELIVERYDATE;
		global $arrPRODUCTLISTMAX;
		$this->arrPRODUCTLISTMAX = $arrPRODUCTLISTMAX;
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$conn = new SC_DBConn();

//ɽ�����������
if(sfIsInt($_REQUEST['disp_number'])) {
	$objPage->disp_number = $_REQUEST['disp_number'];
} else {
	//�Ǿ�ɽ�����������
	$objPage->disp_number = current(array_keys($arrPRODUCTLISTMAX));
}

//ɽ���������¸
$objPage->orderby = $_REQUEST['orderby'];

// GET�Υ��ƥ���ID�򸵤����������ƥ���ID��������롣
$category_id = sfGetCategoryId("", $_GET['category_id']);

// �����ȥ��Խ�
$tpl_subtitle = "";
$tpl_search_mode = false;
if($_GET['mode'] == 'search'){
	$tpl_subtitle = "�������";
	$tpl_search_mode = true;
}elseif ($category_id == "" ) {
	$tpl_subtitle = "������";
}else{
	$arrFirstCat = sfGetFirstCat($category_id);
	$tpl_subtitle = $arrFirstCat['name'];
}

$objQuery = new SC_Query();
$count = $objQuery->count("dtb_best_products", "category_id = ?", array($category_id));

// �ʲ��ξ���BEST���ʤ�ɽ������
// ��BEST������ξ��ʤ���Ͽ����Ƥ��롣
// �����ƥ���ID���롼��ID�Ǥ��롣
// �������⡼�ɤǤʤ���
if(($count >= BEST_MIN) && lfIsRootCategory($category_id) && ($_GET['mode'] != 'search') ) {
	// ����TOP��ɽ������
	/** ɬ�����ꤹ�� **/
	$objPage->tpl_mainpage = "products/list.tpl";		// �ᥤ��ƥ�ץ졼��

	$objPage->arrBestItems = sfGetBestProducts($conn, $category_id);
	$objPage->BEST_ROOP_MAX = ceil((BEST_MAX-1)/2);
} else {
	if ($_GET['mode'] == 'search' && strlen($_GET['category_id']) == 0 ){
		// ��������category_id��GET��¸�ߤ��ʤ����ϡ�������᤿ID�������᤹
		$category_id = '';
	}

	// ���ʰ�����ɽ������
	$objPage = lfDispProductsList($category_id, $_GET['name'], $objPage->disp_number, $_REQUEST['orderby']);

	// ����������̤�ɽ��
	// ���ƥ��꡼�������
	if (strlen($_GET['category_id']) == 0) {
		$arrSearch['category'] = "����ʤ�";
	}else{
		$arrCat = $conn->getOne("SELECT category_name FROM dtb_category WHERE category_id = ?",array($category_id));
		$arrSearch['category'] = $arrCat;
	}

	// ����̾�������
	if ($_GET['name'] === "") {
		$arrSearch['name'] = "����ʤ�";
	}else{
		$arrSearch['name'] = $_GET['name'];
	}
}

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "products/list.php");

if($_POST['mode'] == "cart" && $_POST['product_id'] != "") {
	// �ͤ������������å�
	if(!sfIsInt($_POST['product_id']) || !sfIsRecord("dtb_products", "product_id", $_POST['product_id'], "del_flg = 0 AND status = 1")) {
		sfDispSiteError(PRODUCT_NOT_FOUND, "", false, "", true);
	} else {
		// �����ͤ��Ѵ�
		$objPage->arrErr = lfCheckError($_POST['product_id']);
		if(count($objPage->arrErr) == 0) {
			$objCartSess = new SC_CartSession();
			$classcategory_id = "classcategory_id". $_POST['product_id'];
			$classcategory_id1 = $_POST[$classcategory_id. '_1'];
			$classcategory_id2 = $_POST[$classcategory_id. '_2'];
			$quantity = "quantity". $_POST['product_id'];
			// ����1�����ꤵ��Ƥ��ʤ����
			if(!$objPage->tpl_classcat_find1[$_POST['product_id']]) {
				$classcategory_id1 = '0';
			}
			// ����2�����ꤵ��Ƥ��ʤ����
			if(!$objPage->tpl_classcat_find2[$_POST['product_id']]) {
				$classcategory_id2 = '0';
			}
			$objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
			$objCartSess->addProduct(array($_POST['product_id'], $classcategory_id1, $classcategory_id2), $_POST[$quantity]);
			header("Location: " . MOBILE_URL_CART_TOP);
			exit;
		}
	}
}


// �ڡ������굡ǽ�Ѥ�URL��������롣
$objURL = new Net_URL($_SERVER['PHP_SELF']);
foreach ($_REQUEST as $key => $value) {
	if ($key == session_name() || $key == 'pageno') {
		continue;
	}
	$objURL->addQueryString($key, mb_convert_encoding($value, 'SJIS', 'EUC-JP'));
}

if ($objPage->objNavi->now_page > 1) {
	$objURL->addQueryString('pageno', $objPage->objNavi->now_page - 1);
	$objPage->tpl_previous_page = $objURL->path . '?' . $objURL->getQueryString();
}
if ($objPage->objNavi->now_page < $objPage->objNavi->max_page) {
	$objURL->addQueryString('pageno', $objPage->objNavi->now_page + 1);
	$objPage->tpl_next_page = $objURL->path . '?' . $objURL->getQueryString();
}


$objPage->tpl_subtitle = $tpl_subtitle;
$objPage->tpl_search_mode = $tpl_search_mode;

// ��ʧ��ˡ�μ���
$objPage->arrPayment = lfGetPayment();
// ���Ͼ�����Ϥ�
$objPage->arrForm = $_POST;

$objPage->category_id = $category_id;
$objPage->arrSearch = $arrSearch;

$objView = new SC_MobileView();
$objView->assignObj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
/* ���ƥ���ID���롼�Ȥ��ɤ�����Ƚ�� */
function lfIsRootCategory($category_id) {
	$objQuery = new SC_Query();
	$level = $objQuery->get("dtb_category", "level", "category_id = ?", array($category_id));
	if($level == 1) {
		return true;
	}
	return false;
}

/* ���ʰ�����ɽ�� */
function lfDispProductsList($category_id, $name, $disp_num, $orderby) {
	global $objPage;
	$objQuery = new SC_Query();
	$objPage->tpl_pageno = $_REQUEST['pageno'];

	//ɽ������ǥƥ�ץ졼�Ȥ��ڤ��ؤ���
	$objPage->tpl_mainpage = "products/list.tpl";		// �ᥤ��ƥ�ץ졼��

	//ɽ�����
	switch($orderby) {
	//���ʽ�
	case 'price':
		$order = "price02_min ASC";
		break;
	//�����
	case 'date':
		$order = "create_date DESC";
		break;
	default:
		$order = "category_rank DESC, rank DESC";
		break;
	}

	// ���ʸ������κ�����̤�����ɽ����
	$where = "del_flg = 0 AND status = 1 ";
	// ���ƥ��꤫���WHEREʸ�������
	if ( $category_id ) {
		$where .= 'AND category_id = ?';
		$arrval = array($category_id);
	}

	// ����̾��whereʸ��
	$name = ereg_replace(",", "", $name);
	if ( strlen($name) > 0 ){
		$where .= " AND ( name ILIKE ? OR comment3 ILIKE ?) ";
		$ret = sfManualEscape($name);
		$arrval[] = "%$ret%";
		$arrval[] = "%$ret%";
	}

	// �Կ��μ���
	$linemax = $objQuery->count("vw_products_allclass AS allcls", $where, $arrval);
	$objPage->tpl_linemax = $linemax;	// ���郎�������ޤ�����ɽ����

	// �ڡ�������μ���
	$objNavi = new SC_PageNavi($_REQUEST['pageno'], $linemax, $disp_num, "fnNaviPage", NAVI_PMAX);

	$strnavi = $objNavi->strnavi;
	$strnavi = str_replace('onclick="fnNaviPage', 'onclick="form1.mode.value=\''.'\'; fnNaviPage', $strnavi);
	$objPage->tpl_strnavi = $strnavi;		// ɽ��ʸ����
	$startno = $objNavi->start_row;					// ���Ϲ�

	// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
	$objQuery->setlimitoffset($disp_num, $startno);
	// ɽ�����
	$objQuery->setorder($order);
	// ������̤μ���
	$objPage->arrProducts = $objQuery->select("*", "vw_products_allclass AS allcls", $where, $arrval);

	// ����̾����
	$arrClassName = sfGetIDValueList("dtb_class", "class_id", "name");
	// ����ʬ��̾����
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
	// ��襻�쥯�ȥܥå�������
	if($disp_num == 15) {
		for($i = 0; $i < count($objPage->arrProducts); $i++) {
			$objPage = lfMakeSelect($objPage->arrProducts[$i]['product_id'], $arrClassName, $arrClassCatName);
			// �������¿������
			$objPage = lfGetSaleLimit($objPage->arrProducts[$i]);
		}
	}

	$objPage->objNavi =& $objNavi;
	return $objPage;
}

/* ���ʥ��쥯�ȥܥå����κ��� */
function lfMakeSelect($product_id, $arrClassName, $arrClassCatName) {
	global $objPage;

	$classcat_find1 = false;
	$classcat_find2 = false;
	// �߸ˤ���ξ��ʤ�̵ͭ
	$stock_find = false;

	// ���ʵ��ʾ���μ���
	$arrProductsClass = lfGetProductsClass($product_id);

	// ����1���饹̾�μ���
	$objPage->tpl_class_name1[$product_id] = $arrClassName[$arrProductsClass[0]['class_id1']];
	// ����2���饹̾�μ���
	$objPage->tpl_class_name2[$product_id] = $arrClassName[$arrProductsClass[0]['class_id2']];

	// ���٤Ƥ��Ȥ߹�碌��
	$count = count($arrProductsClass);

	$classcat_id1 = "";

	$arrSele = array();
	$arrList = array();

	$list_id = 0;
	$arrList[0] = "\tlist". $product_id. "_0 = new Array('���򤷤Ƥ�������'";
	$arrVal[0] = "\tval". $product_id. "_0 = new Array(''";

	for ($i = 0; $i < $count; $i++) {
		// �߸ˤΥ����å�
		if($arrProductsClass[$i]['stock'] <= 0 && $arrProductsClass[$i]['stock_unlimited'] != '1') {
			continue;
		}

		$stock_find = true;

		// ����1�Υ��쥯�ȥܥå�����
		if($classcat_id1 != $arrProductsClass[$i]['classcategory_id1']){
			$arrList[$list_id].=");\n";
			$arrVal[$list_id].=");\n";
			$classcat_id1 = $arrProductsClass[$i]['classcategory_id1'];
			$arrSele[$classcat_id1] = $arrClassCatName[$classcat_id1];
			$list_id++;
		}

		// ����2�Υ��쥯�ȥܥå�����
		$classcat_id2 = $arrProductsClass[$i]['classcategory_id2'];

		// ���쥯�ȥܥå���ɽ����
		if($arrList[$list_id] == "") {
			$arrList[$list_id] = "\tlist". $product_id. "_". $list_id. " = new Array('���򤷤Ƥ�������', '". $arrClassCatName[$classcat_id2]. "'";
		} else {
			$arrList[$list_id].= ", '".$arrClassCatName[$classcat_id2]."'";
		}

		// ���쥯�ȥܥå���POST��
		if($arrVal[$list_id] == "") {
			$arrVal[$list_id] = "\tval". $product_id. "_". $list_id. " = new Array('', '". $classcat_id2. "'";
		} else {
			$arrVal[$list_id].= ", '".$classcat_id2."'";
		}
	}

	$arrList[$list_id].=");\n";
	$arrVal[$list_id].=");\n";

	// ����1
	$objPage->arrClassCat1[$product_id] = $arrSele;

	$lists = "\tlists".$product_id. " = new Array(";
	$no = 0;
	foreach($arrList as $val) {
		$objPage->tpl_javascript.= $val;
		if ($no != 0) {
			$lists.= ",list". $product_id. "_". $no;
		} else {
			$lists.= "list". $product_id. "_". $no;
		}
		$no++;
	}
	$objPage->tpl_javascript.= $lists.");\n";

	$vals = "\tvals".$product_id. " = new Array(";
	$no = 0;
	foreach($arrVal as $val) {
		$objPage->tpl_javascript.= $val;
		if ($no != 0) {
			$vals.= ",val". $product_id. "_". $no;
		} else {
			$vals.= "val". $product_id. "_". $no;
		}
		$no++;
	}
	$objPage->tpl_javascript.= $vals.");\n";

	// ���򤵤�Ƥ��뵬��2ID
	$classcategory_id = "classcategory_id". $product_id;
	$objPage->tpl_onload .= "lnSetSelect('".$classcategory_id."_1','".$classcategory_id."_2','".$product_id."','".$_POST[$classcategory_id."_2"]."'); ";

	// ����1�����ꤵ��Ƥ���
	if($arrProductsClass[0]['classcategory_id1'] != '0') {
		$classcat_find1 = true;
	}

	// ����2�����ꤵ��Ƥ���
	if($arrProductsClass[0]['classcategory_id2'] != '0') {
		$classcat_find2 = true;
	}

	$objPage->tpl_classcat_find1[$product_id] = $classcat_find1;
	$objPage->tpl_classcat_find2[$product_id] = $classcat_find2;
	$objPage->tpl_stock_find[$product_id] = $stock_find;

	return $objPage;
}
/* ���ʵ��ʾ���μ��� */
function lfGetProductsClass($product_id) {
	$arrRet = array();
	if(sfIsInt($product_id)) {
		// ���ʵ��ʼ���
		$objQuery = new SC_Query();
		$col = "product_class_id, classcategory_id1, classcategory_id2, class_id1, class_id2, stock, stock_unlimited";
		$table = "vw_product_class AS prdcls";
		$where = "product_id = ?";
		$objQuery->setorder("rank1 DESC, rank2 DESC");
		$arrRet = $objQuery->select($col, $table, $where, array($product_id));
	}
	return $arrRet;
}

/* �������ƤΥ����å� */
function lfCheckError($id) {
	global $objPage;

	// ���ϥǡ������Ϥ���
	$objErr = new SC_CheckError();

	$classcategory_id1 = "classcategory_id". $id. "_1";
	$classcategory_id2 = "classcategory_id". $id. "_2";
	$quantity = "quantity". $id;
	// ʣ�����ܥ����å�
	if ($objPage->tpl_classcat_find1[$id]) {
		$objErr->doFunc(array("����1", $classcategory_id1, INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	}
	if ($objPage->tpl_classcat_find2[$id]) {
		$objErr->doFunc(array("����2", $classcategory_id2, INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	}
	$objErr->doFunc(array("�Ŀ�", $quantity, INT_LEN), array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

	return $objErr->arrErr;
}

// �������¿�������
function lfGetSaleLimit($product) {
	global $objPage;
	//�߸ˤ�̵�¤ޤ��Ϲ��������ͤ������ͤ���礭�����
	if($product['sale_unlimited'] == 1 || $product['sale_limit'] > SALE_LIMIT_MAX) {
		$objPage->tpl_sale_limit[$product['product_id']] = SALE_LIMIT_MAX;
	} else {
		$objPage->tpl_sale_limit[$product['product_id']] = $product['sale_limit'];
	}

	return $objPage;
}

//��ʧ��ˡ�μ���
//payment_id	1:��������2:��Կ�����ߡ�3:�����α
function lfGetPayment() {
	$objQuery = new SC_Query;
	$col = "payment_id, rule, payment_method";
	$from = "dtb_payment";
	$where = "del_flg = 0";
	$order = "payment_id";
	$objQuery->setorder($order);
	$arrRet = $objQuery->select($col, $from, $where);
	return $arrRet;
}

?>
