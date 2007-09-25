<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = USER_PATH . 'templates/mypage/refusal.tpl';
		$this->tpl_title = "MYページ/退会手続き(入力ページ)";
		$this->tpl_navi = USER_PATH . 'templates/mypage/navi.tpl';
		$this->tpl_mainno = 'mypage';
		$this->tpl_mypageno = 'refusal';
		//session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCustomer = new SC_Customer();
$objQuery = new SC_Query();
$objSiteSess = new SC_SiteSession();

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
    
	$objPage->tpl_mainpage = USER_PATH . 'templates/mypage/refusal_confirm.tpl';
	$objPage->tpl_title = "MYページ/退会手続き(確認ページ)";
    
    // 確認ページを経由したことを登録
    $objSiteSess->setRegistFlag();
    // hiddenにuniqidを埋め込む
    $objPage->tpl_uniqid = $objSiteSess->getUniqId();
    
	break;
	
	case 'complete':
    // 正しい遷移かどうかをチェック
    lfIsValidMovement($objSiteSess);
    
	//会員削除
	$objQuery->exec("UPDATE dtb_customer SET del_flg=1, update_date=now() WHERE customer_id=?", array($objCustomer->getValue('customer_id')));

	$objCustomer->EndSession();
	//完了ページへ
	header("Location: " . sfGetCurrentUri() . "/refusal_complete.php");
	exit;
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

// 正しい遷移かどうかをチェック
function lfIsValidMovement($objSiteSess) {
    // 確認ページからの遷移かどうかをチェック
    sfIsPrePage($objSiteSess);
    
    // uniqid がPOSTされているかをチェック
    $uniqid = $objSiteSess->getUniqId();
    if ( !empty($_POST['uniqid']) && ($_POST['uniqid'] === $uniqid) ) {
        return;
    } else {
        sfDispSiteError(PAGE_ERROR, $objSiteSess);
    }
}
?>