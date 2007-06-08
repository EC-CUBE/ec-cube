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
		$this->tpl_css = '/css/layout/contact/index.css';	// メインCSSパス
		$this->tpl_mainpage = 'contact/index.tpl';
		$this->tpl_title = 'お問い合わせ(入力ページ)';
		$this->tpl_page_category = 'contact';
		global $arrPref;
		$this->arrPref = $arrPref;
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_MobileView();
$CONF = sf_getBasisData();			// 店舗基本情報

//----　ページ表示
$objView->assignobj($objPage);
$objView->assignarray($CONF);
$objView->display(SITE_FRAME);

//------------------------------------------------------------------------------------------------------------------------------------------
?>