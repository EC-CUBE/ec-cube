<?php

require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage ="mypage/reading.tpl";
		$this->tpl_css = '/css/layout/mypage/favorite.css';
		$this->tpl_title = 'MYページ閲覧履歴';
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

//ログイン判定
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR);
}

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

// 基本情報の取得
$objPage->arrInfo = $objSiteInfo->data;

//履歴の削除
if ($_POST['mode'] == 'delete'){
	$objQuery->delete("dtb_customer_reading","customer_id=? AND reading_product_id=?", array($objCustomer->getValue('customer_id'), $_POST['product_id']));
}

$objPage->tpl_pageno = $_POST['pageno'];

$select = "customer_id, reading_product_id, A.update_date, name, price02_min, price02_max ";
$from = "dtb_customer_reading AS A INNER JOIN vw_products_allclass AS B ON reading_product_id = B.product_id";
//公開商品を検索
$where = "A.customer_id = ? AND status = 1";
$arrval = array($objCustomer->getValue('customer_id'));
$order = "A.update_date DESC";
//表示順序
$objQuery->setorder($order);
	
$linemax = $objQuery->count($from, $where, $arrval);
$objPage->tpl_linemax = $linemax;
	
// ページ送りの取得
$objNavi = new SC_PageNavi($_POST['pageno'], $linemax, SEARCH_PMAX, "fnNaviPage", NAVI_PMAX);
$objPage->tpl_strnavi = $objNavi->strnavi;		// 表示文字列
$startno = $objNavi->start_row;
	
// 取得範囲の指定(開始行番号、行数のセット)
$objQuery->setlimitoffset(SEARCH_PMAX, $startno);

//履歴情報の取得
$objPage->arrForm = $objQuery->select($select, $from, $where, $arrval);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>

