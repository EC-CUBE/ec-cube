<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "include/page_layout.inc");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrSTATUS_IMAGE;
		$this->arrSTATUS_IMAGE = $arrSTATUS_IMAGE;
		global $arrDELIVERYDATE;
		$this->arrDELIVERYDATE = $arrDELIVERYDATE;
		global $arrRECOMMEND;
		$this->arrRECOMMEND = $arrRECOMMEND;
		
		//$this->tpl_mainpage="products/detail.tpl";
		
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCustomer = new SC_Customer();
$objQuery = new SC_Query();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "products/detail.php");

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

// �ե�����������饹
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
// �ե��������ν����
lfInitFile();

// �����ڡ�������γ�ǧ�ξ��ϡ�������ξ��ʤ�ɽ�����롣
if($_GET['admin'] == 'on') {
	$where = "del_flg = 0";
} else {
	$where = "del_flg = 0 AND status = 1";
}

if($_POST['mode'] != "") {
	$tmp_id = $_POST['product_id'];
} else {
	$tmp_id = $_GET['product_id'];
}

// �ͤ������������å�
if(!sfIsInt($_GET['product_id']) || !sfIsRecord("dtb_products", "product_id", $tmp_id, $where)) {
	sfDispSiteError(PRODUCT_NOT_FOUND);
}
// ������Ƚ��
if($objCustomer->isLoginSuccess()) {
	//����������ܥ���ɽ��
	$objPage->tpl_login = true;

/* ��������ǽ�ϸ���̤����
	
	$table = "dtb_customer_reading";
	$where = "customer_id = ? ";
	$arrval[] = $objCustomer->getValue('customer_id');
	//�ܵҤα������ʿ�
	$rpcnt = $objQuery->count($table, $where, $arrval);

	//��������������ʲ�
	if ($rpcnt < CUSTOMER_READING_MAX){
		//��������˿����ɲ�
		lfRegistReadingData($tmp_id, $objCustomer->getValue('customer_id'));
	} else {
		//�����������ǰ��ָŤ���Τ������ƿ����ɲ�
		$oldsql = "SELECT MIN(update_date) FROM ".$table." WHERE customer_id = ?";
		$old = $objQuery->getone($oldsql, array($objCustomer->getValue("customer_id")));
		$where = "customer_id = ? AND update_date = ? ";
		$arrval = array($objCustomer->getValue("customer_id"), $old);
		//���
		$objQuery->delete($table, $where, $arrval);
		//�ɲ�
		lfRegistReadingData($tmp_id, $objCustomer->getValue('customer_id'));
	}
*/
}


// �������򥻥쥯�ȥܥå����κ���
$objPage = lfMakeSelect($objPage, $tmp_id);

// ����ID��FORM����ݻ����롣
$objPage->tpl_product_id = $tmp_id;

switch($_POST['mode']) {
case 'cart':
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError();
	if(count($objPage->arrErr) == 0) {
		$objCartSess = new SC_CartSession();
		$classcategory_id1 = $_POST['classcategory_id1'];
		$classcategory_id2 = $_POST['classcategory_id2'];
				
		// ����1�����ꤵ��Ƥ��ʤ����
		if(!$objPage->tpl_classcat_find1) {
			$classcategory_id1 = '0';
		}
		
		// ����2�����ꤵ��Ƥ��ʤ����
		if(!$objPage->tpl_classcat_find2) {
			$classcategory_id2 = '0';
		}
		
		$objCartSess->setPrevURL($_SERVER['REQUEST_URI']);
		$objCartSess->addProduct(array($_POST['product_id'], $classcategory_id1, $classcategory_id2), $objFormParam->getValue('quantity'));
		header("Location: " . URL_CART_TOP);

		exit;
	}
	break;
		
default:
	break;
}

$objQuery = new SC_Query();
// DB���龦�ʾ����������롣
$arrRet = $objQuery->select("*", "vw_products_allclass_detail AS alldtl", "product_id = ?", array($tmp_id));
$objPage->arrProduct = $arrRet[0];

// ���ʥ����ɤμ���
$code_sql = "SELECT product_code FROM dtb_products_class AS prdcls WHERE prdcls.product_id = ? GROUP BY product_code ORDER BY product_code";
$arrProductCode = $objQuery->getall($code_sql, array($tmp_id));
$arrProductCode = sfswaparray($arrProductCode);
$objPage->arrProductCode = $arrProductCode["product_code"];

// �������¿������
if($objPage->arrProduct['sale_unlimited'] == 1 || $objPage->arrProduct['sale_limit'] > SALE_LIMIT_MAX) {
  $objPage->tpl_sale_limit = SALE_LIMIT_MAX;
} else {
  $objPage->tpl_sale_limit = $objPage->arrProduct['sale_limit'];
}

// ���֥����ȥ�����
$arrFirstCat = GetFirstCat($arrRet[0]['category_id']);
$tpl_subtitle = $arrFirstCat['name'];
$objPage->tpl_subtitle = $tpl_subtitle;

