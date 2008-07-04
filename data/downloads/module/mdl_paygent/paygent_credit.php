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
		if (GC_MobileUserAgent::isMobile()) {
			$this->tpl_mainpage = MODULE_PATH . "mdl_paygent/paygent_credit_mobile.tpl";
		} else {
			$this->tpl_mainpage = MODULE_PATH . "mdl_paygent/paygent_credit.tpl";
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
if (GC_MobileUserAgent::isMobile()) {
	$objView = new SC_MobileView();
} else {
	$objView = new SC_SiteView();
}
$objCampaignSess = new SC_CampaignSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// クレジット用パラメータの取得
$arrPaymentDB = sfGetPaymentDB(MDL_PAYGENT_ID, "AND memo03 = 1");
$arrConfig = unserialize($arrPaymentDB[0]['other_param']);

// モード設定
$_POST['mode'] = lfGetMode();

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
		header("Location: " . gfAddSessionId(MOBILE_URL_SHOP_CONFIRM));
	} else {
		header("Location: " . URL_SHOP_CONFIRM);
	}
	break;
// 次へ
case 'next':
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError();
	// 入力エラーなしの場合
	if(count($objPage->arrErr) == 0) {
		// 入力データの取得を行う
    	$arrInput = $objFormParam->getHashArray();
		// クレジット電文送信
		$arrRet = sfSendPaygentCredit($arrData, $arrInput, $uniqid);
		
		// カード登録
        if ($_POST['stock_new'] == 1 && $_POST['stock'] != 1 && 
            ($arrRet['result'] === "0" || $arrRet['result'] === "7")) {
            sfSetPaygentCreditStock($arrData, $arrInput);
        }
		// 成功（3Dセキュア未対応）
		if ($arrRet['result'] === "0") {
            // 正常に登録されたことを記録
            $objSiteSess->setRegistFlag();
            LC_Helper_Send_Payment::sendPaymentData(MDL_PAYGENT_CODE, $arrData['payment_total']);
			if (GC_MobileUserAgent::isMobile()) {
				header("Location: ". gfAddSessionId(MOBILE_URL_SHOP_COMPLETE));
			} else {
				header("Location: ". URL_SHOP_COMPLETE);
			}
		// 成功（3Dセキュア対応）
		} elseif ($arrRet['result'] === "7") {
			// カード会社画面へ遷移（ACS支払人認証要求HTMLを表示）
			print mb_convert_encoding($arrRet['out_acs_html'], CHAR_CODE, "Shift-JIS");
			exit;
		// 失敗
		} else {
			$objPage->tpl_error = "決済に失敗しました。". $arrRet['response'];
		}
	}
	break;
// 3Dセキュア実施後
case '3d_secure':
	// クレジット電文送信（3Dセキュア実施後）
	$arrRet = sfSendPaygetnCredit3d($arrData, $_POST, $uniqid);
	// 成功
	if ($arrRet['result'] === "0") {
		// 正常に登録されたことを記録
		$objSiteSess->setRegistFlag();
		LC_Helper_Send_Payment::sendPaymentData(MDL_PAYGENT_CODE, $arrData['payment_total']);
		header("Location: ". URL_SHOP_COMPLETE);
	}
	break;
// 登録カード削除
case 'deletecard':
    // 入力値の変換
    $objFormParam->convParam();
    $objPage->arrErr = lfCheckError();
    // 入力エラーなしの場合
    if(count($objPage->arrErr) == 0) {
        // 入力データの取得
        $arrInput = $objFormParam->getHashArray();
        $arrRet = sfDelPaygentCreditStock($arrData, $arrInput);
        // 失敗
        if ($arrRet[0]['result'] !== "0") {
            $objPage->arrErr['CardSeq'] = "登録カード情報の削除に失敗しました。". $arrRet[0]['response'];
        }
    }
	break;
}

// 登録カード情報の取得
if ($arrConfig['stock_card'] == 1) {
    $objPage = lfGetStockCardData($arrData, $objPage);
}

$objDate = new SC_Date();
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(RELEASE_YEAR + CREDIT_ADD_YEAR);
$objPage->arrYear = $objDate->getZeroYear();
$objPage->arrMonth = $objDate->getZeroMonth();

