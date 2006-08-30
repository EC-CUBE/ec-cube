<?php
//データベースから商品検索を実行する。（ECキット動作試験用の開発）
require_once("../require.php");

class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = ROOT_DIR . USER_DIR . 'templates/mypage/index.tpl';
		$this->tpl_title = 'MYページ/購入履歴一覧';
		$this->tpl_navi = ROOT_DIR . 'data/Smarty/templates/mypage/navi.tpl';
		$this->tpl_mainno = 'mypage';
		$this->tpl_mypageno = 'index';
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();			
$objQuery = new SC_Query();             
$objCustomer = new SC_Customer();

// レイアウトデザインを取得
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

// ログインチェック
if(!isset($_SESSION['customer'])) {
	sfDispSiteError(CUSTOMER_ERROR);
}else {
	//マイページトップ顧客情報表示用
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}

//ページ送り用
$objPage->tpl_pageno = $_POST['pageno'];
	
$col = "order_id, create_date, payment_id, payment_total";
$from = "dtb_order";
$where = "delete = 0 AND customer_id=?";
$arrval = array($objCustomer->getvalue('customer_id'));
$order = "order_id DESC";

$linemax = $objQuery->count($from, $where, $arrval);
$objPage->tpl_linemax = $linemax;

// ページ送りの取得
$objNavi = new SC_PageNavi($_POST['pageno'], $linemax, SEARCH_PMAX, "fnNaviPage", NAVI_PMAX);
$objPage->tpl_strnavi = $objNavi->strnavi;		// 表示文字列
$startno = $objNavi->start_row;

// 取得範囲の指定(開始行番号、行数のセット)
$objQuery->setlimitoffset(SEARCH_PMAX, $startno);
// 表示順序
$objQuery->setorder($order);

//購入履歴の取得
$objPage->arrOrder = $objQuery->select($col, $from, $where, $arrval);

// 支払い方法の取得
$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

$objView->assignobj($objPage);				//$objpage内の全てのテンプレート変数をsmartyに格納
$objView->display(SITE_FRAME);				//パスとテンプレート変数の呼び出し、実行


//-------------------------------------------------------------------------------------------------------------------------
											
//エラーチェック

function lfErrorCheck() {
	$objErr = new SC_CheckError();
			$objErr->doFunc(array("メールアドレス", "login_email", STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","EMAIL_CHECK","MAX_LENGTH_CHECK"));
			$objErr->dofunc(array("パスワード", "login_password", PASSWORD_LEN2), array("EXIST_CHECK","ALNUM_CHECK"));
	return $objErr->arrErr;
}
				
?>