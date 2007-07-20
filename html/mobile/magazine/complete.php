<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'magazine/complete.tpl';		// メインテンプレート
		$this->tpl_title .= 'メルマガ登録(完了ページ)';			//　ページタイトル
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//----------------------------------------------------------------------------------------------------------------------
?>
