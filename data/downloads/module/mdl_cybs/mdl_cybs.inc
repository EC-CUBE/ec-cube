<?php
/**
 * サイバーソースモジュールで共通使用する定数・配列・関数群
 *
 */
require_once MODULE_PATH . "mdl_cybs/class/mdl_cybs_config.php";
require_once MODULE_PATH . "mdl_cybs/class/mdl_cybs_request.php";

/**
 * dtb_order memo仕様
 * memo06 与信ステータス $arrCybsAuthStatus参照
 * memo07 request_token
 * memo08 request_id
 * memo09 支払い方法のキー名 $arrCybsPayMethod参照
 * memo10 返金実行済みフラグ 1:実行済み、1以外:未実行
 */

// モジュール番号
define('MDL_CYBS_ID', 14);

// ログパス
define('MDL_CYBS_LOG', DATA_PATH . 'logs/cybs.log');

// 拡張モジュール名
define('MDL_CYBS_MOD_CYBS', 'mod_cybs');

// サブスクリプションID登録数上限
define('MDL_CYBS_SUBS_ID_MAX', 5);

/**
 * mod_cybsをロードする
 *
 * @return boolean
 */
function sfCybsLoadModCybs() {
    if (!extension_loaded(MDL_CYBS_MOD_CYBS)) {
        if (@!dl(MDL_CYBS_MOD_CYBS . ".so")) {
            return false;
        }
    }
    return true;
}

// 本番URL
define('MDL_CYBS_REQUEST_URL', 'ics2.ic3.com');
// テストURL
define('MDL_CYBS_REQUEST_URL_TEST', 'ics2test.ic3.com');

// ICSエラー
$arrCybsIcsErr = array(
    'DCALL' => 'カード会社が与信判定を取り消しました。',
    'DCARDEXPIRED' => 'カードの有効期限切れです。',
    'DCARDREFUSED' => 'カード会社が与信を承認しませんでした。',
    'DINVALIDCARD' => 'クレジットカード番号が正しくありません。',
    'DINVALIDDATA' => 'リクエストに含まれるデータが不正です。',
    'DMISSINGFIELD' => 'リクエストに必須フィールドが欠けています。',
    'ESYSTEM' => 'システムエラーが発生しました。',
    'ETIMEOUT' => 'リクエストがタイムアウトしました。',
);

// リクエスト先URL配列
$arrCybsRequestURL = array(
    '1' => MDL_CYBS_REQUEST_URL_TEST,
    '2' => MDL_CYBS_REQUEST_URL
);

/**
 *  支払い方法
 *  array(
 *      '[支払い方法]-[分割回数]' => '支払い方法名称・分割回数',
 *      '[支払い方法]-[分割回数]' => '支払い方法名称・分割回数',
 *      ...
 *  );
 */
$arrCybsPayMethod = array(
    '1-0' => "一括払い",
    '4-3' => "分割3回払い",
    '4-6' => "分割6回払い",
    '4-10'=> "分割10回払い",
    '4-15'=> "分割15回払い",
    '4-20'=> "分割20回払い",
    '5-0' => "リボルビング払い"
);

// カード会社の種類
$arrCybsCardCompany = array(
    '001' => 'Visa',
    '002' => 'MasterCard',
    '003' => 'American Express',
    '004' => 'Discover',
    '005' => 'Diners Club',
    '006' => 'Carte Blanche',
    '007' => 'JCB',
    '008' => 'Optima',
    '010' => 'Benificial Private Label',
    '014' => 'EnRoute',
    '017' => 'MBNA',
    '021' => 'JAL',
    '027' => 'Nicos',
);

// 与信ステータス
define('MDL_CYBS_AUTH_STATUS_AUTH',       '1'); // 与信
define('MDL_CYBS_AUTH_STATUS_AUTHCANCEL', '2'); // 与信取り消し
define('MDL_CYBS_AUTH_STATUS_CAPTURE',    '3'); // 売上
define('MDL_CYBS_AUTH_STATUS_RETURN',     '4'); // 返金
$arrCybsAuthStatus = array(
    MDL_CYBS_AUTH_STATUS_AUTH       => '与信',
    MDL_CYBS_AUTH_STATUS_AUTHCANCEL => '与信取消',
    MDL_CYBS_AUTH_STATUS_CAPTURE    => '売上',
    MDL_CYBS_AUTH_STATUS_RETURN     => '返金'
);

// 管理画面で実行するICSアプリケーション
define('MDL_CYBS_APP_REVERSAL', '1');
define('MDL_CYBS_APP_BILL',     '2');
define('MDL_CYBS_APP_CREDIT',   '3');
$arrCybsApp = array(
    MDL_CYBS_APP_REVERSAL => 'ics_auth_reversal',
    MDL_CYBS_APP_BILL     => 'ics_bill',
    MDL_CYBS_APP_CREDIT   => 'ics_credit'
);

