<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 * 退会処理
 */
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = 'mypage/refusal.tpl';
		$this->tpl_title = "MYページ/退会手続き(入力ページ)";
		//session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_MobileView();
$objCustomer = new SC_Customer();
$objQuery = new SC_Query();

//ログイン判定
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
}else {
	//マイページトップ顧客情報表示用
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}


// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

if (isset($_POST['no'])) {
	header("Location: " . gfAddSessionId("index.php"));
	exit;
} elseif (isset($_POST['complete'])){
	//会員削除
	$objQuery->exec("UPDATE dtb_customer SET del_flg=1, update_date=now() WHERE customer_id=?", array($objCustomer->getValue('customer_id')));

	$where = "email ILIKE ?";
	if (DB_TYPE == "mysql")	$where = sfChangeILIKE($where);
	$objCustomer->EndSession();
	//完了ページへ
	header("Location: " . gfAddSessionId("refusal_complete.php"));
	exit;
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>
