<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '';			// メインCSSパス
		/** 必ず指定する **/
		// メインテンプレート
		$this->tpl_mainpage = 'unsupported/index.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
