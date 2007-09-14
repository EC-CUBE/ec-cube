<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

//---- �ڡ���ɽ�����饹
class LC_Page {
	
	function LC_Page() {
		$this->tpl_mainpage = TEMPLATE_DIR . '/campaign/index.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView(false);
$objQuery = new SC_Query();
$objCampaignSess = new SC_CampaignSession();

// �ǥ��쥯�ȥ�̾�����
$dir_name = dirname($_SERVER['PHP_SELF']);
$arrDir = split('/', $dir_name);
$dir_name = $arrDir[count($arrDir) -1];

/* ���å����˥����ڡ���ǡ�����񤭹��� */
// �����ڡ��󤫤�����ܤȤ���������ݻ�
$objCampaignSess->setIsCampaign();
// �����ڡ���ID���ݻ�
$campaign_id = $objQuery->get("dtb_campaign", "campaign_id", "directory_name = ? AND del_flg = 0", array($dir_name));
$objCampaignSess->setCampaignId($campaign_id);
// �����ڡ���ǥ��쥯�ȥ�̾���ݻ�
$objCampaignSess->setCampaignDir($dir_name);

// �����Ȥ�����ʤ��ڡ����ξ��Υڡ���(�����Τߥڡ���)�إ�����쥯��
$cart_flg = $objQuery->get("dtb_campaign", "cart_flg", "campaign_id = ?", array($campaign_id));
if(!$cart_flg) {
	header("location: ". CAMPAIGN_URL . "$dir_name/application.php");
}

// �����ڡ��󤬳����椫������å�
if(lfCheckActive($dir_name)) {
	$status = CAMPAIGN_TEMPLATE_ACTIVE;
} else {
	$status = CAMPAIGN_TEMPLATE_END;
}

if($_GET['init'] != "") {
	$objPage->tpl_init = 'false';
	lfDispProductsList($_GET['ids']);
} else {
	$objPage->tpl_init = 'true';	
}

switch($_POST['mode']) {

case 'cart':
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
		$objCartSess->addProduct(array($_POST['product_id'], $classcategory_id1, $classcategory_id2), $_POST[$quantity], $campaign_id);
		header("Location: " . URL_CART_TOP);
		exit;
	}
	break;
default :
	break;
}
// ���Ͼ�����Ϥ�
$objPage->arrForm = $_POST;
$objPage->tpl_dir_name = CAMPAIGN_TEMPLATE_PATH . $dir_name  . "/" . $status;

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);


//---------------------------------------------------------------------------------------------------------------------------------------------------------
/* 
 * �ؿ�̾��lfCheckActive()
 * ����1 ���ǥ��쥯�ȥ�̾
 * �������������ڡ����椫�����å�
 * ����͡������ڡ�����ʤ� true ��λ�ʤ� false
 */
function lfCheckActive($directory_name) {
	
	global $objQuery;
	$is_active = false;
	
	$col = "limit_count, total_count, start_date, end_date";
	$arrRet = $objQuery->select($col, "dtb_campaign", "directory_name = ? AND del_flg = 0", array($directory_name));

	// �����������������������
	$start_date = (date("YmdHis", strtotime($arrRet[0]['start_date'])));
	$end_date = (date("YmdHis", strtotime($arrRet[0]['end_date'])));
	$now_date = (date("YmdHis"));

	// �����ڡ��󤬳��Ŵ��֤ǡ����Ŀ���������Ǥ���
	if($now_date > $start_date && $now_date < $end_date
			&& ($arrRet[0]['limit_count'] > $arrRet[0]['total_count'] || $arrRet[0]['limit_count'] < 1)) {
		$is_active = true;
	}
		
	return $is_active;
}

/* ���ʰ�����ɽ�� */
function lfDispProductsList($ids) {
	
	global $objQuery;
	global $objPage;

	// ����̾����
	$arrClassName = sfGetIDValueList("dtb_class", "class_id", "name");
	// ����ʬ��̾����
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
	
	$arrProductIds = split('-', $ids);
	if(!is_array($arrProductIds)) {
		$arrProductIds[0] = $ids;
	}
	
	// where������
	$count = 0;
	$where = "product_id IN (";
	foreach($arrProductIds as $key =>$val) {
		if($count > 0) $where .= ",";
		$where .= "?";
		$arrval[] = $val;
		$count++;
	}
	$where .= ")";

	// ���ʰ���
	$arrProducts = $objQuery->select("*", "vw_products_allclass AS allcls", $where, $arrval);

	for($i = 0; $i < count($arrProducts); $i++) {
		$objPage = lfMakeSelect($arrProducts[$i]['product_id'], $arrClassName, $arrClassCatName);
		// �������¿������
		$objPage = lfGetSaleLimit($arrProducts);
	}

	foreach($arrProducts as $key =>$val) {
		$arrCamp[$val['product_id']] = $val;
	}
	
	$objPage->arrProducts = $arrCamp;
	
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