/**
 * サイバーソースにリクエストを送信する.
 * 引数の送信パラメータはlf(sf)CreateParam***()で生成する.
 *
 * @param array $arrSendParam
 * @return array
 */
function sfCybsSendRequest($arrSendParam) {
    $objRequest = new CYBS_REQ;

    foreach ($arrSendParam as $key => $value) {
        $objRequest->add_request($key, $value);
    }

    // カード番号はログ保存しない
    if (isset($arrSendParam['customer_cc_number'])) {
        $arrSendParam['customer_cc_number'] = '****';
    }

    gfPrintLog('### send request param ###', MDL_CYBS_LOG);
    gfPrintLog(print_r($arrSendParam, true), MDL_CYBS_LOG);

    if ( ($result = cybs_send($objRequest->requests)) == false ) {
        sfDispSiteError('');
        gfPrintLog(' -> error: cybs_send() function.' , MDL_CYBS_LOG);
    }

    return $result;
}

/**
 * レスポンスのエラーチェック
 *
 * @param array $arrResults
 * @return boolean|PEAR::Error
 */
function sfCybsIsError($arrResults) {
    global $arrCybsIcsErr;
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
        $msg = "処理が拒否されました。入力内容に間違いがないかご確認ください。\n";
        $rflag = $arrResults['ics_rflag'];
        $err = isset($arrCybsIcsErr[$rflag]) ? $arrCybsIcsErr[$rflag] : "エラーコード：$rflag";
        $ret = PEAR::raiseError($msg . $err);
        break;
    case '-1':
        $msg = "システムまたはネットワークエラーにより処理がエラーとなりました。\n";
        $rflag = $arrResults['ics_rflag'];
        $err = isset($arrCybsIcsErr[$rflag]) ? $arrCybsIcsErr[$rflag] : "エラーコード：$rflag";
        $ret = PEAR::raiseError($msg . $err);
        break;
    default:
        $ret = PEAR::raiseError("不明なエラーが発生しました。\n");
    }

    return $ret;
}

/**
 * 受注編集ページのテンプレート制御
 *
 * @param LC_Page $objPage
 * @return LC_Page
 */
function sfCybsOrderPage($objPage) {
    $objQuery = new SC_Query;
    $moduleId = $objQuery->get('dtb_payment', 'module_id', 'payment_id = ?', array($objPage->arrDisp['payment_id']));
    $objPage->cybs_disp = true;
    if ($moduleId != MDL_CYBS_ID) {
        $objPage->cybs_disp = false;
        return $objPage;
    }

    if ($objPage->disp_mode) {
        $objPage->cybs_disp = false;
        return $objPage;
    }

    global $arrCybsAuthStatus;
    $objPage->arrCybsAuthStatus = $arrCybsAuthStatus;


    $arrMemo = $objQuery->select(
        'memo06, memo07, memo08, memo09, memo10',
        'dtb_order',
        'order_id = ?',
        array($objPage->tpl_order_id));

    $objPage->arrCybsMemo = isset($arrMemo[0]) ? $arrMemo[0] : array();
    return $objPage;
}

/**
 * EC-CUBE側の与信ステータスを変更する
 * サイバーソース側と通信は行わない。変更はEC-CUBE側のみ。
 */
function sfCybsChangeAuthStatus($objPage, $status) {
    global $arrCybsAuthStatus;
    if (empty($arrCybsAuthStatus[$status])) {
        gfPrintLog('error: call sfCybsChangeAuthStatus()', MDL_CYBS_LOG);
        sfDispError('');
    }

    $arrUpdate = array(
        'memo06' => $status,
        'update_date' => 'NOW()'
    );
    $objQuery = new SC_Query;
    $objQuery->update('dtb_order', $arrUpdate, 'order_id = ?', array($objPage->tpl_order_id));

    $objPage->cybs_result = "ステータスを" . $arrCybsAuthStatus[$status] . "へ変更しました。";
    return $objPage;
}

/**
 * ICSアプリケーションの実行
 *
 */
