<?php

require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = ROOT_DIR . USER_DIR . 'templates/mypage/refusal.tpl';
		$this->tpl_title = "MYページ/退会手続き(入力ページ)";
		$this->tpl_navi = ROOT_DIR . USER_DIR . 'templates/mypage/navi.tpl';
		$this->tpl_mainno = 'mypage';
		$this->tpl_mypageno = 'refusal';
		//session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCustomer = new SC_Customer();
$objQuery = new SC_Query();

//ログイン判定
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR);
}else {
	//マイページトップ顧客情報表示用
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}


// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

switch ($_POST['mode']){
	case 'confirm':
	$objPage->tpl_mainpage = ROOT_DIR . USER_DIR . 'templates/mypage/refusal_confirm.tpl';
	$objPage->tpl_title = "MYページ/退会手続き(確認ページ)";

	break;
	
	case 'complete':
	//会員削除
	$objQuery->exec("UPDATE dtb_customer SET delete=1 WHERE customer_id=?", array($objCustomer->getValue('customer_id')));
	$objQuery->delete("dtb_customer_mail", "email ILIKE ?", array($objCustomer->getValue('email')));
	$objCustomer->EndSession();
	//完了ページへ
	header("Location: ./refusal_complete.php");
	exit;
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>