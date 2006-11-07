<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");

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

// アクセスの正当性の判定
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

$_SESSION['site']['pre_page'] = $_SERVER['PHP_SELF'];

sfprintr($_SESSION["site"]);

// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
// 一時受注テーブルの読込
$arrData = sfGetOrderTemp($uniqid);
// カート集計を元に最終計算
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

// 代表商品情報
$arrMainProduct = $objPage->arrProductsClass[0];

// 支払い情報を取得
$arrPayment = 

sfprintr($arrData);
sfprintr($objPage);

$order_url = "http://beta.epsilon.jp/cgi-bin/order/receive_order3.cgi";

$arrData = array(
	'contract_code' => '13094800',											// 契約コード
	'user_id' => $arrData["customer_id"],									// ユーザID
	'user_name' => $arrData["order_name01"].$arrData["order_name02"],		// ユーザ名
	'user_mail_add' => $arrData["order_email"],								// メールアドレス
	'order_number' => $arrData["order_id"],									// オーダー番号
	'item_code' => $arrMainProduct["product_code"],							// 商品コード(代表)
	'item_name' => $arrMainProduct["name"],									// 商品名(代表)
	'item_price' => $objPage->tpl_total_pretax + $objPage->tpl_total_tax,	// 商品価格(税込み総額)
	'st_code' => '10000-0000-00000',			// 決済区分
	'mission_code' => '1',						// 課金区分
	'process_code' => '1',						// 処理区分
	'xml' => '1',								// 応答形式
	'memo1' => ECCUBE_PAYMENT,					// 予備01
	'memo2' => ''								// 予備02
);

$req = new HTTP_Request($order_url);
$req->setMethod(HTTP_REQUEST_METHOD_POST);
		
$arrSendData = array();
$req->addPostDataArray($arrData);

if (!PEAR::isError($req->sendRequest())) {
	$response = $req->getResponseBody();
} else {
	$response = "";
}
$req->clearPostData();

$parser = xml_parser_create();
xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
xml_parse_into_struct($parser,$response,$arrVal,$idx);
xml_parser_free($parser);

$err_code = lfGetXMLValue($arrVal,'RESULT','ERR_CODE');

if($err_code != "") {
	$err_detail = lfGetXMLValue($arrVal,'RESULT','ERR_DETAIL');
	print($err_detail);
} else {
	$url = lfGetXMLValue($arrVal,'RESULT','REDIRECT');
	header("Location: " . $url);	
}


function lfGetXMLValue($arrVal, $tag, $att) {
	$ret = "";
	foreach($arrVal as $array) {
		if($tag == $array['tag']) {
			if(!is_array($array['attributes'])) {
				continue;
			}
			foreach($array['attributes'] as $key => $val) {
				if($key == $att) {
					$ret = $val;
					break;
				}
			}			
		}
	}
	$dec = urldecode($ret);
	$enc = mb_convert_encoding($dec, 'EUC-JP', 'auto');
	return $enc;
}

?>