// DB����Υǡ���������Ѥ�
$objUpFile->setDBFileList($objPage->arrProduct);
// �ե�����ɽ����������Ϥ�
$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL, true);
// ��ʧ��ˡ�μ���
$objPage->arrPayment = lfGetPayment();
// ���Ͼ�����Ϥ�
$objPage->arrForm = $objFormParam->getFormParamList();
//��ӥ塼����μ���
$objPage->arrReview = lfGetReviewData($tmp_id);
// �����ȥ�˾���̾�������
$objPage->tpl_title = "���ʾܺ� ". $objPage->arrProduct["name"];
//�������ᾦ�ʾ���ɽ��
$objPage->arrRecommend = lfPreGetRecommendProducts($tmp_id);
//���ξ��ʤ���ä��ͤϤ���ʾ��ʤ���äƤ��ޤ�
$objPage->arrRelateProducts = lfGetRelateProducts($tmp_id);

// ��������Υ�����ɥ��������򥻥å�
list($large_width, $large_height) = getimagesize(IMAGE_SAVE_DIR . basename($objPage->arrFile["main_large_image"]["filepath"]));
$objPage->tpl_large_width = $large_width + 60;
$objPage->tpl_large_height = $large_height + 80;

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
/* �ե��������ν���� */
function lfInitFile() {
	global $objUpFile;
	$objUpFile->addFile("����-�ᥤ�����", 'main_list_image', array('jpg','gif'),IMAGE_SIZE, true, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
	$objUpFile->addFile("�ܺ�-�ᥤ�����", 'main_image', array('jpg'), IMAGE_SIZE, true, NORMAL_IMAGE_WIDTH, NORMAL_IMAGE_HEIGHT);
	$objUpFile->addFile("�ܺ�-�ᥤ��������", 'main_large_image', array('jpg'), IMAGE_SIZE, false, LARGE_IMAGE_HEIGHT, LARGE_IMAGE_HEIGHT);
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$objUpFile->addFile("�ܺ�-���ֲ���$cnt", "sub_image$cnt", array('jpg'), IMAGE_SIZE, false, NORMAL_SUBIMAGE_HEIGHT, NORMAL_SUBIMAGE_HEIGHT);	
		$objUpFile->addFile("�ܺ�-���ֳ������$cnt", "sub_large_image$cnt", array('jpg'), IMAGE_SIZE, false, LARGE_SUBIMAGE_HEIGHT, LARGE_SUBIMAGE_HEIGHT);
	}
	$objUpFile->addFile("������Ӳ���", 'file1', array('jpg'), IMAGE_SIZE, false, NORMAL_IMAGE_HEIGHT, NORMAL_IMAGE_HEIGHT);
	$objUpFile->addFile("���ʾܺ٥ե�����", 'file2', array('pdf'), PDF_SIZE, false, 0, 0, false);
}

/* �������򥻥쥯�ȥܥå����κ��� */
function lfMakeSelect($objPage, $product_id) {
	global $objPage;
	$classcat_find1 = false;
	$classcat_find2 = false;
	// �߸ˤ���ξ��ʤ�̵ͭ
	$stock_find = false;
	
	// ����̾����
	$arrClassName = sfGetIDValueList("dtb_class", "class_id", "name");
	// ����ʬ��̾����
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
	// ���ʵ��ʾ���μ���	
	$arrProductsClass = lfGetProductsClass($product_id);
	
	// ����1���饹̾�μ���
	$objPage->tpl_class_name1 = $arrClassName[$arrProductsClass[0]['class_id1']];
	// ����2���饹̾�μ���
	$objPage->tpl_class_name2 = $arrClassName[$arrProductsClass[0]['class_id2']];
	
	// ���٤Ƥ��Ȥ߹�碌��	
	$count = count($arrProductsClass);
	
	$classcat_id1 = "";
	
	$arrSele = array();
	$arrList = array();
	
	$list_id = 0;
	$arrList[0] = "\tlist0 = new Array('���򤷤Ƥ�������'";
	$arrVal[0] = "\tval0 = new Array(''";
	
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
			$arrList[$list_id] = "\tlist".$list_id." = new Array('���򤷤Ƥ�������', '".$arrClassCatName[$classcat_id2]."'";
		} else {
			$arrList[$list_id].= ", '".$arrClassCatName[$classcat_id2]."'";
		}
		
		// ���쥯�ȥܥå���POST��
		if($arrVal[$list_id] == "") {
			$arrVal[$list_id] = "\tval".$list_id." = new Array('', '".$classcat_id2."'";
		} else {
			$arrVal[$list_id].= ", '".$classcat_id2."'";
		}
	}	
	
	$arrList[$list_id].=");\n";
	$arrVal[$list_id].=");\n";
		
	// ����1
	$objPage->arrClassCat1 = $arrSele;
	
	$lists = "\tlists = new Array(";
	$no = 0;
	
	foreach($arrList as $val) {
		$objPage->tpl_javascript.= $val;
		if ($no != 0) {
			$lists.= ",list".$no;
		} else {
			$lists.= "list".$no;
		}
		$no++;
	}
	$objPage->tpl_javascript.=$lists.");\n";
	
	$vals = "\tvals = new Array(";
	$no = 0;
	
	foreach($arrVal as $val) {
		$objPage->tpl_javascript.= $val;
		if ($no != 0) {
			$vals.= ",val".$no;
		} else {
			$vals.= "val".$no;
		}
		$no++;
	}
	$objPage->tpl_javascript.=$vals.");\n";
	
	// ���򤵤�Ƥ��뵬��2ID
	$objPage->tpl_onload = "lnSetSelect('form1', 'classcategory_id1', 'classcategory_id2', '" . $_POST['classcategory_id2'] . "');";

	// ����1�����ꤵ��Ƥ���
	if($arrProductsClass[0]['classcategory_id1'] != '0') {
		$classcat_find1 = true;
	}
	
	// ����2�����ꤵ��Ƥ���
	if($arrProductsClass[0]['classcategory_id2'] != '0') {
		$classcat_find2 = true;
	}
		
	$objPage->tpl_classcat_find1 = $classcat_find1;
	$objPage->tpl_classcat_find2 = $classcat_find2;
	$objPage->tpl_stock_find = $stock_find;
		
	return $objPage;
}

