<?php
$PHP_DIR = realpath(dirname( __FILE__));
require_once($PHP_DIR . "require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_css = '/css/layout/contact/index.css';	// メインCSSパス
		
	}
}

sfprintr($PHP_DIR);

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