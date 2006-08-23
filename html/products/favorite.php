<?php

require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = "products/favorite.tpl";
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();

//ログイン判定
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR);
}else{
	switch($_POST['mode']){
		case 'favorite':
		$col= "'".$_SESSION['customer']['customer_id']."','".$_POST['product_id']."','1','now()','now()' ";
		$objQuery->exec("INSERT INTO dtb_customer_favorite VALUES (".$col.")");
		$objQuery->getLastQuery(true);
		break;
	}
}
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>