// 共通の表示準備
$objPage = sfPaygentDisp($objPage, $payment_id);

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
	$_POST['mode'] = (isset($_POST['mode'])) ? $_POST['mode'] : "";
	$_POST['stock'] = (isset($_POST['stock'])) ? $_POST['stock'] : "";
	if ($_POST['mode'] == "deletecard" || $_POST['stock'] == 1) {
		$objFormParam->addParam("支払回数", "payment_class", INT_LEN, "n", array());
		$objFormParam->addParam("カード番号1", "card_no01", CREDIT_NO_LEN, "n", array());
		$objFormParam->addParam("カード番号2", "card_no02", CREDIT_NO_LEN, "n", array());
		$objFormParam->addParam("カード番号3", "card_no03", CREDIT_NO_LEN, "n", array());
		$objFormParam->addParam("カード番号4", "card_no04", CREDIT_NO_LEN, "n", array());
		$objFormParam->addParam("カード期限年", "card_year", 2, "n", array());
		$objFormParam->addParam("カード期限月", "card_month", 2, "n", array());
		$objFormParam->addParam("姓", "card_name01", 32, "KVa", array());
		$objFormParam->addParam("名", "card_name02", 32, "KVa", array());
	} else {
		$objFormParam->addParam("支払回数", "payment_class", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
		$objFormParam->addParam("カード番号1", "card_no01", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
		$objFormParam->addParam("カード番号2", "card_no02", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
		$objFormParam->addParam("カード番号3", "card_no03", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
		$objFormParam->addParam("カード番号4", "card_no04", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
		$objFormParam->addParam("カード期限年", "card_year", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
		$objFormParam->addParam("カード期限月", "card_month", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
		$objFormParam->addParam("姓", "card_name01", 32, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
		$objFormParam->addParam("名", "card_name02", 32, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
	}
	if ($_POST['mode'] == "deletecard") {
		$objFormParam->addParam("削除カード", "CardSeq", "", "n", array("EXIST_CHECK", "NUM_CHECK"));
	} elseif ($_POST['stock'] == 1) {
		$objFormParam->addParam("", "stock", "", "n", array());
		$objFormParam->addParam("登録カード", "CardSeq", "", "n", array("EXIST_CHECK", "NUM_CHECK"));
	}
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

/**
 * モード設定
 */
function lfGetMode() {
    $mode = '';
    // 3Dセキュアの戻り
    if (isset($_GET['mode']) && $_GET['mode'] == "credit_3d" && 
        isset($_GET['uniqid']) && $_GET['uniqid'] == $uniqid) {
        $mode = '3d_secure';
    // モバイル：登録カードの削除
    } elseif (isset($_POST['deletecard'])) {
        $mode = 'deletecard';
    // その他
    } elseif (isset($_POST['mode'])) {
        $mode = $_POST['mode'];
    }
    return $mode;
}

/**
 * 登録カード情報取得
 */
function lfGetStockCardData($arrData, $objPage) {
    $objQuery = new SC_Query();
    
    // 登録者の確認
    $ret = $objQuery->select("paygent_card", "dtb_customer", "customer_id = ?", array($arrData['customer_id']));
    // 登録者の情報取得
    if (count($ret) > 0) {
        $objPage->stock_flg = 1;
        if ($ret[0]['paygent_card'] == 1) {
            $arrRet = sfGetPaygentCreditStock($arrData);
            // 成功
            if ($arrRet[0]['result'] === "0") {
                foreach ($arrRet as $key => $val) {
                    if ($key != 0) {
                        $objPage->arrCardInfo[] = array("CardSeq" => $val['customer_card_id'],
                                                     "CardNo" => $val['card_number'],
                                                     "Expire" => $val['card_valid_term'],
                                                     "HolderName" => $val['cardholder_name']);
                    }
                }
            // 失敗
            } else {
                $objPage->tpl_error = "登録カード情報の取得に失敗しました。". $arrRet[0]['response'];
            }
        }
    }
    $objPage->cnt_card = count($objPage->arrCardInfo);
    if ($objPage->cnt_card >= 5) $objPage->stock_flg = 0;
    if ($objPage->cnt_card > 0) $objPage->tpl_onload = "fnCngStock();";
    
    return $objPage;
}
?>