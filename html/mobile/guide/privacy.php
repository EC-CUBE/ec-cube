<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * モバイルサイト/プライバシーポリシー
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_mainpage = 'guide/privacy.tpl';	// メインテンプレート
		$this->tpl_title = 'プライバシーポリシー';
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
