<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once(MODULE_PATH . "mdl_gmo-pg/mdl_gmo-pg.inc");

/*

支払方法を表すコード

1： 一括払い
2： 分割払い
3： ボーナス一括払い
4： ボーナス分割払い
5： リボ払い

 */

$arrPayMethod = array(
	'1-0' => "一括払い",
	'2-3' => "分割3回払い",
	'2-6' => "分割6回払い",
	'2-10'=> "分割10回払い",
	'2-15'=> "分割15回払い",
	'2-20'=> "分割20回払い",
	'5-0' => "リボ払い"	
);

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/shopping/card.css';	// メインCSSパス
		if (GC_MobileUserAgent::isMobile()) {
			$this->tpl_mainpage = MODULE_PATH . "mdl_gmo-pg/gmo-pg_credit_mobile.tpl";
		} else {
			$this->tpl_mainpage = MODULE_PATH . "mdl_gmo-pg/gmo-pg_credit.tpl";
		}
		global $arrPayMethod;
		$this->arrPayMethod = $arrPayMethod;
		/*
		 session_start時のno-cacheヘッダーを抑制することで
		 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
		 private-no-expire:クライアントのキャッシュを許可する。
		*/
		session_cache_limiter('private-no-expire');		
	}
}

$objPage = new LC_Page();
$objView = (GC_MobileUserAgent::isMobile()) ? new SC_MobileView() : new SC_SiteView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

// アクセスの正当性の判定
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

switch($_POST['mode']) {
// 登録
case 'regist':
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);

	// 入力エラーなしの場合
	if(count($objPage->arrErr) == 0) {
		// エラーフラグ
		$err_flg = false;
		
		// カート集計処理
		$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
		// 一時受注テーブルの読込
		$arrData = sfGetOrderTemp($uniqid);
		// カート集計を元に最終計算
		$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);
		// カードの認証を行う
		$arrVal = $objFormParam->getHashArray();
		
				// 通信エラーの判定
		$access_err = false;
		// エラーメッセージ
		$credit_err = false;
		$gmo_err_msg = "";
		
		// アクセスIDがセットされていない場合
		if($_SESSION['GMO']['ACCESS_ID'] == "") {
			// 店舗情報の送信
			$arrEntryRet = lfSendGMOEntry($arrData['order_id'], $arrData['payment_total']);
			if($arrEntryRet == NULL) {
				$access_err = true;
			}
			
			// 店舗情報エラーの判定
			if($arrEntryRet['ERR_CODE'] == '0' && $arrEntryRet['ERR_INFO'] == 'OK') {
				$_SESSION['GMO']['ACCESS_ID'] = $arrEntryRet['ACCESS_ID'];
				$_SESSION['GMO']['ACCESS_PASS'] = $arrEntryRet['ACCESS_PASS'];
			} else {
				$_SESSION['GMO']['ACCESS_ID'] = "";
				$_SESSION['GMO']['ACCESS_PASS'] = "";				
				$credit_err = true;
				$detail_code01 = substr($arrEntryRet['ERR_INFO'], 0, 5);
				$detail_code02 = substr($arrEntryRet['ERR_INFO'], 5, 4);
				$gmo_err_msg = $detail_code01 . "-" . $detail_code02;
			}
		}
		
		// エラーなしの場合
		if(!$access_err && !$credit_err) {
			// 店舗情報送信結果
			$sqlval['memo04'] = $arrEntryRet['ERR_CODE'];
			$sqlval['memo05'] = $arrEntryRet['ERR_INFO'];
			
			// 店舗情報エラーの判定
			if($_SESSION['GMO']['ACCESS_ID'] != "" && $_SESSION['GMO']['ACCESS_PASS'] != "" ) {
				// 決済情報の送信
				$arrExecRet = lfSendGMOExec($_SESSION['GMO']['ACCESS_ID'], $_SESSION['GMO']['ACCESS_PASS'], $arrData['order_id'], $arrVal['card_no01'], $arrVal['card_no02'], $arrVal['card_no03'], $arrVal['card_no04'], $arrVal['card_month'], $arrVal['card_year'], $arrVal['paymethod']);
				if($arrExecRet == NULL) {
					$access_err = true;
				}
			}
		}
		
		// エラーなしの場合
		if(!$access_err && !$credit_err) {
			// 追加情報はないためダミーデータを格納
			$sqlval['memo02'] = serialize(array());
					
			// 応答内容の記録
			$sqlval['memo03'] = $arrVal['card_name01'] . " " . $arrVal['card_name02'];
	
			// 決済情報送信結果
			$sqlval['memo06'] = $arrExecRet['ErrType'];
			$sqlval['memo07'] = $arrExecRet['ErrInfo'];
			
			$objQuery = new SC_Query();
			$objQuery->update("dtb_order_temp", $sqlval, "order_temp_id = ?", array($uniqid));
		
			// 与信処理成功の場合
			if($arrExecRet['Html'] == "Receipt" && $arrExecRet['ErrType'] == "" && $arrExecRet['ErrInfo'] == "") {
				// 正常に登録されたことを記録しておく
				$objSiteSess->setRegistFlag();
				// アクセスIDをクリアする。
				$_SESSION['GMO']['ACCESS_ID'] = "";
				$_SESSION['GMO']['ACCESS_PASS'] = "";	
				// 処理完了ページへ
				if (GC_MobileUserAgent::isMobile()) {
					header("Location: " . gfAddSessionId(URL_SHOP_COMPLETE));
				} else {
					header("Location: " . URL_SHOP_COMPLETE);
				}
			} else {
				$credit_err = true;
				$detail_code01 = substr($arrExecRet['ErrInfo'], 0, 5);
				$detail_code02 = substr($arrExecRet['ErrInfo'], 5, 4);
				$gmo_err_msg = $detail_code01 . "-" . $detail_code02;
			}
		}
		
		if($access_err || $credit_err) {
			if($access_err) {
				$objPage->tpl_error = "※ クレジット承認に失敗しました：通信エラー";				
			} else {
				if($gmo_err_msg != "") {
					$objPage->tpl_error = "※ クレジット承認に失敗しました：".$gmo_err_msg;				
				} else {
					$objPage->tpl_error = "※ クレジット承認に失敗しました：不明なエラー";								
				}
			}
		}
	}
	break;
