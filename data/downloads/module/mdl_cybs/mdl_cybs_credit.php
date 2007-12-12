<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once MODULE_PATH . "mdl_cybs/mdl_cybs.inc";
require_once MODULE_PATH . "mdl_cybs/class/mdl_cybs_config.php";
require_once MODULE_PATH . "mdl_cybs/class/mdl_cybs_request.php";
dl(MDL_CYBS_EXT);

class LC_Page {
    function LC_Page() {
        $this->tpl_mainpage = MODULE_PATH . 'mdl_cybs/mdl_cybs_credit.tpl';
        global $arrPayMethod;
        $this->arrPayMethod = $arrPayMethod;
        /**
         * session_start時のno-cacheヘッダーを抑制することで
         * 「戻る」ボタン使用時の有効期限切れ表示を抑制する。
         * private-no-expire:クライアントのキャッシュを許可する。
         */
        session_cache_limiter('private-no-expire');
    }
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCampaignSess = new SC_CampaignSession();
$arrInfo = sf_getBasisData();

$objDate = new SC_Date();
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(RELEASE_YEAR + CREDIT_ADD_YEAR);
$objPage->arrYear = $objDate->getZeroYear();
$objPage->arrMonth = $objDate->getZeroMonth();

// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

$objForm = lfInitParam();
$objPage->arrForm = $objForm->getFormParamList();

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
switch($mode) {

case 'register':
    // 入力項目の検証
    if ($arrErr = lfCheckError($objForm)) {
        $objPage->arrErr = $arrErr;
        break;
    }
    // カート集計処理
    $objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
    // 一時受注テーブルの読込
    $arrData = sfGetOrderTemp($uniqid);
    // カート集計を元に最終計算
    $arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

    // リクエストの送信
    gfPrintLog('#### cybs request start ###' , MDL_CYBS_LOG);
    $arrResults = lfSendRequest($objForm->getHashArray(), $arrData);
    // 結果の判定
    $e = lfIsError($arrResults);
    if (PEAR::isError($e)) {
        gfPrintLog('#### cybs request error ###' , MDL_CYBS_LOG);
        gfPrintLog('-> cybs request results' , MDL_CYBS_LOG);
        gfPrintLog(print_r($arrResults, true), MDL_CYBS_LOG);
        $objPage->tpl_error = $e->getMessage();
        break;
    }

    gfPrintLog('#### cybs request successfull ###', MDL_CYBS_LOG);
    gfPrintLog('-> cybs request results', MDL_CYBS_LOG);
    gfPrintLog(print_r($arrResults, true), MDL_CYBS_LOG);
    gfPrintLog('#### cybs request end ###' , MDL_CYBS_LOG);

    // 成功時は完了画面へ遷移
    $objSiteSess->setRegistFlag();
    lfRegisterOrderTemp($uniqid, $objForm->getHashArray(), $arrResults);
    header("Location: " . URL_SHOP_COMPLETE);
    exit;

// 戻るボタン押下時
case 'return':
    $objSiteSess->setRegistFlag();
    header("Location: " . URL_SHOP_CONFIRM);
    exit;

// 通常表示
default:
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);


/**
 * パラメータの初期化
 *
 * @return SC_FormParam
 */
function lfInitParam() {
    $objForm = new SC_FormParam;
    $objForm->addParam("カード番号1", "card_no01", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objForm->addParam("カード番号2", "card_no02", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objForm->addParam("カード番号3", "card_no03", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objForm->addParam("カード番号4", "card_no04", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objForm->addParam("カード期限年", "card_year", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
    $objForm->addParam("カード期限月", "card_month", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
    $objForm->addParam("姓", "card_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
    $objForm->addParam("名", "card_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
    $objForm->addParam("支払方法", "paymethod", STEXT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    $objForm->setParam($_POST);;
    return $objForm;
}

/**
 * 入力項目のエラーチェック
 *
 * @param SC_FormParam $objForm
 * @return array|null
 */
function lfCheckError($objForm) {
    if ($arrErr = $objForm->checkError()) {
        return $arrErr;
    }
    return null;
}

/**
 * リクエストを送信する
 *
 * @param array $arrForm
 * @param array $arrData
 * @return array
 */
function lfSendRequest($arrForm, $arrData) {
    global $objCartSess;
    global $arrCybsRequestURL;
    global $arrPref;

    $objConfig =& new Mdl_Cybs_Config;
    $objRequest = new CYBS_REQ;

    $arrConfig = $objConfig->getConfig();

    $cardNo = $arrForm['card_no01'] . $arrForm['card_no02'] . $arrForm['card_no03'] . $arrForm['card_no04'];
    $expMo = $arrForm['card_month'];
    $expyr = '20' . $arrForm['card_year'];
    $phoneNo = $arrData['order_tel01'] . $arrData['order_tel02'] . $arrData['order_tel03'];

    $objRequest->add_request("server_host", $arrCybsRequestURL[$arrConfig['cybs_request_url']]);
    $objRequest->add_request("server_port", "80");
    $objRequest->add_request("ics_applications", "ics_auth");
    $objRequest->add_request("merchant_id", $arrConfig['cybs_merchant_id']);
    $objRequest->add_request("customer_cc_number", $cardNo);
    $objRequest->add_request("customer_cc_expmo", $expMo);
    $objRequest->add_request("customer_cc_expyr", $expyr);
    $objRequest->add_request("customer_firstname", lfToSjis($arrData['order_name02']));
    $objRequest->add_request("customer_lastname", lfToSjis($arrData['order_name01']));
    $objRequest->add_request("customer_email", $arrData['order_email']);
    $objRequest->add_request("customer_phone", $phoneNo);
    $objRequest->add_request("bill_address1", lfToSjis($arrData['order_addr02']));
    $objRequest->add_request("bill_city", lfToSjis($arrData['order_addr01']));
    $objRequest->add_request("bill_state", lfToSjis($arrPref[$arrData['order_pref']]));
    $objRequest->add_request("bill_zip", $arrData['order_zip01'] . $arrData['order_zip02']);
    $objRequest->add_request("bill_country", "JP");
    $objRequest->add_request("merchant_ref_number", $arrData['order_id']);
    $objRequest->add_request("currency", "JPY");
    // 支払い方法
    list($method, $paytimes) = split("-", $arrForm['paymethod']);
    $objRequest->add_request("jpo_payment_method", $method);
    if ($paytimes > 0) $objRequest->add_request("jpo_installments", $paytimes);

    $objRequest->add_request("offer0", "offerid:0^amount:" . $arrData['payment_total']);

    $request_array = $objRequest->requests;
    gfPrintLog(print_r($request_array, true), MDL_CYBS_LOG);

    if( ($result = cybs_send($request_array)) == false ) {
      print("error");
      gfPrintLog('#### cybs_send() error ###' , MDL_CYBS_LOG);
      exit;
    }
    return $result;
}

/**
 * リクエストのエラーチェック
 *
 * @param array $arrResults
 * @return boolean|PEAR::Error
 */
function lfIsError($arrResults) {
    global $arrIcsAuthErr;
    $ret = null;

    switch ($arrResults['ics_rcode']) {
    // 成功
    case '1':
        $ret = true;
        break;
    case '0':
        $msg = "処理が拒否されました。\n" . $arrIcsAuthErr[$arrResults['ics_rflag']];
        $ret = PEAR::raiseError($msg);
        break;
    case '-1':
        $msg = "システムまたはネットワークエラーにより処理がエラーとなりました。\n" . $arrIcsAuthErr[$arrResults['ics_rflag']];
        $ret = PEAR::raiseError($msg);
        break;
    default:
        $ret = PEAR::raiseError("不明なエラーが発生しました。\n");
    }

    return $ret;
}

/**
 * SJISへ変換する
 *
 * @param string $str
 * @return string
 */
function lfToSjis($str) {
    return mb_convert_encoding($str, 'SJIS', CHAR_CODE);
}

/**
 * 入力情報を一部記録
 *
 * @param string $uniqid
 * @param array $arrForm
 */
function lfRegisterOrderTemp($uniqid, $arrForm, $arrResults) {
    $sqlval = array();
    $sqlval['memo03'] = $arrForm['card_name01'] . " " . $arrForm['card_name02'];
    $sqlval['memo02'] = serialize($arrResults['auth_auth_code']);

    $objQuery = new SC_Query;
    $objQuery->update("dtb_order_temp", $sqlval, "order_temp_id = ?", array($uniqid));
}

?>
