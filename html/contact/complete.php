<?php

require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css[1] = '/css/layout/contact/index.css';	// メインCSSパス
		$this->tpl_mainpage = 'contact/complete.tpl';
		$this->tpl_title .= 'お問い合わせ(完了ページ)';
		$this->tpl_mainno = 'contact';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>