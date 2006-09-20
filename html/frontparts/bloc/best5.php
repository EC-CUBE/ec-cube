<?php

class LC_Best5Page {
	function LC_Best5Page() {
		/** 必ず変更する **/
		$this->tpl_mainpage = ROOT_DIR . BLOC_DIR.'best5.tpl';	// メイン
	}
}

$objSubPage = new LC_Best5Page();
$objSubView = new SC_SiteView();
$objSiteInfo = $objView->objSiteInfo;

// 基本情報を渡す
$objSubPage->arrInfo =  new SC_SiteInfo();

//おすすめ商品表示
$objSubPage->arrBestProducts = lfGetRanking();

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
//おすすめ商品検索
function lfGetRanking(){
	$objQuery = new SC_Query();
	
	$col = "A.*, name, price02_min, price01_min, main_list_image ";
	$from = "dtb_best_products AS A INNER JOIN vw_products_allclass AS allcls using(product_id)";
	$where = "status = 1";
	$order = "rank";
	
	$arrBestProducts = $objQuery->select($col, $from, $where);
	return $arrBestProducts;
}

?>