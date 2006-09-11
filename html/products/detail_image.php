<?php

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'products/detail_image.tpl';			// �ᥤ��ƥ�ץ졼��
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

// �����ڡ�������γ�ǧ�ξ��ϡ�������ξ��ʤ�ɽ�����롣
if($_GET['admin'] == 'on') {
	$where = "del_flg = 0";
} else {
	$where = "del_flg = 0 AND status = 1";
}

// �ͤ������������å�
if(!sfIsInt($_GET['product_id']) || !sfIsRecord("dtb_products", "product_id", $_GET['product_id'], $where)) {
	sfDispSiteError(PRODUCT_NOT_FOUND);
}


$image_key = $_GET['image'];

$objQuery = new SC_Query();
$col = "name, $image_key";
$arrRet = $objQuery->select($col, "dtb_products", "product_id = ?", array($_GET['product_id']));

if (sfIsInt($_GET['width']) && sfIsInt($_GET['height'])) {
	$objPage->tpl_width = $_GET['width'];
	$objPage->tpl_height = $_GET['height']; 	
} else {
	$objPage->tpl_width = LARGE_IMAGE_WIDTH;
	$objPage->tpl_height = LARGE_IMAGE_HEIGHT;
}

$objPage->tpl_table_width = $objPage->tpl_width + 20;
$objPage->tpl_table_height = $objPage->tpl_height + 20;

$objPage->tpl_image = $arrRet[0][$image_key];
$objPage->tpl_name = $arrRet[0]['name'];

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
?>