<?php

if (class_exists('LC_CartPage')) {
	class LC_CartPage {
		function LC_CartPage() {
			/** 必ず変更する **/
			$this->tpl_mainpage = ROOT_DIR . BLOC_DIR.'cart.tpl';	// メイン
		}
	}
}

$objSubPage = new LC_CartPage();
$objSubView = new SC_SiteView();
$objCart = new SC_CartSession();
$objSiteInfo = new SC_SiteInfo();

if (count($_SESSION[$objCart->key]) > 0){
	// カート情報を取得
	$arrCartList = $objCart->getCartList();
	
	// カート内の商品ＩＤ一覧を取得
	$arrAllProductID = $objCart->getAllProductID();
	// 商品が1つ以上入っている場合には商品名称を取得
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
	// 店舗情報の取得
	$arrInfo = $objSiteInfo->data;
	// 購入金額合計
	$ProductsTotal = $objCart->getAllProductsTotal($arrInfo);
	
	// 合計個数
	$TotalQuantity = $objCart->getTotalQuantity();
	
	// 送料無料までの金額
	$deliv_free = $arrInfo['free_rule'] - $ProductsTotal;

	$arrCartList[0]['ProductsTotal'] = $ProductsTotal;
	$arrCartList[0]['TotalQuantity'] = $TotalQuantity;
	$arrCartList[0]['deliv_free'] = $deliv_free;
	
	
	$objSubPage->arrCartList = $arrCartList;
}

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------

?>