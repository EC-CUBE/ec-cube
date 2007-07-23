<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * 変更完了
 */
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = 'mypage/change_complete.tpl';
		$this->tpl_title = 'MYページ/会員登録内容変更(完了ページ)';
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();
$objCustomer = new SC_Customer();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

//セッション情報を最新の状態に更新する
$objCustomer->updateSession();

//ログイン判定
if (!$objCustomer->isLoginSuccess(true)){
	sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
}else {
	//マイページトップ顧客情報表示用
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}


$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>
