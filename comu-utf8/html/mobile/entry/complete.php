<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = '/css/layout/entry/complete.css';	// メインCSSパス
		
		if(CUSTOMER_CONFIRM_MAIL == true) {
			// 仮会員登録完了
			$this->tpl_mainpage = 'entry/complete.tpl';			// メインテンプレート
		} else {
			// 本会員登録完了
			$this->tpl_mainpage = 'regist/complete.tpl';		// メインテンプレート			
		}
        
		$this->tpl_title .= '会員登録(完了ページ)';			//　ページタイトル
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();


// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//----------------------------------------------------------------------------------------------------------------------
?>