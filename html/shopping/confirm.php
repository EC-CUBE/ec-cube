<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	var $tpl_total_deliv_fee;
	function LC_Page() {
		$this->tpl_mainpage = 'shopping/confirm.tpl';
		$this->tpl_css = '/css/layout/shopping/confirm.css';
		$this->tpl_title = "ご入力内容のご確認";
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrSex;
		$this->arrSex = $arrSex;
		global $arrMAILMAGATYPE;
		$this->arrMAILMAGATYPE = $arrMAILMAGATYPE;
		global $arrReminder;
		$this->arrReminder = $arrReminder;
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');		

	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCartSess = new SC_CartSession();
$objSiteInfo = $objView->objSiteInfo;
$objSiteSess = new SC_SiteSession();
$objCustomer = new SC_Customer();
$arrInfo = $objSiteInfo->data;

// 前のページで正しく登録手続きが行われた記録があるか判定
sfIsPrePage($objSiteSess);

// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);
$objPage->tpl_uniqid = $uniqid;

// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
// 一時受注テーブルの読込
$arrData = sfGetOrderTemp($uniqid);
// カート集計を元に最終計算
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo, $objCustomer);

// 会員ログインチェック
if($objCustomer->isLoginSuccess()) {
	$objPage->tpl_login = '1';
	$objPage->tpl_user_point = $objCustomer->getValue('point');
}

switch($_POST['mode']) {
// 前のページに戻る
case 'return':
	// 正常な推移であることを記録しておく
	$objSiteSess->setRegistFlag();
	header("Location: " . URL_SHOP_PAYMENT);
	exit;
	break;
case 'confirm':
	// 集計結果を受注一時テーブルに反映
	sfRegistTempOrder($uniqid, $arrData);
	// 正常に登録されたことを記録しておく
	$objSiteSess->setRegistFlag();
	
	// 決済方法により画面切替
	switch($arrData['payment_id']) {
/* クレジット、ローン、コンビニ決済は次期開発
	case PAYMENT_CREDIT_ID:
		header("Location: " . URL_SHOP_CREDIT);
		break;
	case PAYMENT_LOAN_ID:
		header("Location: " . URL_SHOP_LOAN);
		break;
	case PAYMENT_CONVENIENCE_ID:
		header("Location: " . URL_SHOP_CONVENIENCE);
		break;
*/
	default:
		header("Location: " . URL_SHOP_COMPLETE);
		break;
	}
	break;
default:
	break;
}

$objPage->arrData = $arrData;
$objPage->arrInfo = $arrInfo;
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//--------------------------------------------------------------------------------------------------------------------------
?>