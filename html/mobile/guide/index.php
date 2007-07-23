<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * モバイルサイト/ご利用ガイド
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_mainpage = 'guide/index.tpl';	// メインテンプレート
		$this->tpl_title = 'ご利用ガイド';
	}
}

$objPage = new LC_Page();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView = new SC_MobileView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
