<?php
/**
 * 新規登録
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_mainpage = 'entry/new.tpl';	// メインテンプレート
	}
}

$objPage = new LC_Page();

// レイアウトデザインを取得
$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
