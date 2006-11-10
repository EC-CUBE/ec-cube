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
	
	// トランザクションコード
	$arrMemo["trans_code"] = array("name"=>"Epsilonトランザクションコード", "value" => $_GET["trans_code"]);
	$arrVal["memo02"] = serialize($arrMemo);

	// 受注一時テーブルに更新
	sfRegistTempOrder($uniqid, $arrVal);

	// 完了画面へ
	header("Location: " .  URL_SHOP_COMPLETE);
}

// データ送信
lfSendCredit();

//---------------------------------------------------------------------------------------------------------------------------------------------------------

// データ送信処理
function lfSendCredit(){
	global $arrPayment;
	global $arrData;
	global $arrMainProduct;
	
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
		'memo1' => ECCUBE_PAYMENT . "_" . date("YmdHis"),					// 予備01
		'memo2' => ''														// 予備02
	);
	
	// データ送信
	$arrXML = sfPostPaymentData($order_url, $arrData);
	
	// エラーがあるかチェックする
	$err_code = sfGetXMLValue($arrXML,'RESULT','ERR_CODE');
	
	if($err_code != "") {
		$err_detail = sfGetXMLValue($arrXML,'RESULT','ERR_DETAIL');
		sfprintr($err_code . ":" . $err_detail);
		if($err_code == "909"){
			sfprintr($arrData);
			sfprintr($arrPayment[0]);
			$arrPayment[0]["memo04"] = "10000-0000-00000";
			lfSendCredit();
		}
		sfDispSiteError(FREE_ERROR_MSG, "", true, "購入処理中に以下のエラーが発生しました。<br /><br /><br />・" . $err_detail . "<br /><br /><br />この手続きは無効となりました。");
	} else {
		// 正常な推移であることを記録しておく
		$objSiteSess->setRegistFlag();
		
		$url = sfGetXMLValue($arrXML,'RESULT','REDIRECT');
		header("Location: " . $url);
	}
}

?>