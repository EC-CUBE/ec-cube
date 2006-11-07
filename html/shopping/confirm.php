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
$objQuery = new SC_Query();

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

// カー都内の商品の売り切れチェック
$objCartSess->chkSoldOut($objCartSess->getCartList());

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

	// この時点でオーダーIDを確保しておく（クレジット、コンビニ決済で必要なため）
	if($arrData["order_id"] == ""){
		// postgresqlとmysqlとで処理を分ける
		if (DB_TYPE == "pgsql") {
			$order_id = $objQuery->nextval("dtb_order","order_id");
			$objQuery->query("INSERT INTO dtb_order (order_id, customer_id, create_date, del_flg) VALUES (?, ?, now(), 1)", array($order_id, $arrData["customer_id"]));
		}elseif (DB_TYPE == "mysql") {
			$objQuery->query("INSERT INTO dtb_order (customer_id, create_date, del_flg) VALUES (?, now(), 1)", array($arrData["customer_id"]));
			$order_id = $objQuery->nextval("dtb_order","order_id");
		}
		$arrData["order_id"] = $order_id;
	}

	// 集計結果を受注一時テーブルに反映
	sfRegistTempOrder($uniqid, $arrData);
	// 正常に登録されたことを記録しておく
	$objSiteSess->setRegistFlag();
	
	// 決済区分を取得する
	if(sfColumnExists("dtb_payment", "memo01")){
		$sql = "SELECT memo03 FROM dtb_payment WHERE payment_id = ?";
		$arrPayment = $objQuery->getall($sql, array($arrData['payment_id']));
	}
	
	// 決済方法により画面切替
	switch($arrPayment[0]["memo03"]) {
	case PAYMENT_CREDIT_ID:
	case PAYMENT_CONVENIENCE_ID:
		//header("Location: " . URL_SHOP_CREDIT);
		//header("Location: " . URL_SHOP_CONVENIENCE);
		$_SESSION["payment_id"] = $arrData['payment_id'];
		header("Location: " . URL_SHOP_MODULE);
		break;
/*
	case PAYMENT_LOAN_ID:
		header("Location: " . URL_SHOP_LOAN);
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