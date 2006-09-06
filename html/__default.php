<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_css = '/css/layout/contact/index.css';	// メインCSSパス
		
	}
}
$objPage = new LC_Page();
$objView = new SC_SiteView();

phpinfo();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage);

// 画面の表示
 $objView->assignobj($objPage);
 $objView->display(SITE_FRAME);
 
//-----------------------------------------------------------------------------------------------------------------------------------

?>