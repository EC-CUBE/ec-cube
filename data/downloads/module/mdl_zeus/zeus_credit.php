<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once(MODULE_PATH . "mdl_zeus/mdl_zeus.inc");

class LC_Page {
	function LC_Page() {
		if (GC_MobileUserAgent::isMobile()) {
			$this->tpl_mainpage = MODULE_PATH . "mdl_zeus/zeus_credit_mobile.tpl";
		} else {
			$this->tpl_mainpage = MODULE_PATH . "mdl_zeus/zeus_credit.tpl";
		}		
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
$objCampaignSess = new SC_CampaignSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;


// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);

// 一時受注テーブルの読込
$arrData = sfGetOrderTemp($uniqid);

// カート集計を元に最終計算
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

switch($_POST['mode']) {
// 前のページに戻る
case 'return':
	// 正常な推移であることを記録しておく
	$objSiteSess->setRegistFlag();
	if (GC_MobileUserAgent::isMobile()) {
		header("Location: " . gfAddSessionId(URL_SHOP_CONFIRM));
	} else {
		header("Location: " . URL_SHOP_CONFIRM);
	}
	exit;
	break;
// 次へ
case 'next':
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	
	// 入力エラーなしの場合
	if(count($objPage->arrErr) == 0) {
		// 入力データの取得を行う
    	$arrInput = $objFormParam->getHashArray();
    	// クレジット電文送信
		$ret = sfPostPaymentData($arrData, $arrInput);
		// 成功
		if($ret) {
            // 正常に登録されたことを記録しておく
            $objSiteSess->setRegistFlag();
			if (GC_MobileUserAgent::isMobile()) {
				header("Location: " . gfAddSessionId(URL_SHOP_COMPLETE));
			} else {
				header("Location: " . URL_SHOP_COMPLETE);
			}
		} else {
			// 失敗
			$objPage->tpl_error = "認証に失敗しました。お手数ですが入力内容をご確認ください。";
		}
	}
	break;
case 'quick_charge':
    // クレジット電文送信
	$ret = sfPostPaymentData($arrData, $arrInput, true);
	// 成功
	if($ret) {
    	// 正常に登録されたことを記録しておく
        $objSiteSess->setRegistFlag();
		if (GC_MobileUserAgent::isMobile()) {
			header("Location: " . gfAddSessionId(URL_SHOP_COMPLETE));
		} else {
			header("Location: " . URL_SHOP_COMPLETE);
		}
	} else {
		// 失敗
		$objPage->tpl_error = "認証に失敗しました。お手数ですが入力内容をご確認ください。";
	}
	break;
}

$objDate = new SC_Date();
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(RELEASE_YEAR + CREDIT_ADD_YEAR);
$objPage->arrYear = $objDate->getZeroYear();
$objPage->arrMonth = $objDate->getZeroMonth();

// 過去の注文を検索する。
$objPage->quick_charge_ok = sfEnableQuickCharge($arrData['customer_id']);

// 共通の表示準備
$objPage = sfZeusDisp($objPage, $payment_id);

// 支払回数
$objPage->arrPaymentClass = $arrPaymentClass;
$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
// フレームを選択(キャンペーンページから遷移なら変更)
$objCampaignSess->pageView($objView);
//-------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("支払回数", "payment_class", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("カード番号1", "card_no01", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード番号2", "card_no02", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード番号3", "card_no03", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード番号4", "card_no04", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード期限年", "card_year", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("カード期限月", "card_month", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("名", "card_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
	$objFormParam->addParam("姓", "card_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
	$objFormParam->addParam("前回利用したカードを使用する", "quick_check", INT_LEN, "n", array("MAX_LENGTH_CHECK"));	
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
?>