<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
class LC_CartPage {
	function LC_CartPage() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = BLOC_PATH . 'cart.tpl';	// �ᥤ��
	}
}

$objSubPage = new LC_CartPage();
$objSubView = new SC_SiteView();
$objCart = new SC_CartSession();
$objSiteInfo = new SC_SiteInfo;

if (count($_SESSION[$objCart->key]) > 0){
	// �����Ⱦ�������
	$arrCartList = $objCart->getCartList();
	
	// ��������ξ��ʣɣİ��������
	$arrAllProductID = $objCart->getAllProductID();
	// ���ʤ�1�İʾ����äƤ�����ˤϾ���̾�Τ����
	if (count($arrAllProductID) > 0){
		$objQuery = new SC_Query();
		$arrVal = array();
		$sql = "";
		$sql = "SELECT name FROM dtb_products WHERE product_id IN ( ?";
		$arrVal = array($arrAllProductID[0]);
		for($i = 1 ; $i < count($arrAllProductID) ; $i++){
			$sql.= " ,? ";
			array_push($arrVal, $arrAllProductID[$i]);
		}
		$sql.= " )";
		
		$arrProduct_name = $objQuery->getAll($sql, $arrVal);
		
		foreach($arrProduct_name as $key => $val){
			$arrCartList[$key]['product_name'] = $val['name'];
		}
	}
	// Ź�޾���μ���
	$arrInfo = $objSiteInfo->data;
	// ������۹��
	$ProductsTotal = $objCart->getAllProductsTotal($arrInfo);
	
	// ��׸Ŀ�
	$TotalQuantity = $objCart->getTotalQuantity();
	
	// ����̵���ޤǤζ��
	$arrCartList[0]['ProductsTotal'] = $ProductsTotal;
	$arrCartList[0]['TotalQuantity'] = $TotalQuantity;
	$deliv_free = $arrInfo['free_rule'] - $ProductsTotal;
	$arrCartList[0]['free_rule'] = $arrInfo['free_rule'];
	$arrCartList[0]['deliv_free'] = $deliv_free;
	
	$objSubPage->arrCartList = $arrCartList;
}

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------

?>