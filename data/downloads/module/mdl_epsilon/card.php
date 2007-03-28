<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
$objCampaignSess = new SC_CampaignSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);

// 一時受注テーブルの読込
$arrData = sfGetOrderTemp($uniqid);

// カート集計を元に最終計算
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

// 代表商品情報
$arrMainProduct = $objPage->arrProductsClass[0];

// 支払い情報を取得
$arrPayment = $objQuery->getall("SELECT module_id, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ? ", array($arrData["payment_id"]));

// trans_codeに値があり且つ、正常終了のときはオーダー確認を行う。
if($_GET["result"] == "1"){
	
	// 正常な推移であることを記録しておく
	$objSiteSess->setRegistFlag();
	
	// GETデータを保存
	$arrVal["credit_result"] = $_GET["result"];
	$arrVal["memo01"] = PAYMENT_CREDIT_ID;
	$arrVal["memo03"] = $arrPayment[0]["module_id"];
	$sqlval["memo04"] = sfGetXMLValue($arrXML,'RESULT','TRANS_CODE');

	// トランザクションコード
	$arrMemo["trans_code"] = array("name"=>"Epsilonトランザクションコード", "value" => $_GET["trans_code"]);
	$arrVal["memo02"] = serialize($arrMemo);

	// 決済送信データ作成
	$arrModule['module_id'] = MDL_EPSILON_ID;
	$arrModule['payment_total'] = $arrPayment[0]["payment_total"];
	$arrModule['payment_id'] = PAYMENT_CREDIT_ID;
	$arrVal["memo05"] = serialize($arrModule);

	// 受注一時テーブルに更新
	sfRegistTempOrder($uniqid, $arrVal);

	// 完了画面へ
	if (is_callable(GC_MobileUserAgent) && GC_MobileUserAgent::isMobile()) {
		header("Location: " .  gfAddSessionId(URL_SHOP_COMPLETE));
	} else {
		header("Location: " .  URL_SHOP_COMPLETE);
	}
}

// データ送信
lfSendCredit($arrData, $arrPayment, $arrMainProduct);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

// データ送信処理
function lfSendCredit($arrData, $arrPayment, $arrMainProduct, $again = true){
	global $objSiteSess;
	global $objCampaignSess;
	
	// データ送信先CGI
	$order_url = $arrPayment[0]["memo02"];

	// 非会員のときは user_id に not_memberと送る
	($arrData["customer_id"] == 0) ? $user_id = "not_member" : $user_id = $arrData["customer_id"];	
	
	// 送信データ生成
	$item_name = $arrMainProduct["name"] . "×" . $arrMainProduct["quantity"] . "個 (代表)";
	$arrSendData = array(
		'contract_code' => $arrPayment[0]["memo01"],						// 契約コード
		'user_id' => $user_id ,												// ユーザID
		'user_name' => $arrData["order_name01"].$arrData["order_name02"],	// ユーザ名
		'user_mail_add' => $arrData["order_email"],							// メールアドレス
		'order_number' => $arrData["order_id"],								// オーダー番号
		'item_code' => $arrMainProduct["product_code"],						// 商品コード(代表)
		'item_name' => $item_name,											// 商品名(代表)
		'item_price' => $arrData["payment_total"],							// 商品価格(税込み総額)
		'st_code' => $arrPayment[0]["memo04"],								// 決済区分
		'mission_code' => '1',												// 課金区分(固定)
		'process_code' => '1',												// 処理区分(固定)
		'xml' => '1',														// 応答形式(固定)
		'memo1' => "",														// 予備01
		'memo2' => ECCUBE_PAYMENT . "_" . date("YmdHis"),					// 予備02
	);

	// データ送信
	$arrXML = sfPostPaymentData($order_url, $arrSendData);
	
	// エラーがあるかチェックする
	$err_code = sfGetXMLValue($arrXML,'RESULT','ERR_CODE');
	
	if($err_code != "") {
		$err_detail = sfGetXMLValue($arrXML,'RESULT','ERR_DETAIL');
		
		// 決済区分エラーの場合には VISA,MASTER のみで再送信を試みる
		if($err_code == "909" and $again){
			$arrPayment[0]["memo04"] = "10000-0000-00000";
			lfSendCredit($arrData, $arrPayment, $arrMainProduct, false);
		}
		sfDispSiteError(FREE_ERROR_MSG, "", true, "購入処理中に以下のエラーが発生しました。<br /><br /><br />・" . $err_detail . "<br /><br /><br />この手続きは無効となりました。");
	} else {
		// 正常な推移であることを記録しておく
		$objSiteSess->setRegistFlag();
		
		// 携帯端末の場合は、セッションID・オーダー番号・戻ってくるURLを保存しておく。
		if (is_callable(GC_MobileUserAgent) && GC_MobileUserAgent::isMobile()) {
			sfMobileSetExtSessionId('order_number', $arrData['order_id'], 'shopping/load_payment_module.php');
			sfMobileSetExtSessionId('order_number', $arrData['order_id'], 'shopping/confirm.php');
		}

		$url = sfGetXMLValue($arrXML,'RESULT','REDIRECT');
		header("Location: " . $url);
	}
}

?>
