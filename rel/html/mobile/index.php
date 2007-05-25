<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * モバイルサイト/トップページ
 */

require_once('./require.php');

class LC_Page {
	function LC_Page() {
		/** 必ず変更する **/
		$this->tpl_mainpage = 'top.tpl';	// メインテンプレート
	}
}

$objPage = new LC_Page();
$conn = new SC_DBConn();
$objCustomer = new SC_Customer();

// レイアウトデザインを取得
//$objPage = sfGetPageLayout($objPage, false, 'index.php');

$objView = new SC_MobileView();
$objView->assign("isLogin", $objCustomer->isLoginSuccess());
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>
