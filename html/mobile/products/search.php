<?php
/**
 * モバイルサイト/商品検索フォーム
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_mainpage = 'products/search.tpl';			// メインテンプレート
		$this->tpl_title = '商品検索';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