function sfCybsDoIcsApplication($objPage, $ics_app) {
    global $arrCybsApp;
    if (empty($arrCybsApp[$ics_app])) {
        gfPrintLog('error: call sfCybsDoIcsApplication() error', MDL_CYBS_LOG);
        sfDispError('');
    }

    global $arrCybsRequestURL;

    $objConfig =& Mdl_Cybs_Config::getInstanse();
    $arrConfig = $objConfig->getConfig();

    $objQuery = new SC_Query;
    $arrOrder = $objQuery->select('*', 'dtb_order', 'order_id = ?', array($objPage->tpl_order_id));
    $arrOrder = $arrOrder[0];

    // 共通パラメータ
    $arrSendParam = array(
        "server_host"         => $arrCybsRequestURL[$arrConfig['cybs_request_url']],
        "server_port"         => "80",
        'currency'            => 'JPY',
        'merchant_id'         => $arrConfig['cybs_merchant_id'],
        'merchant_ref_number' => $objPage->tpl_order_id,
    );

    $resultMessage = '';
    $resultStatus  = '';

    switch($arrCybsApp[$ics_app]) {
    // 与信取消
    case 'ics_auth_reversal':
        $resultMessage = '与信取消処理は成功しました。';
        $resultStatus = MDL_CYBS_AUTH_STATUS_AUTHCANCEL;
        $arrSendParam['auth_request_token'] = $arrOrder['memo07'];
        $arrSendParam['auth_request_id']    = $arrOrder['memo08'];
        $arrSendParam['ics_applications']   = "ics_auth_reversal";
        $arrSendParam["offer0"] = "amount:" . $arrOrder['payment_total'];
        $arrResults = sfCybsSendRequest($arrSendParam);
        break;

    // 売上
    case 'ics_bill':
        $resultMessage = '売上処理は成功しました。';
        $resultStatus = MDL_CYBS_AUTH_STATUS_CAPTURE;
        $arrSendParam['auth_request_token'] = $arrOrder['memo07']; // request_token
        $arrSendParam['auth_request_id']    = $arrOrder['memo08']; // request_id
        $arrSendParam['ics_applications']   = "ics_bill";
        $arrSendParam["offer0"] = "amount:" . $arrOrder['payment_total'];
        list($method, $paytimes) = split("-", $arrOrder['memo09']); // paymethod 4-15 など
        $arrSendParam['jpo_payment_method'] = $method;
        if ($paytimes > 0) $arrSendParam['jpo_installments'] = $paytimes;
        $arrResults = sfCybsSendRequest($arrSendParam);
        break;

    // 売上取消
    case 'ics_credit':
        // 返金処理が実行済みならリクエストを送信しない。
        if ($arrOrder['memo10'] == '1') {
            $objPage->cybs_result = '既に返金されています。';
            return $objPage;
        }
        $resultMessage = '返金処理は成功しました。';
        $resultStatus = MDL_CYBS_AUTH_STATUS_RETURN;
        $arrSendParam['ics_applications']   = "ics_credit";
        $arrSendParam['bill_request_token'] = $arrOrder['memo07']; // request_token
        $arrSendParam['bill_request_id']    = $arrOrder['memo08']; // request_id
        $arrSendParam["offer0"] = "amount:" . $arrOrder['payment_total'];
        $arrResults = sfCybsSendRequest($arrSendParam);
        break;
    }

    if (PEAR::isError($e = sfCybsIsError($arrResults))) {
        $objPage->cybs_result = $e->getMessage();
        gfPrintLog(" -> " . $arrCybsApp[$ics_app] . " error: " . $e->getMessage(), MDL_CYBS_LOG);
        gfPrintLog(print_r($arrResults, true), MDL_CYBS_LOG);
        $objPage->cybs_result = $e->getMessage();
        return $objPage;
    }

    // request_token, request_idの更新
    $arrUpdate = array(
        'update_date' => 'NOW()',
        'memo07' => $arrResults['request_token'],
        'memo08' => $arrResults['request_id'],
    );
    // 返金処理成功時は返金済みフラグを立てる
    if ($arrCybsApp[$ics_app] == 'ics_credit') {
        $arrUpdate['memo10'] = '1';
    }
    $objQuery->update('dtb_order', $arrUpdate, 'order_id = ?', array($objPage->tpl_order_id));

    $objPage = sfCybsChangeAuthStatus($objPage, $resultStatus);
    $objPage->cybs_result = $resultMessage;
    return $objPage;
}

/**
 * 売上バッチ用CSVのダウンロード
 *
 */
function sfCybsCSVDownload($where, $arrval) {
    $objQuery = new SC_Query;
    $objQuery->setoption('ORDER BY update_date DESC');
    $arrOrder = $objQuery->select(
        'order_id, payment_total, memo08 as req_id, memo07 as req_token',
        'dtb_order',
        $where,
        $arrval);

    $line = '';
    $arrayOrder = is_array($arrOrder) ? $arrOrder : array();
    foreach ($arrOrder as $order) {
        $line .= implode(',', array(
            $order['order_id'],
            'JPY',
            $order['payment_total'],
            $order['req_id'],
            $order['req_token'])) . ",,,,,,,\r\n";
    }

    $batchId = uniqid('');
    $recordCount = count($arrayOrder);

    $head1 = "batchID=$batchId,transactionType=capture,recordCount=$recordCount,,,,,,,,,\r\n";
    $head2 = implode(',', array(
        'merchantReferenceCode*',
        'purchaseTotals_currency*',
        'purchaseTotals_grandTotalAmount*',
        'ccCaptureService_authRequestID*',
        'ccCaptureService_authRequestToken',
        'ccCaptureService_authType',
        'ccCaptureService_verbalAuthCode',
        'billTo_customerID',
        'merchantDefinedData_field1',
        'merchantDefinedData_field2',
        'merchantDefinedData_field3',
        'merchantDefinedData_field4')) . "\r\n";

    $csvString = $head1 . $head2 . $line;
    sfCSVDownload($csvString, 'cybs_capture');
}
