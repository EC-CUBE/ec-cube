<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = '/css/layout/entry/complete.css';	// メインCSSパス
		$this->tpl_mainpage = 'entry/complete.tpl';			// メインテンプレート
		$this->tpl_title .= '会員登録(完了ページ)';			//　ページタイトル
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();


// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//----------------------------------------------------------------------------------------------------------------------
?>