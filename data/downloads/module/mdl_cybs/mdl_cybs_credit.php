<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once MODULE_PATH . "mdl_cybs/mdl_cybs.inc";
require_once MODULE_PATH . "mdl_cybs/class/mdl_cybs_config.php";
require_once MODULE_PATH . "mdl_cybs/class/mdl_cybs_request.php";

// extensionがインストールされていなければエラー画面表示
if (!lfLoadModCybs()) {
    sfDispSiteError(FREE_ERROR_MSG, '', false,
        'この決済は使用することが出来ません。<br>お手数ですがお支払い方法を選択し直して下さい。');
}

class LC_Page {
    function LC_Page() {
        $this->tpl_mainpage = MODULE_PATH . 'mdl_cybs/mdl_cybs_credit.tpl';
        // 支払い方法
        global $arrPayMethod;
        $this->arrPayMethod = $arrPayMethod;
        // カード会社種類
        global $arrCardCompany;
        $this->arrCardCompany = $arrCardCompany;
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

$objCybs =& Mdl_Cybs_Config::getInstanse();
$objPage->enable_ondemand = $objCybs->enableOndemand(); //オンデマンド課金の使用可否
$objPage->tpl_payment_method = 'サイバーソース決済';

$objDate = new SC_Date();
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(RELEASE_YEAR + CREDIT_ADD_YEAR);
$objPage->arrYear = $objDate->getZeroYear();
$objPage->arrMonth = $objDate->getZeroMonth();

// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

$objForm = lfInitParam($_POST);
$objPage->arrForm = $objForm->getFormParamList();

switch(lfGetMode()) {

case 'register':
    // 入力項目の検証
    if ($arrErr = $objForm->checkError()) {
        $objPage->arrErr = $arrErr;
        break;
    }

    // カート集計処理
    $objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
    // 一時受注テーブルの読込
    $arrData = sfGetOrderTemp($uniqid);
    // カート集計を元に最終計算
    $arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

    /**
     * オンデマンド課金が有効で、かつカードを登録するにチェックが入っていた場合は、
     * サブスクリプションを作成する
     */
    if ($objCybs->enableOndemand() && $objForm->getValue('register_ondemand')) {
        // 登録件数のチェック
        if (!$objCybs->canAddSubsId()) {
            $objPage->tpl_error = '登録出来るカードの件数は' . MDL_CYBS_SUBS_ID_MAX . "件までです。\n";
            gfPrintLog(' -> ondemand error: over card max', MDL_CYBS_LOG);
            break;
        }
        // オンデマンド課金のリクエストを送信する
        $arrResults = lfSendRequest(lfCreateOndemandParam($objForm->getHashArray(), $arrData));
        if (PEAR::isError($e = lfIsError($arrResults))) {
            $objPage->tpl_error = $e->getMessage();
            gfPrintLog(' -> ondemand error: ' . $e->getMessage(), MDL_CYBS_LOG);
            gfPrintLog(print_r($arrResults, true), MDL_CYBS_LOG);
            break;
        }
        // サブスクリプションIDを顧客テーブルへ追加する
        $subsId = $arrResults['pay_subscription_create_subscription_id'];
        $objCybs->addSubsId($subsId);
    }

    $authAddParam = null; // チャージバック用追加パラメータ

    // 3Dセキュア使用設定判定
    if ($objCybs->use3D() && !$objCybs->enableOndemand()) {
        // 3Dセキュアリクエストを送信する
        $arrResults = lfSendRequest(lfCreateEnrollParam($objForm->getHashArray(), $arrData));
        // エラー処理
        if (PEAR::isError($e = lfIsError($arrResults))) {
            $objPage->tpl_error = $e->getMessage();
            gfPrintLog(' -> 3d(enroll) error: ' . $e->getMessage(), MDL_CYBS_LOG);
            gfPrintLog(print_r($arrResults, true), MDL_CYBS_LOG);
            break;
        }

        /**
         * 登録有りの場合はリダイレクトHTMLを出力する.
         * 登録無しでかつ与信へ進める場合(上記エラー判定でSOKの場合)は,
         * チャージバック用のパラメータを追加し次の与信リクエストへ進む.
         */
        if (isset($arrResults['pa_enroll_rflag']) && $arrResults['pa_enroll_rflag'] == 'DAUTHENTICATE') {
            $objPage->AcsUrl  = $arrResults['pa_enroll_acs_url'];
            $objPage->TermUrl = SSL_URL . 'shopping/load_payment_module.php';
            $objPage->PaReq   = $arrResults['pa_enroll_pareq'];
            $objPage->MD      = base64_encode(serialize($objForm->getHashArray())); // POSTデータをMDに保持
            $objPage->tpl_onload   = 'OnLoadEvent()';
            $objPage->tpl_mainpage = MODULE_PATH . 'mdl_cybs/mdl_cybs_credit_3d.tpl'; // リダイレクトHTML
            $objSiteSess->setRegistFlag();
            break;
        }
        // チャージバック用追加パラメータ
        $authAddParam = array(
            'e_commerce_indicator' => $arrResults['pa_enroll_e_commerce_indicator'],
            'eci_raw'              => '06',
        );
        // マスターカードのみのパラメータ
        if ($objForm->getValue('card_company') === '002') $authAddParam['ucaf_collection_indicator'] = '1';
    }

    $arrSendParam = array();
    // オンデマンド決済時のパラメータ構築
    if ($objCybs->enableOndemand() && $objForm->getValue('register_ondemand')) {
        $arrSendParam = lfCreateAuthParam($objForm->getHashArray(), $arrData);
        $arrSendParam = lfCreateOndemandAuthParam($subsId, $arrSendParam);

    // 通常決済のパラメータ構築
    } else {
        $arrSendParam = lfCreateAuthParam($objForm->getHashArray(), $arrData, $authAddParam);
    }

    // 与信リクエストを送信する
    $arrResults = lfSendRequest($arrSendParam);
    if (PEAR::isError($e = lfIsError($arrResults))) {
        $objPage->tpl_error = $e->getMessage();
        gfPrintLog(' -> auth error: ' . $e->getMessage(), MDL_CYBS_LOG);
        gfPrintLog(print_r($arrResults, true), MDL_CYBS_LOG);
        break;
    }

    $objSiteSess->setRegistFlag();
    //lfRegisterOrderTemp($uniqid, $objForm->getHashArray(), $arrResults);
    header("Location: " . URL_SHOP_COMPLETE);
    exit;

// パスワード入力からの戻り
case 'verify3d':
    // カート集計処理
    $objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
    // 一時受注テーブルの読込
    $arrData = sfGetOrderTemp($uniqid);
    // カート集計を元に最終計算
    $arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

    // 検証+与信リクエストを送信する
    $obj3DForm = lfInit3DParam($_POST);
    $arrResults = lfSendRequest(lfCreateValidateParam($obj3DForm->getHashArray(), $arrData));
    if (PEAR::isError($e = lfIsError($arrResults))) {
        $objPage->tpl_error = $e->getMessage();
        gfPrintLog(' -> error: ' . $e->getMessage(), MDL_CYBS_LOG);
        gfPrintLog(print_r($arrResults, true), MDL_CYBS_LOG);
        break;
    }

    $objSiteSess->setRegistFlag();
    header("Location: " . URL_SHOP_COMPLETE);
    break;

// 戻るボタン押下時
case 'return':
    $objSiteSess->setRegistFlag();
    header("Location: " . URL_SHOP_CONFIRM);
    exit;

// 通常表示
default:
    // TODO オンデマンド取得

}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
sfPrintR($_POST, 'blue');
sfPrintR($objPage, 'red');
/**
 * モードを取得する
 *
 * @return string
 */

function lfGetMode() {
    $mode = isset($_POST['mode']) ? $_POST['mode'] : '';

    // 3Dセキュアの戻り用。カード会社のパスワード入力画面から遷移する
    $obj3dForm = lfInit3DParam($_POST);
    if (!$obj3dForm->checkError()) {
        $mode = 'verify3d';
    }

    return $mode;
}

/**
 * 3Dセキュア戻り用パラメータの初期化
 *
 * @param array $arrParam
 * @return SC_FormParam
 */
function lfInit3DParam($arrParam) {
    $objForm = new SC_FormParam;
    $objForm->addParam("PaRes", "PaRes", '', "", array("EXIST_CHECK"));
    $objForm->addParam("MD", "MD", '', "", array("EXIST_CHECK"));
    $objForm->setParam($arrParam);
    return $objForm;
}

/**
 * パラメータの初期化
 *
 * @return SC_FormParam
 */
function lfInitParam($arrParam) {
    $objForm = new SC_FormParam;
    $objForm->addParam("カード番号1", "card_no01", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objForm->addParam("カード番号2", "card_no02", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objForm->addParam("カード番号3", "card_no03", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objForm->addParam("カード番号4", "card_no04", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objForm->addParam("カード会社", "card_company", 3, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objForm->addParam("カード期限年", "card_year", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
    $objForm->addParam("カード期限月", "card_month", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
    $objForm->addParam("姓", "card_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
    $objForm->addParam("名", "card_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
    $objForm->addParam("支払方法", "paymethod", STEXT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    $objForm->addParam("カード情報の登録", "register_ondemand", 1, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
    $objForm->setParam($arrParam);
    $objForm->convParam();
    return $objForm;
}

/**
 * サイバーソースにリクエストを送信する.
 * 引数の送信パラメータはlfCreateParam***()で生成する.
 *
 * @param array $arrSendParam
 * @return array
 */
function lfSendRequest($arrSendParam) {
    $objRequest = new CYBS_REQ;

    gfPrintLog('### send request param ###', MDL_CYBS_LOG);
    gfPrintLog(print_r($arrSendParam, true), MDL_CYBS_LOG);

    foreach ($arrSendParam as $key => $value) {
        $objRequest->add_request($key, $value);
    }

    if ( ($result = cybs_send($objRequest->requests)) == false ) {
        sfDispSiteError('');
        gfPrintLog(' -> error: cybs_send() function.' , MDL_CYBS_LOG);
    }

    return $result;
}

/**
 * ics_authパラメータを構築する.
 *
 * @param array $arrForm
 * @param array $arrData
 */
function lfCreateAuthParam($arrForm, $arrData, $addData = null) {
    global $arrCybsRequestURL;
    global $arrPref;

    $objConfig =& Mdl_Cybs_Config::getInstanse();
    $arrConfig = $objConfig->getConfig();

    $cardNo = $arrForm['card_no01'] . $arrForm['card_no02'] . $arrForm['card_no03'] . $arrForm['card_no04'];
    $expMo = $arrForm['card_month'];
    $expYr = '20' . $arrForm['card_year'];
    $phoneNo = $arrData['order_tel01'] . $arrData['order_tel02'] . $arrData['order_tel03'];

    $arrSendParam = array(
        "ics_applications"    => "ics_auth",
        "server_host"         => $arrCybsRequestURL[$arrConfig['cybs_request_url']],
        "server_port"         => "80",
        "card_type"           => $arrForm['card_company'],
        "customer_cc_number"  => $cardNo,
        "customer_cc_expmo"   => $expMo,
        "customer_cc_expyr"   => $expYr,
        "customer_firstname"  => lfToSjis($arrData['order_name02']),
        "customer_lastname"   => lfToSjis($arrData['order_name01']),
        "customer_email"      => $arrData['order_email'],
        "customer_phone"      => $phoneNo,
        "bill_address1"       => lfToSjis($arrData['order_addr02']),
        "bill_city"           => lfToSjis($arrData['order_addr01']),
        "bill_state"          => lfToSjis($arrPref[$arrData['order_pref']]),
        "bill_zip"            => $arrData['order_zip01'] . $arrData['order_zip02'],
        "bill_country"        => "JP",
        "merchant_id"         => $arrConfig['cybs_merchant_id'],
        "merchant_ref_number" => $arrData['order_id'],
        "currency"            => "JPY",
        /**
        "ship_to_address1"    => lfToSjis($arrData['deliv_addr02']),
        "ship_to_city"        => lfToSjis($arrData['deliv_addr01']),
        "ship_to_country"     => lfToSjis($arrPref[$arrData['deliv_pref']]),
        "ship_to_state"       => $arrData['deliv_zip01'] . $arrData['deliv_zip02'],
        "ship_to_zip"         => "JP",
        */
    );

    // 支払い方法
    list($method, $paytimes) = split("-", $arrForm['paymethod']);
    $arrSendParam["jpo_payment_method"] = $method;

    // 分割回数
    if ($paytimes > 0) $arrSendParam["jpo_installments"] = $paytimes;

    $arrSendParam["offer0"] = "amount:" . $arrData['payment_total'];

    if (is_array($addData)) {
        $arrSendParam = array_merge($arrSendParam, $addData);
    }
    return $arrSendParam;
}

/**
 * オンデマンド課金+与信リクエストパラメータを構築する.
 *
 * @param string $subsId
 * @param array $arrAuthParam
 * @return array
 */
function lfCreateOndemandAuthParam($subsId, $arrAuthParam) {
    return array(
        'subscription_id'     => $subsId,
        "ics_applications"    => "ics_auth",
        "server_host"         => $arrAuthParam['server_host'],
        "server_port"         => $arrAuthParam["server_port"],
        'merchant_id'         => $arrAuthParam['merchant_id'],
        'merchant_ref_number' => $arrAuthParam['merchant_ref_number'],
        'currency'            => $arrAuthParam['currency'],
        'offer0'              => $arrAuthParam['offer0']
    );
}

/**
 * ics_pa_enrollパラメータを構築する.
 *
 * @param array $arrForm
 * @param array $arrData
 */
function lfCreateEnrollParam($arrForm, $arrData) {
    global $arrInfo;
    global $arrCybsRequestURL;

    $objCustomer = new SC_Customer;

    $cardNo  = $arrForm['card_no01'] . $arrForm['card_no02'] . $arrForm['card_no03'] . $arrForm['card_no04'];
    $expMo   = $arrForm['card_month'];
    $expYr   = '20' . $arrForm['card_year'];
    $phoneNo = $arrData['order_tel01'] . $arrData['order_tel02'] . $arrData['order_tel03'];

    $objConfig =& Mdl_Cybs_Config::getInstanse();
    $arrConfig = $objConfig->getConfig();

    $arrSendParam = array(
        "ics_applications"    => "ics_pa_enroll",
        "server_host"         => $arrCybsRequestURL[$arrConfig['cybs_request_url']],
        "server_port"         => "80",
        'card_type'           => $arrForm['card_company'],
        'customer_account_id' => $objCustomer->getValue('customer_id'),
        "currency"            => "JPY",
        "customer_cc_expmo"   => $expMo,
        "customer_cc_expyr"   => $expYr,
        'customer_cc_number'  => $cardNo,
        "merchant_id"         => $arrConfig['cybs_merchant_id'],
        "merchant_ref_number" => $arrData['order_id'],
        'pa_http_accept'      => $_SERVER['HTTP_ACCEPT'],
        'pa_http_user_agent'  => $_SERVER['HTTP_USER_AGENT'],
        'pa_merchant_country_code' => 'JP',
        //'pa_merchant_id'      => $arrConfig['cybs_merchant_id'], // サイバーソースのマーチャントIDとは別
        'pa_merchant_name'    => lfToSjis($arrInfo['shop_name']),
        'pa_merchant_url'     => SSL_URL,
        'offer0'              => "amount:" . $arrData['payment_total']
    );
    return $arrSendParam;
}

/**
 * ics_pa_validateパラメータを構築する
 * ics_authパラメータとマージする
 *
 * @param array $arrForm MD及びPaReqが含まれる配列
 * @param array $arrData 一時受注データ
 * @return array
 */
function lfCreateValidateParam($arrForm, $arrData) {
    global $arrCybsRequestURL;
    $objCustomer = new SC_Customer;

    $objConfig =& Mdl_Cybs_Config::getInstanse();
    $arrConfig = $objConfig->getConfig();

    $arrCardData = unserialize(base64_decode($arrForm['MD']));

    $arrSendParam = array(
        "server_host"         => $arrCybsRequestURL[$arrConfig['cybs_request_url']],
        "server_port"         => "80",
        'card_type'           => $arrCardData['card_company'],
        'customer_account_id' => $objCustomer->getValue('customer_id'),
        "currency"            => "JPY",
        "merchant_id"         => $arrConfig['cybs_merchant_id'],
        "merchant_ref_number" => $arrData['order_id'],
        'pa_signedpares'      => trim($arrForm['PaRes']),
        'offer0'              => "amount:" . $arrData['payment_total']
    );

    $arrSendParam = lfCreateAuthParam($arrCardData, $arrData, $arrSendParam);
    $arrSendParam["ics_applications"] = "ics_pa_validate,ics_auth";

    return $arrSendParam;
}

/**
 * オンデマンド課金のパラメータを構築する
 *
 * @param array $arrForm フォームパラメータ(カード情報など)
 * @param array $arrData 一時受注データ
 * @return array
 */

function lfCreateOndemandParam($arrForm, $arrData) {
    global $arrCybsRequestURL;
    global $arrPref;
    $objCustomer = new SC_Customer;

    $objConfig =& Mdl_Cybs_Config::getInstanse();
    $arrConfig = $objConfig->getConfig();

    $cardNo = $arrForm['card_no01'] . $arrForm['card_no02'] . $arrForm['card_no03'] . $arrForm['card_no04'];
    $expMo = $arrForm['card_month'];
    $expYr = '20' . $arrForm['card_year'];
    $phoneNo = $arrData['order_tel01'] . $arrData['order_tel02'] . $arrData['order_tel03'];

    $arrSendParam = array(
        "ics_applications"    => "ics_pay_subscription_create",
        "server_host"         => $arrCybsRequestURL[$arrConfig['cybs_request_url']],
        "server_port"         => "80",
        "customer_account_id" => $objCustomer->getValue('customer_id'),
        "customer_cc_number"  => $cardNo,
        "customer_cc_expmo"   => $expMo,
        "customer_cc_expyr"   => $expYr,
        "customer_firstname"  => lfToSjis($arrData['order_name02']),
        "customer_lastname"   => lfToSjis($arrData['order_name01']),
        "customer_email"      => $arrData['order_email'],
        "customer_phone"      => $phoneNo,
        "bill_address1"       => lfToSjis($arrData['order_addr02']),
        "bill_city"           => lfToSjis($arrData['order_addr01']),
        "bill_state"          => lfToSjis($arrPref[$arrData['order_pref']]),
        "bill_zip"            => $arrData['order_zip01'] . $arrData['order_zip02'],
        "bill_country"        => "JP",
        "merchant_id"         => $arrConfig['cybs_merchant_id'],
        "merchant_ref_number" => $arrData['order_id'],
        "currency"            => "JPY",
        /**
        "ship_to_address1"    => lfToSjis($arrData['deliv_addr02']),
        "ship_to_city"        => lfToSjis($arrData['deliv_addr01']),
        "ship_to_country"     => lfToSjis($arrPref[$arrData['deliv_pref']]),
        "ship_to_state"       => $arrData['deliv_zip01'] . $arrData['deliv_zip02'],
        "ship_to_zip"         => "JP",
        */
        "recurring_disable_auto_auth" => 'Y',
        "recurring_frequency" => 'on-demand',
        "card_type"           => $arrForm['card_company'],
    );

    return $arrSendParam;
}


/**
 * レスポンスのエラーチェック
 *
 * @param array $arrResults
 * @return boolean|PEAR::Error
 */
function lfIsError($arrResults) {
    global $arrIcsErr;
    $ret = null;

    switch ($arrResults['ics_rcode']) {
    // 成功
    case '1':
        $ret = true;
        break;
    case '0':
        // 3Dセキュアの場合、ics_rflagがDAUTHENTICATEであれば登録あり
        if (isset($arrResults['pa_enroll_rflag'])
            && $arrResults['pa_enroll_rflag'] == 'DAUTHENTICATE') {

            $ret = true;
            break;
        }
        $msg = "処理が拒否されました。\nエラーコード：${arrResults['ics_rflag']}\n";
        $ret = PEAR::raiseError($msg);
        break;
    case '-1':
        $msg = "システムまたはネットワークエラーにより処理がエラーとなりました。\nエラーコード：${arrResults['ics_rflag']}\n";
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
    //$sqlval['memo02'] = serialize($arrResults['auth_auth_code']);

    $objQuery = new SC_Query;
    $objQuery->update("dtb_order_temp", $sqlval, "order_temp_id = ?", array($uniqid));
}
?>
