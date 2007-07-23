<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_mainpage = 'products/detail_image.tpl';			// メインテンプレート
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCartSess = new SC_CartSession("", false);

// 管理ページからの確認の場合は、非公開の商品も表示する。
if($_GET['admin'] == 'on') {
	$where = "del_flg = 0";
} else {
	$where = "del_flg = 0 AND status = 1";
}

// 値の正当性チェック
if(!sfIsInt($_GET['product_id']) || !sfIsRecord("dtb_products", "product_id", $_GET['product_id'], $where)) {
	sfDispSiteError(PRODUCT_NOT_FOUND);
}


$image_key = $_GET['image'];

$objQuery = new SC_Query();
$col = "name, $image_key";
$arrRet = $objQuery->select($col, "dtb_products", "product_id = ?", array($_GET['product_id']));

list($width, $height) = getimagesize(IMAGE_SAVE_DIR . $arrRet[0][$image_key]);
$objPage->tpl_width = $width;
$objPage->tpl_height = $height;

$objPage->tpl_table_width = $objPage->tpl_width + 20;
$objPage->tpl_table_height = $objPage->tpl_height + 20;

$objPage->tpl_image = $arrRet[0][$image_key];
$objPage->tpl_name = $arrRet[0]['name'];

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
?>