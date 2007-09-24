<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
$start_time = sfGetMicrotime_float();
 
require_once("./require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_css = URL_DIR.'css/layout/index.css';						// メインCSSパス
		/** 必ず変更する **/
		$this->tpl_mainpage = HTML_PATH . "user_data/templates/top.tpl";		// メインテンプレート
	}
}

$objPage = new LC_Page();
$conn = new SC_DBConn();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "index.php");

$objView = new SC_SiteView();
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
$end_time=sfGetMicrotime_float();

sfprintr( 'Script Execution Time: ' . round($end_time - $start_time, 5) . ' seconds'); 
//-----------------------------------------------------------------------------------------------------------------------------------
// Function to calculate script execution time.  
function sfGetMicrotime_float () { 
	list ($msec, $sec) = explode(' ', microtime()); 
	$microtime = (float)$msec + (float)$sec; 
	return $microtime; 
} 

?>