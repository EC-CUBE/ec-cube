<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/product/index.css';	// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'products/top.tpl';			// メインテンプレート
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();






$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>