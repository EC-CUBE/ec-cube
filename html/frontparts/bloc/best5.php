<?php

class LC_Best5Page {
	function LC_Best5Page() {
		/** 必ず変更する **/
		$this->tpl_mainpage = ROOT_DIR . BLOC_DIR.'best5.tpl';	// メイン
	}
}

$objSubPage = new LC_Best5Page();
$objSubView = new SC_SiteView();
$objSiteInfo = new SC_SiteInfo();

// 基本情報を渡す
$objSubPage->arrInfo = $objSiteInfo->data;

//おすすめ商品表示
$objSubPage->arrBestProducts = lfGetRanking();

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
//おすすめ商品検索
function lfGetRanking(){
	$objQuery = new SC_Query();
	
	$col = "A.*, B.name, B.price02_min, B.price01_min, B.main_list_image ";
	$from = "dtb_best_products AS A INNER JOIN vw_products_allclass AS B ON A.product_id = B.product_id";
	$where = "status = 1";
	$order = "rank";
	$objQuery->setorder($order);
	$arrBestProducts = $objQuery->select($col, $from, $where);
	return $arrBestProducts;
}

?>