<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once(DATA_PATH . "module/Request.php");
require_once(MODULE_PATH . "mdl_remise/mdl_remise.inc");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_mainpage = MODULE_PATH . 'mdl_remise/card.tpl';			// メインテンプレート
		$this->tpl_title = "カード決済";
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

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);

// 一時受注テーブルの読込
$arrData = sfGetOrderTemp($uniqid);

// カート集計を元に最終計算
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

$sql = "SELECT module_id, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 ".
	"FROM dtb_payment WHERE payment_id = ? ";

// 支払い情報を取得
$arrPayment = $objQuery->getall($sql, array($arrData["payment_id"]));

// 画面遷移判定
switch($_POST["mode"]){
	//戻る
	case 'return':
		// 正常に登録されたことを記録しておく
		$objSiteSess->setRegistFlag();
		// 確認ページへ移動
		header("Location: " . URL_SHOP_CONFIRM);
		exit;
		break;
}

// ルミーズからの返信があった場合
if (isset($_POST["X-R_CODE"])) {
	
	$err_detail = "";
	
	// 通信時エラー
	if ($_POST["X-R_CODE"] != $arrRemiseErrorWord["OK"]) {
		$err_detail = $_POST["X-R_CODE"];
		sfDispSiteError(FREE_ERROR_MSG, "", false, "購入処理中に以下のエラーが発生しました。<br /><br /><br />・" . $err_detail);
		
	// 通信結果正常
	} else {
		
		$log_path = DATA_PATH . "logs/remise_card_finish.log";
		gfPrintLog("remise card finish start----------", $log_path);
		foreach($_POST as $key => $val){
			gfPrintLog( "\t" . $key . " => " . $val, $log_path);
		}
		gfPrintLog("remise card finish end  ----------", $log_path);
	
		// 金額の整合性チェック
		if ($arrData["payment_total"] != $_POST["X-TOTAL"] && $arrData["credit_result"] != $_POST["X-TRANID"]) {
			sfDispSiteError(FREE_ERROR_MSG, "", false, "購入処理中に以下のエラーが発生しました。<br /><br /><br />・請求金額と支払い金額が違います。");
		}
		
		// 正常な推移であることを記録しておく
		$objSiteSess->setRegistFlag();
		
		// POSTデータを保存
		$arrVal["credit_result"] = $_POST["X-TRANID"];
		$arrVal["memo01"] = PAYMENT_CREDIT_ID;
		$arrVal["memo03"] = $arrPayment[0]["module_id"];
		$arrVal["memo04"] = $_POST["X-TRANID"];
		
		// トランザクションコード
		$arrMemo["trans_code"] = array("name"=>"Remiseトランザクションコード", "value" => $_POST["X-TRANID"]);
		$arrVal["memo02"] = serialize($arrMemo);
		
		// 決済送信データ作成
		$arrModule['module_id'] = MDL_REMISE_ID;
		$arrModule['payment_total'] = $arrData["payment_total"];
		$arrModule['payment_id'] = PAYMENT_CREDIT_ID;
		$arrVal['memo05'] = serialize($arrModule);
		
		// 受注一時テーブルに更新
		sfRegistTempOrder($uniqid, $arrVal);

		// 完了画面へ
		header("Location: " .  URL_SHOP_COMPLETE);
	}
}

// EC-CUBE側の通知用URL
$retUrl = SITE_URL . 'shopping/load_payment_module.php?module_id=' . MDL_REMISE_ID;
$exitUrl = SITE_URL . 'shopping/load_payment_module.php';

$arrSendData = array(
	'SEND_URL' => $arrPayment[0]["memo04"],	// 接続先URL
	'S_TORIHIKI_NO' => $arrData["order_id"],	// オーダー番号
	'MAIL' => $arrData["order_email"],			// メールアドレス
	'AMOUNT' => $arrData["subtotal"],			// 金額
	'TAX' => $arrData["deliv_fee"],				// 送料 + 税
	'TOTAL' => $arrData["payment_total"],		// 合計金額
	'SHOPCO' => $arrPayment[0]["memo01"],		// 店舗コード
	'HOSTID' => $arrPayment[0]["memo02"],		// ホストID
	'JOB' => REMISE_PAYMENT_JOB_CODE,			// ジョブコード 
	'ITEM' => '0000120',						// 商品コード(ルミーズ固定)
	'RETURL' => $retUrl,						// 完了通知URL
	'NG_RETURL' => $retUrl,					// NG完了通知URL
	'EXITURL' => $exitUrl,						// 戻り先URL
	'REMARKS3' => MDL_REMISE_POST_VALUE
);

// 支払い方法表示処理
$objFormParam->setValue("credit_method", $arrPayment[0]["memo08"]);
$objFormParam->splitParamCheckBoxes("credit_method");
$arrUseCreMet = $objFormParam->getValue("credit_method");

foreach($arrUseCreMet as $key => $val) {
	$arrCreMet[$val] = $arrCredit[$val];
}

// 分割回数表示処理(管理画面での設定回数以内まで表示)
foreach($arrCreditDivide as $key => $val) {
	if ($arrPayment[0]["memo09"] >= $val) {
		$arrCreDiv[$val] = $val;
	}
}

$objPage->arrCreMet = $arrCreMet;
$objPage->arrCreDiv = $arrCreDiv;
$objPage->arrSendData = $arrSendData;

$objView->assignobj($objPage);

// 出力内容をSJISにする(ルミーズ対応)
mb_http_output(REMISE_SEND_ENCODE);
$objView->display(MODULE_PATH . "mdl_remise/card.tpl");

//---------------------------------------------------------------------------------------------------------------------------------------------------------

//パラメータの初期化
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("支払い方法", "credit_method");
}

?>