// 前のページに戻る
case 'return':
	// 正常な推移であることを記録しておく
	$objSiteSess->setRegistFlag();
	header("Location: " . URL_SHOP_CONFIRM);
	exit;
default:
	
	break;
}

$objDate = new SC_Date();
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(RELEASE_YEAR + CREDIT_ADD_YEAR);
$objPage->arrYear = $objDate->getZeroYear();
$objPage->arrMonth = $objDate->getZeroMonth();

$objPage->arrForm = $objFormParam->getFormParamList();

// 共通の表示準備
$objPage = sfGmoDisp($objPage, $payment_id);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("カード番号1", "card_no01", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード番号2", "card_no02", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード番号3", "card_no03", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード番号4", "card_no04", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード期限年", "card_year", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード期限月", "card_month", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("姓", "card_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
	$objFormParam->addParam("名", "card_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
	$objFormParam->addParam("支払方法", "paymethod", STEXT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	return $objErr->arrErr;
}

// 店舗情報の送信
function lfSendGMOEntry($order_id, $amount, $tax = 0) {
	
	$arrRet = sfGetPaymentDB();
		
	$arrData = array(
		'OrderId' => $order_id,		// 店舗ごとに一意な注文IDを送信する。
		'TdTenantName' => '',		// 3D認証時表示用店舗名
		'TdFlag' => '',				// 3Dフラグ
		'ShopId' => $arrRet[0]['gmo_shopid'],		// ショップID
		'ShopPass' => $arrRet[0]['gmo_shoppass'],	// ショップパスワード
		'Currency' => 'JPN',		// 通貨コード
		'Amount' => $amount,		// 金額
		'Tax' => $tax,				// 消費税
		'JobCd' => 'AUTH',			// 処理区分
		'TenantNo' => $arrRet[0]['gmo_tenantno'],	// 店舗IDを送信する。
	);
	
	$req = new HTTP_Request(GMO_ENTRY_URL);
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	$req->addPostDataArray($arrData);
	
	if (!PEAR::isError($req->sendRequest())) {
		$response = $req->getResponseBody();
	}
	$req->clearPostData();
	$arrRet = lfGetPostArray($response);
	
	return $arrRet;
}

function lfSendGMOExec($access_id, $access_pass, $order_id, $cardno1, $cardno2, $cardno3, $cardno4, $ex_mm, $ex_yy, $paymethod) {
	
	// 支払方法、回数の取得
	list($method, $paytimes) = split("-", $paymethod);
	
	if(!($paytimes > 0)) {
		$paytimes = "";
	}
			
	$arrData = array(
	'AccessId' => $access_id,
	'AccessPass' => $access_pass,
	'OrderId' => $order_id,
	'RetURL' => GMO_RETURL,
	// プロパーカードを扱わない場合はVISA固定でOK
	'CardType' => 'VISA, 11111, 111111111111111111111111111111111111, 1111111111',
	// 支払い方法
	/*
		1:一括
		2:分割
		3:ボーナス一括
		4:ボーナス分割
		5:リボ払い
	 */
	'Method' => $method,
	// 支払回数
	'PayTimes' => $paytimes,
	// カード番号
	/*
		試験用カード番号は、4111-1111-1111-1111
	 */
	'CardNo1' => $cardno1,
	'CardNo2' => $cardno2,
	'CardNo3' => $cardno3,
	'CardNo4' => $cardno4,
    'ExpireMM' => $ex_mm,
    'ExpireYY' => $ex_yy,
	// 加盟店自由項目返却フラグ
    'ClientFieldFlag' => '1',
    'ClientField1' => 'f1',
    'ClientField2' => 'f2',
    'ClientField3' => 'f3',
	// リダイレクトページでの応答を受け取らない
	/*
		0: HTML リダイレクトページ（Default 値）
		1: テキスト
	 */
	'ModiFlag' => '1',	
	);
	
	$req = new HTTP_Request(GMO_EXEC_URL);
	$req->setMethod(HTTP_REQUEST_METHOD_POST);
	
	$req->addPostDataArray($arrData);
	
	if (!PEAR::isError($req->sendRequest())) {
		$response = $req->getResponseBody();
	}
	$req->clearPostData();
	
	$arrRet = lfGetPostArray($response);
	
	return $arrRet;
}

function lfGetPostArray($text) {
	$arrRet = array();
	if($text != "") {
		$text = ereg_replace("[\n\r]", "", $text);
		$arrTemp = split("&", $text);
		foreach($arrTemp as $ret) {
			list($key, $val) = split("=", $ret);
			$arrRet[$key] = $val;
		}
	}
	return $arrRet;
}
?>