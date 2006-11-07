<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");
require_once(MODULE_PATH . "mdl_epsilon/mdl_epsilon.inc");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_mainpage = 'mdl_epsilon/card.tpl';			// メインテンプレート
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
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;


// trans_codeに値があり且つ、正常終了のときはオーダー確認を行う。
if($_SESSION['site']['pre_regist_success']){
	if($_GET["trans_code"] != ""){
		
		sfprintr($_GET);
		sfprintr($_SESSION);
		
		// 正常な推移であることを記録しておく
		$objSiteSess->setRegistFlag();
		
		// 完了画面へ
		header("Location: " . URL_SHOP_COMPLETE);
		
	}else{
		$_SESSION['site']['now_page'] = "";
		$objSiteSess->unsetUniqId();
		sfDispSiteError(FREE_ERROR_MSG, "", true, "購入処理中にエラーが発生しました。<br>この手続きは無効となりました。");
	}
}




// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);

// 一時受注テーブルの読込
$arrData = sfGetOrderTemp($uniqid);

// カート集計を元に最終計算
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

// 代表商品情報
$arrMainProduct = $objPage->arrProductsClass[0];

// 支払い情報を取得
$arrPayment = $objQuery->getall("SELECT memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ? ", array($arrData["payment_id"]));

// データ送信先CGI
$order_url = $arrPayment[0]["memo02"];

// 送信データ生成
$arrData = array(
	'contract_code' => $arrPayment[0]["memo01"],						// 契約コード
	'user_id' => $arrData["customer_id"],								// ユーザID
	'user_name' => $arrData["order_name01"].$arrData["order_name02"],	// ユーザ名
	'user_mail_add' => $arrData["order_email"],							// メールアドレス
	'order_number' => $arrData["order_id"],								// オーダー番号
	'item_code' => $arrMainProduct["product_code"],						// 商品コード(代表)
	'item_name' => $arrMainProduct["name"],								// 商品名(代表)
	'item_price' => $arrData["payment_total"],							// 商品価格(税込み総額)
	'st_code' => $arrPayment[0]["memo04"],								// 決済区分
	'mission_code' => '1',												// 課金区分(固定)
	'process_code' => '1',												// 処理区分(固定)
	'xml' => '1',														// 応答形式(固定)
	'memo1' => ECCUBE_PAYMENT,											// 予備01
	'memo2' => ''														// 予備02
);

// データ送信
sfPostPaymentData($order_url, $arrData);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

?>