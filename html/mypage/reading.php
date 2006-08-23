<?php

require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage ="mypage/reading.tpl";
		$this->tpl_css = '/css/layout/mypage/favorite.css';
		$this->tpl_title = 'MY�ڡ�����������';
		$this->tpl_mypageno = 'reading';
		session_cache_limiter('private-no-expire');
		$this->tpl_navi = 'mypage/navi.tpl';
	}
}

$objPage = new LC_Page();
$objQuery = new SC_Query();
$objView = new SC_SiteView();
$objCustomer = new SC_Customer();
$objSiteInfo = new SC_SiteInfo();

//������Ƚ��
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR);
}

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

// ���ܾ���μ���
$objPage->arrInfo = $objSiteInfo->data;

//����κ��
if ($_POST['mode'] == 'delete'){
	$objQuery->delete("dtb_customer_reading","customer_id=? AND reading_product_id=?", array($objCustomer->getValue('customer_id'), $_POST['product_id']));
}

$objPage->tpl_pageno = $_POST['pageno'];

$select = "customer_id, reading_product_id, A.update_date, name, price02_min, price02_max ";
$from = "dtb_customer_reading AS A INNER JOIN vw_products_allclass AS B ON reading_product_id = B.product_id";
//�������ʤ򸡺�
$where = "A.customer_id = ? AND status = 1";
$arrval = array($objCustomer->getValue('customer_id'));
$order = "A.update_date DESC";
//ɽ�����
$objQuery->setorder($order);
	
$linemax = $objQuery->count($from, $where, $arrval);
$objPage->tpl_linemax = $linemax;
	
// �ڡ�������μ���
$objNavi = new SC_PageNavi($_POST['pageno'], $linemax, SEARCH_PMAX, "fnNaviPage", NAVI_PMAX);
$objPage->tpl_strnavi = $objNavi->strnavi;		// ɽ��ʸ����
$startno = $objNavi->start_row;
	
// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
$objQuery->setlimitoffset(SEARCH_PMAX, $startno);

//�������μ���
$objPage->arrForm = $objQuery->select($select, $from, $where, $arrval);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>

