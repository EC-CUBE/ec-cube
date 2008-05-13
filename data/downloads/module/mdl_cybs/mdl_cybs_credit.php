<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once MODULE_PATH . "mdl_cybs/mdl_cybs.inc";

// モバイルはエラー画面表示
if (GC_MobileUserAgent::isMobile()) {
    sfDispSiteError(
        FREE_ERROR_MSG,
        '',
        false,
        'この決済は使用することが出来ません。<br>お手数ですがお支払い方法を選択し直して下さい。',
        true);
}

// extensionがインストールされていなければエラー画面表示
if (!sfCybsLoadModCybs()) {
    sfDispSiteError(FREE_ERROR_MSG, '', false,
        'この決済は使用することが出来ません。<br>お手数ですがお支払い方法を選択し直して下さい。');
}

class LC_Page {
    function LC_Page() {
        $this->tpl_css      = URL_DIR . 'css/layout/shopping/confirm.css';
        $this->tpl_mainpage = MODULE_PATH . 'mdl_cybs/mdl_cybs_credit.tpl';
        // 支払い方法
        global $arrCybsPayMethod;
        $this->arrPayMethod = $arrCybsPayMethod;
        // カード会社種類
        global $arrCybsCardCompany;
        $this->arrCardCompany = $arrCybsCardCompany;
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
$objPage->enable_ondemand = $objCybs->enableOndemand(); // オンデマンド課金の使用可否
$objPage->can_add_subsid  = $objCybs->canAddSubsId();   // カード登録数上限の判定

$objPage->tpl_payment_method = 'サイバーソース決済';

$objDate = new SC_Date();
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(RELEASE_YEAR + CREDIT_ADD_YEAR);
$objPage->arrYear = $objDate->getZeroYear();
$objPage->arrMonth = $objDate->getZeroMonth();

// ユーザユニークIDの取得と購入状態の正当性をチェック
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// カート集計処理
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
// 一時受注テーブルの読込
$arrData = sfGetOrderTemp($uniqid);
// カート集計を元に最終計算
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

$objForm = lfInitParam($_POST);
$objPage->arrForm = $objForm->getFormParamList();

$objCybs->deleteSubsId(200);

switch(lfGetMode()) {

case 'register':
    // 入力項目の検証
    if ($arrErr = $objForm->checkError()) {
        $objPage->arrErr = $arrErr;
        break;
    }

    $authAddParam = null; // チャージバック用追加パラメータ

    // 3Dセキュア使用設定判定
    if ($objCybs->use3D() && lfIs3DCard($objForm->getValue('card_company'))) {
        // 3Dセキュアリクエストを送信する
        $arrResults = sfCybsSendRequest(lfCreateEnrollParam($objForm->getHashArray(), $arrData));
        // エラー処理
        if (PEAR::isError($e = sfCybsIsError($arrResults))) {
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

    $arrSendParam = lfCreateAuthParam($objForm->getHashArray(), $arrData, $authAddParam);
    /**
     * オンデマンド課金が有効で、かつカードを登録するにチェックが入っていた場合は、
     * サブスクリプション作成パラメータを追加する
     */
    if ($objCybs->enableOndemand() && $objForm->getValue('register_ondemand')) {
        //$arrSendParam = lfCreateAuthParam($objForm->getHashArray(), $arrData);
        //$arrSendParam = lfCreateOndemandAuthParam($subsId, $arrSendParam);
        // 登録件数のチェック
        if (!$objCybs->canAddSubsId()) {
            $objPage->tpl_error = '登録出来るカードの件数は' . MDL_CYBS_SUBS_ID_MAX . "件までです。\n";
            gfPrintLog(' -> ondemand error: over card max ', MDL_CYBS_LOG);
            break;
        }
        $arrSendParam['ics_applications']           .= ',ics_pay_subscription_create';
        $arrSendParam['recurring_disable_auto_auth'] = 'Y';
        $arrSendParam['recurring_frequency']         = 'on-demand';
        $arrSendParam['card_type']                   = $objForm->getValue('card_company');
    }

    // 与信リクエストを送信する
    $arrResults = sfCybsSendRequest($arrSendParam);
    if (PEAR::isError($e = sfCybsIsError($arrResults))) {
        $objPage->tpl_error = $e->getMessage();
        gfPrintLog(' -> auth error: ' . $e->getMessage(), MDL_CYBS_LOG);
        gfPrintLog(print_r($arrResults, true), MDL_CYBS_LOG);
        break;
    }

    // サブスクリプションIDを顧客テーブルへ追加する
    if (isset($arrResults['pay_subscription_create_subscription_id'])) {
        $objCybs->addSubsId($arrResults['pay_subscription_create_subscription_id'], $arrResults['merchant_ref_number']);
    }

    $objSiteSess->setRegistFlag();
    lfRegisterOrderTemp($uniqid, $arrResults, $objForm->getHashArray());
    header("Location: " . URL_SHOP_COMPLETE);
    exit;
    break;

// 登録カードの使用
case 'ondemand':
    // 入力項目の検証
    $subsId = $objForm->getValue('subs_id');
    $paymethod = $objForm->getValue('ondemand_paymethod');
    $arrErr = $objForm->checkError();
    if (empty($subsId) || !empty($arrErr['subs_id'])) {
        $objPage->arrErr['subs_id'] = '※　使用するカードを選択して下さい。';
        break;
    }
    if (empty($paymethod) || !empty($arrErr['ondemand_paymethod'])) {
        $objPage->arrErr['ondemand_paymethod'] = '※　お支払い方法を選択して下さい。';
        break;
    }
    $arrForm = $objForm->getHashArray();
    $arrForm['paymethod'] = $paymethod;

    $arrSendParam = lfCreateAuthParam($arrForm, $arrData);
    $arrSendParam = lfCreateOndemandAuthParam($subsId, $arrSendParam);
    $arrResults = sfCybsSendRequest($arrSendParam);
    if (PEAR::isError($e = sfCybsIsError($arrResults))) {
        $objPage->tpl_error = $e->getMessage();
        gfPrintLog(' -> auth error: ' . $e->getMessage(), MDL_CYBS_LOG);
        gfPrintLog(print_r($arrResults, true), MDL_CYBS_LOG);
        break;
    }

    $objSiteSess->setRegistFlag();
    lfRegisterOrderTemp($uniqid, $arrResults, $arrForm);
    header("Location: " . URL_SHOP_COMPLETE);
    exit;
    break;

// パスワード入力からの戻り
case 'verify3d':
    // 検証+与信リクエストを送信する
    $obj3DForm = lfInit3DParam($_POST);
    $arrResults = sfCybsSendRequest(lfCreateValidateParam($obj3DForm->getHashArray(), $arrData));
    if (PEAR::isError($e = sfCybsIsError($arrResults))) {
        $objPage->tpl_error = $e->getMessage();
        gfPrintLog(' -> error: ' . $e->getMessage(), MDL_CYBS_LOG);
        gfPrintLog(print_r($arrResults, true), MDL_CYBS_LOG);
        break;
    }

    $objSiteSess->setRegistFlag();
    $arrForm = unserialize(base64_decode($arrResults['MD']));
    lfRegisterOrderTemp($uniqid,  $arrResults, $arrForm);
    header("Location: " . URL_SHOP_COMPLETE);
    exit;
    break;

// カードの削除
case 'delete':
    // 入力項目の検証
    $index = $objForm->getValue('delete_subs_index');
    if (isset($index) && is_numeric($index)) {
        $objCybs->deleteSubsId($index);
    }
    break;

// 戻るボタン押下時
case 'return':
    $objSiteSess->setRegistFlag();
    header("Location: " . URL_SHOP_CONFIRM);
    exit;

// 通常表示
default:
}

lfSetCybsCardInfo($objPage); // 登録済みカード情報をセットする

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

function lfSetCybsCardInfo(&$objPage) {
    $objCybs =& Mdl_Cybs_Config::getInstanse();
    // オンデマンド課金が無効ならreturn
    if (!$objCybs->enableOndemand()) {
        return;
    }

    // サブスクリプションIDを取得
    $arrSubsIds = $objCybs->getSubsIds();

    $objPage->cardCount = 0; // サブスクリプションの登録件数

    foreach ($arrSubsIds as $subs) {
        $arrSendParam = lfCreateOndemandRetParam($subs['subs_id'], $subs['merchant_ref_number']);
        $arrResults = sfCybsSendRequest($arrSendParam);

        if (PEAR::isError($e = sfCybsIsError($arrResults))) {
            $objPage->tpl_error = $e->getMessage();
            gfPrintLog(' -> get subs info error: ' . $e->getMessage(), MDL_CYBS_LOG);
            gfPrintLog(print_r($arrResults, true), MDL_CYBS_LOG);
            return;
        }
        $objPage->cardCount++;
        $objPage->arrCard[] = $arrResults; // カード情報をテンプレートへassign
    }
}

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
    $objForm->addParam("使用するカード", "subs_id", MTEXT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
    $objForm->addParam("削除カード", "delete_subs_index", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objForm->setParam($arrParam);
    $objForm->convParam();
    return $objForm;
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
    $arrSendParam = array(
        'subscription_id'     => $subsId,
        "ics_applications"    => "ics_auth",
        "server_host"         => $arrAuthParam['server_host'],
        "server_port"         => $arrAuthParam["server_port"],
        'merchant_id'         => $arrAuthParam['merchant_id'],
        'merchant_ref_number' => $arrAuthParam['merchant_ref_number'],
        'currency'            => $arrAuthParam['currency'],
        'offer0'              => $arrAuthParam['offer0'],
        'jpo_payment_method'  => $arrAuthParam['jpo_payment_method'],
    );

    // 分割回数
    if (isset($arrAuthParam["jpo_installments"])) {
        $arrSendParam['jpo_installments'] = $arrAuthParam['jpo_installments'];
    }
    return $arrSendParam;
}

function lfCreateOndemandRetParam($subsId, $merchant_ref_number) {
    global $arrCybsRequestURL;

    $objConfig =& Mdl_Cybs_Config::getInstanse();
    $arrConfig = $objConfig->getConfig();

    return array(
        'subscription_id'     => $subsId,
        "ics_applications"    => "ics_pay_subscription_retrieve",
        "server_host"         => $arrCybsRequestURL[$arrConfig['cybs_request_url']],
        "server_port"         => "80",
        'merchant_id'         => $arrConfig['cybs_merchant_id'],
        'merchant_ref_number' => $merchant_ref_number,
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
        //'pa_merchant_id'      => $arrConfig['cybs_merchant_id'],
        'pa_merchant_name'    => lfToSjis($arrInfo['shop_name']),
        'pa_merchant_url'     => SSL_URL,
    );

    $arrSendParam["offer0"] = "amount:" . $arrData['payment_total'];

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
    );

    $arrSendParam["offer0"] = "amount:" . $arrData['payment_total'];

    $arrSendParam = lfCreateAuthParam($arrCardData, $arrData, $arrSendParam);
    $arrSendParam["ics_applications"] = "ics_pa_validate,ics_auth";

    return $arrSendParam;
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
function lfRegisterOrderTemp($uniqid, $arrResults, $arrForm) {
    $sqlval = array(
        'memo06' => MDL_CYBS_AUTH_STATUS_AUTH,
        'memo07' => $arrResults['request_token'],
        'memo08' => $arrResults['request_id'],
        'memo09' => $arrForm['paymethod']
        //'memo10' => $arrResults[''],
    );
    $objQuery = new SC_Query;
    $objQuery->update("dtb_order_temp", $sqlval, "order_temp_id = ?", array($uniqid));
}

/**
 * 3D対応カードかどうかをチェックする
 *
 * @paramカード種別 $cardtype
 * @return boolean
 */
function lfIs3DCard($cardtype) {
    $arrCardType = array('001', '002', '007');
    if (in_array($cardtype, $arrCardType)) {
        return true;
    }
    return false;
}
?>
