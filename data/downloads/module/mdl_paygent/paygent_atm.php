<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once(MODULE_PATH . "mdl_paygent/mdl_paygent.inc");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_mainpage = MODULE_PATH . 'mdl_paygent/paygent_atm.tpl';		// メインテンプレート
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

if (GC_MobileUserAgent::isMobile()) {
	sfDispSiteError(FREE_ERROR_MSG, "", false, "ATM決済は、ご使用の機種には対応しておりません。", true);
	exit;
}

// パラメータ管理クラス
$objFormParam = new SC_FormParam();

// 一時受注テーブルの読込
$arrData = sfGetOrderTemp($uniqid);

// パラメータ情報の初期化
lfInitParam($arrData);
// POST値の取得
$objFormParam->setParam($_POST);

// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);

// カート集計を元に最終計算
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

switch($_POST['mode']) {
// 前のページに戻る
case 'return':
	// 正常な推移であることを記録しておく
	$objSiteSess->setRegistFlag();
	header("Location: " . URL_SHOP_CONFIRM);
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
		$arrRet = sfSendPaygentATM($arrData, $arrInput, $uniqid);
		
		// 成功
		if($arrRet['result'] === "0") {
            // 正常に登録されたことを記録しておく
            $objSiteSess->setRegistFlag();
            header("Location: " . URL_SHOP_COMPLETE);
		} else {
			// 失敗
			$objPage->tpl_error = "認証に失敗しました。お手数ですが入力内容をご確認ください。";
		}
	}
	break;
}

// 共通の表示準備
$objPage = sfPaygentDisp($objPage, $payment_id);

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
// フレームを選択(キャンペーンページから遷移なら変更)
$objCampaignSess->pageView($objView);

//----------------------------------------------------------------------------------

/* パラメータ情報の初期化 */
function lfInitParam($arrData) {
	global $objFormParam;
	$objFormParam->addParam("利用者姓", "customer_family_name", PAYGENT_BANK_STEXT_LEN / 2, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"), $arrData['order_name01']);
	$objFormParam->addParam("利用者名", "customer_name", PAYGENT_BANK_STEXT_LEN / 2, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"), $arrData['order_name02']);
	$objFormParam->addParam("利用者姓カナ", "customer_family_name_kana", PAYGENT_BANK_STEXT_LEN, "CKVa", array("EXIST_CHECK", "KANA_CHECK", "MAX_LENGTH_CHECK"), $arrData['order_kana01']);
	$objFormParam->addParam("利用者名カナ", "customer_name_kana", PAYGENT_BANK_STEXT_LEN, "CKVa", array("EXIST_CHECK", "KANA_CHECK", "MAX_LENGTH_CHECK"), $arrData['order_kana02']);
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