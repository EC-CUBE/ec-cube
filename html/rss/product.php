<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

//�������ʤ��ɤ߹���
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = "rss/product.tpl";
		$this->encode = "UTF-8";
		($_GET['mode'] == "all") ? $this->title = "���ʰ�������" : $this->title = "���ʾܺپ���";
	}
}

$objQuery = new SC_Query();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteInfo = new SC_SiteInfo();

//Ź�޾���򥻥å�
$arrSiteInfo = $objSiteInfo->data;

//����ID�����
$product_id = $_GET['product_id'];
$mode = $_GET['mode'];

if(($product_id != "" and is_numeric($product_id)) or $mode == "all"){
	//���ʾܺ٤����
	$arrProduct = lfGetProductsDetail($objQuery, $product_id);
	// �ͤΥ��åȤ�ľ��
	foreach($arrProduct as $key => $val){
		
		//���ʲ��ʤ��ǹ��ߤ��Խ�
		$arrProduct[$key]["price02"] = sfPreTax($arrProduct[$key]["price02"], $arrSiteInfo["tax"], $arrSiteInfo["tax_rule"]);
		
		// �����ե������URL���å�
		(file_exists(IMAGE_SAVE_DIR . $arrProduct[$key]["main_list_image"])) ? $dir = IMAGE_SAVE_URL_RSS : $dir = IMAGE_TEMP_URL_RSS;
		$arrProduct[$key]["main_list_image"] = $dir . $arrProduct[$key]["main_list_image"];
		(file_exists(IMAGE_SAVE_DIR . $arrProduct[$key]["main_image"])) ? $dir = IMAGE_SAVE_URL_RSS : $dir = IMAGE_TEMP_URL_RSS;
		$arrProduct[$key]["main_image"] = $dir . $arrProduct[$key]["main_image"];
		(file_exists(IMAGE_SAVE_DIR . $arrProduct[$key]["main_large_image"])) ? $dir = IMAGE_SAVE_URL_RSS : $dir = IMAGE_TEMP_URL_RSS;
		$arrProduct[$key]["main_large_image"] = $dir . $arrProduct[$key]["main_large_image"];
		
		// �ݥ���ȷ׻�
		$arrProduct[$key]["point"] = sfPrePoint($arrProduct[$key]["price02"], $arrProduct[$key]["point_rate"], POINT_RULE, $arrProduct[$key]["product_id"]);
	}
}elseif($mode == "list"){
	//���ʰ��������
	$arrProduct = $objQuery->getall("SELECT product_id, name AS product_name FROM dtb_products");
}

//���ʾ���򥻥å�
$objPage->arrProduct = $arrProduct;
if(is_array(sfswaparray($arrProduct))){
	$objPage->arrProductKeys = array_keys(sfswaparray($arrProduct));
}

//Ź�޾���򥻥å�
$objPage->arrSiteInfo = $arrSiteInfo;

//���åȤ����ǡ�����ƥ�ץ졼�ȥե�����˽���
$objView->assignobj($objPage);

//����å��夷�ʤ�(ǰ�Τ���)
header("Paragrama: no-cache");

//XML�ƥ�����(���줬�ʤ��������RSS�Ȥ���ǧ�����Ƥ���ʤ��ġ��뤬���뤿��)
header("Content-type: application/xml");
DETAIL_P_HTML;

//����ɽ��
$objView->display($objPage->tpl_mainpage, true);

//---------------------------------------------------------------------------------------------------------------------
/**************************************************************************************************************
 * �ؿ�̾:lfGetProducts
 * ������:���ʾ�����������
 * ������:$objQuery		DB���饹
 * ������:$product_id	����ID
 * �����:$arrProduct	������̤�������֤�
 **************************************************************************************************************/
function lfGetProductsDetail($objQuery, $product_id){
	$sql = "";
	$sql .= "SELECT ";
	$sql .= "	prod.product_id ";
	$sql .= "	,prod.name AS product_name ";
	$sql .= "	,prod.category_id ";
	$sql .= "	,prod.point_rate ";
	$sql .= "	,prod.comment3 ";
	$sql .= "	,prod.main_list_comment ";
	$sql .= "	,prod.main_list_image ";
	$sql .= "	,prod.main_comment ";
	$sql .= "	,prod.main_image ";
	$sql .= "	,prod.main_large_image ";
	$sql .= "	,cls.product_code ";
	$sql .= "	,cls.price01 ";
	$sql .= "	,cls.price02 ";
	$sql .= "	,cls.classcategory_id1 ";
	$sql .= "	,cls.classcategory_id2 ";
	$sql .= "	,(SELECT name FROM dtb_classcategory AS clscat WHERE clscat.classcategory_id = cls.classcategory_id1) AS classcategory_name1 ";
	$sql .= "	,(SELECT name FROM dtb_classcategory AS clscat WHERE clscat.classcategory_id = cls.classcategory_id2) AS classcategory_name2 ";
	$sql .= "	,(SELECT category_name FROM dtb_category AS cat WHERE cat.category_id = prod.category_id) AS category_name";
	$sql .= " FROM dtb_products AS prod, dtb_products_class AS cls";
	$sql .= " WHERE prod.product_id = cls.product_id AND prod.del_flg = 0 AND prod.status = 1";
	
	if($product_id != "ALL"){
		$sql .= " AND prod.product_id = ?";
		$arrval = array($product_id);
	}
	$sql .= " ORDER BY prod.product_id, cls.classcategory_id1, cls.classcategory_id2";
	$arrProduct = $objQuery->getall($sql, $arrval);
	return $arrProduct;
}

?>