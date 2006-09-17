<?php

class LC_Best5Page {
	function LC_Best5Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = ROOT_DIR . BLOC_DIR.'best5.tpl';	// �ᥤ��
	}
}

$objSubPage = new LC_Best5Page();
$objSubView = new SC_SiteView();
$objSiteInfo = $objView->objSiteInfo;

// ���ܾ�����Ϥ�
$objSubPage->arrInfo = $objSiteInfo->data;

//�������ᾦ��ɽ��
$objSubPage->arrBestProducts = lfGetRanking();

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
//�������ᾦ�ʸ���
function lfGetRanking(){
	$objQuery = new SC_Query();
	
	$col = "A.*, vw_products_allclass.name, vw_products_allclass.price02_min, vw_products_allclass.price01_min, vw_products_allclass.main_list_image ";
	$from = "dtb_best_products AS A INNER JOIN vw_products_allclass ON A.product_id = vw_products_allclass.product_id";
	$where = "status = 1";
	$order = "rank";
	$objQuery->setorder($order);
	$arrBestProducts = $objQuery->select($col, $from, $where);
	return $arrBestProducts;
}

?>