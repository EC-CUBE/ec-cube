<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = USER_PATH . 'templates/mypage/delivery.tpl';
		$this->tpl_title = "MYページ/お届け先追加･変更";
		$this->tpl_navi = USER_PATH . 'templates/mypage/navi.tpl';
		$this->tpl_mainno = 'mypage';
		$this->tpl_mypageno = 'delivery';
		global $arrPref;
		$this->arrPref= $arrPref;
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCustomer = new SC_Customer();
$objQuery = new SC_Query();
$objConn = new SC_DBConn();

//ログイン判定
if(!$objCustomer->isLoginSuccess()) {
	sfDispSiteError(CUSTOMER_ERROR);
}else {
	//マイページトップ顧客情報表示用
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}


// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

//削除
if($_POST['mode'] == 'delete') {
	//不正アクセス判定
	$flag = $objQuery->count("dtb_other_deliv", "customer_id=? AND other_deliv_id=?", array($objCustomer->getValue('customer_id'), $_POST['other_deliv_id']));
	if($flag > 0) {
		//削除
		$objQuery->delete("dtb_other_deliv", "other_deliv_id=?", array($_POST['other_deliv_id']));
	} else {
		sfDispSiteError(CUSTOMER_ERROR);
	}
}

$objPage->tpl_pageno = $_POST['pageno'];

$from = "dtb_other_deliv";
$where = "customer_id=?";
$arrval = array($objCustomer->getValue('customer_id'));
$order = "other_deliv_id DESC";

//お届け先登録件数取得
$linemax = $objQuery->count($from, $where, $arrval);

$objPage->tpl_linemax = $linemax;

// 表示順序
$objQuery->setorder($order);

//別のお届け先情報表示
$objPage->arrOtherDeliv = $objQuery->select("*", $from, $where, $arrval);

//お届け先登録数をテンプレートに渡す
$objPge->deliv_cnt = count($objPage->arrOtherDeliv);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

?>