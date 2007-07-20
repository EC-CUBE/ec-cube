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
		$this->tpl_mainpage = MODULE_PATH . "mdl_remise/convenience.tpl";
		$this->tpl_title = "コンビニ決済";
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');		
	}
}

global $arrConvenience;
global $arrConveni_message;

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// パラメータ管理クラス
$objFormParam = new SC_FormParam();

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

// 代表商品情報
$arrMainProduct = $objPage->arrProductsClass[0];

// 支払い情報を取得
$arrPayment = $objQuery->getall("SELECT module_id, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ? ", array($arrData["payment_id"]));

// 確認画面に戻る
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

		$log_path = DATA_PATH . "logs/remise_cv_finish.log";
		gfPrintLog("remise conveni finish start----------", $log_path);
		foreach($_POST as $key => $val){
			gfPrintLog( "\t" . $key . " => " . $val, $log_path);
		}
		gfPrintLog("remise conveni finish end  ----------", $log_path);

		// 金額の整合性チェック
		if ($arrData["payment_total"] != $_POST["X-TOTAL"]) {
			sfDispSiteError(FREE_ERROR_MSG, "", false, "購入処理中に以下のエラーが発生しました。<br /><br /><br />・請求金額と支払い金額が違います。");
		}
		
		// 正常な推移であることを記録しておく
		$objSiteSess->setRegistFlag();
		
		// ルミーズからの値の取得
		$job_id = lfSetConvMSG("ジョブID(REMISE)", $_POST["X-JOB_ID"]);
		$payment_limit = lfSetConvMSG("支払い期限", $_POST["X-PAYDATE"]);
		$conveni_type = lfSetConvMSG("支払いコンビニ", $arrConvenience[$_POST["X-PAY_CSV"]]);
		$payment_total = lfSetConvMSG("合計金額", $_POST["X-TOTAL"]);
		$receipt_no = lfSetConvMSG("コンビニ払い出し番号", $_POST["X-PAY_NO1"]);

		// ファミリーマートのみURLがない
		if ($_POST["X-PAY_CSV"] != "D030") {
			$payment_url = lfSetConvMSG("コンビニ払い出しURL", $_POST["X-PAY_NO2"]);
		} else {
			$payment_url = lfSetConvMSG("注文番号", $_POST["X-PAY_NO2"]);
		}
		
		$arrRet['cv_type'] = $conveni_type;				// コンビニの種類
		$arrRet['cv_payment_url'] = $payment_url;		// 払込票URL(PC)
		$arrRet['cv_receipt_no'] = $receipt_no;			// 払込票番号
		$arrRet['cv_payment_limit'] = $payment_limit;	// 支払い期限
		$arrRet['title'] = lfSetConvMSG("コンビニ決済", true);
		
		// 決済送信データ作成
		$arrModule['module_id'] = MDL_REMISE_ID;
		$arrModule['payment_total'] = $arrData["payment_total"];
		$arrModule['payment_id'] = PAYMENT_CONVENIENCE_ID;
		
		// ステータスは未入金にする
		$sqlval['status'] = 2;
		
		// コンビニ決済情報を格納
		$sqlval['conveni_data'] = serialize($arrRet);
		$sqlval['memo01'] = PAYMENT_CONVENIENCE_ID;
		$sqlval['memo02'] = serialize($arrRet);
		$sqlval['memo03'] = $arrPayment[0]["module_id"];
		$sqlval['memo04'] = $_POST["X-JOB_ID"];
		$sqlval['memo05'] = serialize($arrModule);

		// 受注一時テーブルに更新
		sfRegistTempOrder($uniqid, $sqlval);

		header("Location: " . URL_SHOP_COMPLETE);
	}
}

// EC-CUBE側の通知用URL
$retUrl = SITE_URL . 'shopping/load_payment_module.php?module_id=' . MDL_REMISE_ID;
$exitUrl = SITE_URL . 'shopping/load_payment_module.php';
$tel = $arrData["order_tel01"].$arrData["order_tel02"].$arrData["order_tel03"];

// 住所整形
$pref = $arrPref[$arrData["order_pref"]];
$address1 = mb_convert_kana($arrData["order_addr01"], "ASKHV");
$address2 = mb_convert_kana($arrData["order_addr02"], "ASKHV");

// 商品名整形(最大7個のため、商品代金として全体で出力する)
$itemName = "商品代金";
$itemPlace = $arrData["payment_total"] - $arrData["deliv_fee"];

$arrSendData = array(
	'SEND_URL' => $arrPayment[0]["memo05"],		// 接続先URL
	'S_TORIHIKI_NO' => $arrData["order_id"],		// 請求番号(EC-CUBE)
	'MAIL' => $arrData["order_email"],				// メールアドレス
	'NAME1' => $arrData["order_name01"],			// ユーザー名1
	'NAME2' => $arrData["order_name02"],			// ユーザー名2
	'KANA1' => $arrData["order_kana01"],			// ユーザー名(カナ)1
	'KANA2' => $arrData["order_kana02"],			// ユーザー名(カナ)2
	'TEL' => $tel,									// 電話番号
	'YUBIN1' => $arrData["order_zip01"],			// 郵便番号1
	'YUBIN2' => $arrData["order_zip02"],			// 郵便番号2
	'ADD1' => $pref,								// 住所1
	'ADD2' => $address1,							// 住所2
	'ADD3' => $address2,							// 住所3
	'MSUM_01' => $arrData["subtotal"],				// 金額
	'TAX' => $arrData["deliv_fee"],					// 送料 + 税
	'TOTAL' => $arrData["payment_total"],			// 合計金額
	'SHOPCO' => $arrPayment[0]["memo01"],			// 店舗コード
	'HOSTID' => $arrPayment[0]["memo02"],			// ホストID
	'RETURL' => $retUrl,							// 完了通知URL
	'NG_RETURL' => $retUrl,						// NG完了通知URL
	'EXITURL' => $exitUrl,							// 戻り先URL
	'MNAME_01' => $itemName,						// 商品名
	'MSUM_01' => $itemPlace,						// 商品代金合計(送料+税以外)
	'REMARKS3' => MDL_REMISE_POST_VALUE
);

$objPage->arrSendData = $arrSendData;
$objPage->arrForm =$objFormParam->getHashArray();
$objView->assignobj($objPage);

// 出力内容をSJISにする(ルミーズ対応)
mb_http_output(REMISE_SEND_ENCODE);
$objView->display(MODULE_PATH . "mdl_remise/convenience.tpl");

//---------------------------------------------------------------------------------------------------------------------------------------------------------

function lfSetConvMSG($name, $value){
	return array("name" => $name, "value" => $value);
}

?>