/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;

	$objFormParam->addParam("����1", "classcategory_id1", INT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����2", "classcategory_id2", INT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("�Ŀ�", "quantity", INT_LEN, "n", array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
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

/* ��Ͽ�Ѥߥ������ᾦ�ʤ��ɤ߹��� */
function lfPreGetRecommendProducts($product_id) {
	$objQuery = new SC_Query();
	$objQuery->setorder("rank DESC");
	$arrRet = $objQuery->select("recommend_product_id, comment", "dtb_recommend_products", "product_id = ?", array($product_id));
	$max = count($arrRet);
	$no = 0;
	for($i = 0; $i < $max; $i++) {
		$where = "del_flg = 0 AND product_id = ? AND status = 1";
		$arrProductInfo = $objQuery->select("main_list_image, price02_min, price02_max, price01_min, price01_max, name, point_rate", "vw_products_allclass  AS allcls", $where, array($arrRet[$i]['recommend_product_id'])); 
				
		if(count($arrProductInfo) > 0) {
			$arrRecommend[$no] = $arrProductInfo[0];
			$arrRecommend[$no]['product_id'] = $arrRet[$i]['recommend_product_id'];
			$arrRecommend[$no]['comment'] = $arrRet[$i]['comment'];
			$no++;
		}	
	}
	return $arrRecommend;
}

/* �������ƤΥ����å� */
function lfCheckError() {
	global $objFormParam;
	global $objPage;
	// ���ϥǡ������Ϥ���
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
		
	// ʣ�����ܥ����å�
	if ($objPage->tpl_classcat_find1) {
		$objErr->doFunc(array("����1", "classcategory_id1"), array("EXIST_CHECK"));
	}
	if ($objPage->tpl_classcat_find2) {
		$objErr->doFunc(array("����2", "classcategory_id2"), array("EXIST_CHECK"));
	}
			
	return $objErr->arrErr;
}

//�������򿷵���Ͽ
function lfRegistReadingData($tmp_id, $customer_id){
	$objQuery = new SC_Query;
	$sqlval['customer_id'] = $customer_id;
	$sqlval['reading_product_id'] = $tmp_id;
	$sqlval['create_date'] = 'NOW()';
	$sqlval['update_date'] = 'NOW()';
	$objQuery->insert("dtb_customer_reading", $sqlval);
}

//���ξ��ʤ���ä��ͤϤ���ʾ��ʤ���äƤ��ޤ�
function lfGetRelateProducts($tmp_id) {
	$objQuery = new SC_Query;
	//��ư���
	$objQuery->setorder("random()");
	//ɽ�����������
	$objQuery->setlimit(RELATED_PRODUCTS_MAX);
	//�������
	$col = "name, main_list_image, price01_min, price02_min, price01_max, price02_max, point_rate";
	$from = "vw_products_allclass AS allcls ";
	$where = "del_flg = 0 AND status = 1 AND (stock_max <> 0 OR stock_max IS NULL) AND product_id = ? ";
	$arrval[] = $tmp_id;
	//��̤μ���
	$arrProducts = $objQuery->select($col, $from, $where, $arrval);
	
	return $arrProducts;
}

//���ʤ��ȤΥ�ӥ塼������������
function lfGetReviewData($id) {
	$objQuery = new SC_Query;
	//���ʤ��ȤΥ�ӥ塼������������
	$col = "create_date, reviewer_url, reviewer_name, recommend_level, title, comment";
	$from = "dtb_review";
	$where = "del_flg = 0 AND status = 1 AND product_id = ? ";
	$arrval[] = $id;
	$arrReview = $objQuery->select($col, $from, $where, $arrval);
	return $arrReview; 
}

//��ʧ��ˡ�μ���
//payment_id	1:���쥸�åȡ�2:����åԥ󥰥���	
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