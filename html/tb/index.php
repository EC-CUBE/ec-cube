<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*
 * トラックバック受信
 * 
 * [1]なるべく多くのブログに対応できるように、GET/POST に関わらず受信する
 * [2]RSSの要求はGETで__modeパラメータがrssの場合のみ対応する(商品情報を返す)
 * [3]文字コードは指定がなければautoで対応する
 * [4]スパムは、オリジナル(好み)のアルゴリズムで対応できるようにしておく
 */

require_once("../require.php");

$objQuery = new SC_Query();
$objFormParam = new SC_FormParam();

// トラックバック機能の稼働状況チェック
if (sfGetSiteControlFlg(SITE_CONTROL_TRACKBACK) != 1) {
	// NG
	IfResponseNg();
	exit();
}

// パラメータ情報の初期化
lfInitParam();

// エンコード設定(サーバ環境によって変更)
$beforeEncode = "auto";
$afterEncode = mb_internal_encoding();

if (isset($_POST["charset"])) {
	$beforeEncode = $_POST["charset"];
} else if (isset($_GET["charset"])) {
	$beforeEncode = $_GET["charset"];
}

// POSTデータの取得とエンコード変換

// ブログ名
if (isset($_POST["blog_name"])) {
	$arrData["blog_name"] = trim(mb_convert_encoding($_POST["blog_name"], $afterEncode, $beforeEncode));
} else if (isset($_GET["blog_name"])) {
	$arrData["blog_name"] = trim(mb_convert_encoding($_GET["blog_name"], $afterEncode, $beforeEncode));
}

// ブログ記事URL
if (isset($_POST["url"])) {
	$arrData["url"] = trim(mb_convert_encoding($_POST["url"], $afterEncode, $beforeEncode));
} else if (isset($_GET["url"])) {
	$arrData["url"] = trim(mb_convert_encoding($_GET["url"], $afterEncode, $beforeEncode));
} else {
	/*
	 * RSS目的ではないGETリクエストを制御(livedoor blog)
	 * _rssパラメータでのGETリクエストを制御(Yahoo blog)
	 */
	if (isset($_GET["__mode"]) && isset($_GET["pid"])) {
		if ($_GET["__mode"] == "rss") {
			IfResponseRss($_GET["pid"]);
		}
	}
	exit();
}

// ブログ記事タイトル
if (isset($_POST["title"])) {
	$arrData["title"] = trim(mb_convert_encoding($_POST["title"], $afterEncode, $beforeEncode));
} else if (isset($_GET["title"])) {
	$arrData["title"] = trim(mb_convert_encoding($_GET["title"], $afterEncode, $beforeEncode));
}

// ブログ記事内容
if (isset($_POST["excerpt"])) {
	$arrData["excerpt"] = trim(mb_convert_encoding($_POST["excerpt"], $afterEncode, $beforeEncode));
} else if (isset($_GET["excerpt"])) {
	$arrData["excerpt"] = trim(mb_convert_encoding($_GET["excerpt"], $afterEncode, $beforeEncode));
}

$log_path = DATA_PATH . "logs/tb_result.log";
gfPrintLog("request data start -----", $log_path);
foreach($arrData as $key => $val) {
	gfPrintLog( "\t" . $key . " => " . $val, $log_path);
}
gfPrintLog("request data end   -----", $log_path);

$objFormParam->setParam($arrData);

// 入力文字の変換
$objFormParam->convParam();
$arrData = $objFormParam->getHashArray();

// エラーチェック(トラックバックが成り立たないので、URL以外も必須とする)
$objPage->arrErr = lfCheckError();

// エラーがない場合はデータを更新
if(count($objPage->arrErr) == 0) {
	
	// 商品コードの取得(GET)
	if (isset($_GET["pid"])) {
		$product_id = $_GET["pid"];

		// 商品データの存在確認
		$table = "dtb_products";
		$where = "product_id = ?";

		// 商品データが存在する場合はトラックバックデータの更新
		if (sfDataExists($table, $where, array($product_id))) {
			$arrData["product_id"] = $product_id;
			
			// データの更新
			if (lfEntryTrackBack($arrData) == 1) {
				IfResponseOk();
			}
		} else {
			gfPrintLog("--- PRODUCT NOT EXISTS : " . $product_id, $log_path);
		}
	}
}

// NG
IfResponseNg();
exit();

//----------------------------------------------------------------------------------------------------

/*
 * パラメータ情報の初期化
 * 
 * @param void なし
 * @return void なし
 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("URL", "url", URL_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("ブログタイトル", "blog_name", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("記事タイトル", "title", MTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("記事内容", "excerpt", MLTEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}

/*
 * 入力内容のチェック
 * 
 * @param void なし
 * @return $objErr->arrErr エラーメッセージ
 */
function lfCheckError() {
	global $objFormParam;
	
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	return $objErr->arrErr;
}

/*
 * 更新処理
 * 
 * @param $arrData トラックバックデータ
 * @return $ret 結果
 */
function lfEntryTrackBack($arrData) {
	global $objQuery;

	// ログ
	$log_path = DATA_PATH . "logs/tb_result.log";

	// スパムフィルター
	if (lfSpamFilter($arrData)) {
		$arrData["status"] = TRACKBACK_STATUS_NOT_VIEW;
	} else {
		$arrData["status"] = TRACKBACK_STATUS_SPAM;
	}

	$arrData["create_date"] = "now()";
	$arrData["update_date"] = "now()";

    if(!isset($arrData['url'])){
        $arrData['url'] = '';
    }elseif(!isset($arrData['excerpt'])){
        $arrData['excerpt'] = '';
    }
	// データの登録
	$table = "dtb_trackback";
	$ret = $objQuery->insert($table, $arrData);
	return $ret;
}

/*
 * スパムフィルター
 * 
 * @param $arrData トラックバックデータ
 * @param $run フィルターフラグ(true:使用する false:使用しない)
 * @return $ret 結果
 */
function lfSpamFilter($arrData, $run = false) {
	$ret = true;
	
	// フィルター処理
	if ($run) {
	}
	return $ret;
}

/*
 * OKレスポンスを返す
 * 
 * @param void なし
 * @return void なし
 */
function IfResponseOk() {
	header("Content-type: text/xml");
	print("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>");
	print("<response>");
	print("<error>0</error>");
	print("</response>");
	exit();
}

/*
 * NGレスポンスを返す
 * 
 * @param void なし
 * @return void なし
 */
function IfResponseNg() {
	header("Content-type: text/xml");
	print("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>");
	print("<response>");
	print("<error>1</error>");
	print("<message>The error message</message>");
	print("</response>");
	exit();
}

/*
 * トラックバックRSSを返す
 * 
 * @param $product_id 商品コード
 * @return void なし
 */
function IfResponseRss($product_id) {
	global $objQuery;
	
	$retProduct = $objQuery->select("*", "dtb_products", "product_id = ?", array($product_id));
	
	if (count($retProduct) > 0) {
		header("Content-type: text/xml");
		print("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>");
		print("<response>");
		print("<error>0</error>");
		print("<rss version=\"0.91\">");
		print("<channel>");
		print("<title>" . $retProduct[0]["name"] . "</title>");
		print("<link>");
		print(SITE_URL . "products/detail.php?product_id=" . $product_id);
		print("</link>");
		print("<description>");
		print($retProduct[0]["main_comment"]);
		print("</description>");
		print("<language>ja-jp</language>");
		print("</channel>");
		print("</rss>");
		print("</response>");
		exit();
	}
}

//-----------------------------------------------------------------------------------------------------------------------------------
?>
