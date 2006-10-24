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
		$this->description = "�������";
	}
}

$objQuery = new SC_Query();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteInfo = new SC_SiteInfo();

//�����������
$arrProduct = lfGetProductsDetail($objQuery, 1);

//����å��夷�ʤ�(ǰ�Τ���)
header("Paragrama: no-cache");

//XML�ƥ�����(���줬�ʤ��������RSS�Ȥ���ǧ�����Ƥ���ʤ��ġ��뤬���뤿��)
header("Content-type: application/xml");

//���ʾ���򥻥å�
$objPage->arrProduct = $arrProduct;

//Ź̾�򥻥å�
$objPage->site_title = $arrProduct[0]['shop_name'];

//��ɽEmail���ɥ쥹�򥻥å�
$objPage->email = $arrProduct[0]['email'];

//���åȤ����ǡ�����ƥ�ץ졼�ȥե�����˽���
$objView->assignobj($objPage);

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
	$sql .= "SELECT 
				prod.product_id
				,prod.name
				,prod.category_id
				,prod.point_rate
				,prod.comment3
				,prod.main_list_comment
				,prod.main_list_image
				,prod.main_comment
				,prod.main_image
				,prod.main_large_image
				,cls.price01
				,cls.price02
				,(SELECT name FROM dtb_classcategory AS clscat WHERE clscat.classcategory_id = cls.classcategory_id1) AS classcategory_name1
				,(SELECT name FROM dtb_classcategory AS clscat WHERE clscat.classcategory_id = cls.classcategory_id2) AS classcategory_name2
				,(SELECT category_name FROM dtb_category AS cat WHERE cat.category_id = prod.category_id) AS category_name
			FROM dtb_products AS prod, dtb_products_class AS cls
			WHERE prod.product_id = cls.product_id AND prod.del_flg = 0 AND prod.status = 1 AND prod.product_id = ?
			ORDER BY prod.product_id, cls.classcategory_id1, cls.classcategory_id2
			";
	$arrProduct = $objQuery->getall($sql, array($product_id));
	return $arrProduct;
}

?>