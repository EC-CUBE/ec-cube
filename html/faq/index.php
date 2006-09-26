<?php
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/faq/index.css';	// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'faq/index.tpl';			// メインテンプレート
		$this->tpl_page_category = 'faq';				
		$this->tpl_title = 'よくある質問';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>
