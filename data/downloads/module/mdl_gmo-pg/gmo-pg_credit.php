<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once(MODULE_PATH . "mdl_gmo-pg/mdl_gmo-pg.inc");

class LC_Page {
    function LC_Page() {
        $this->tpl_css = '/css/layout/shopping/card.css';

        if (GC_MobileUserAgent::isMobile()) {
            $this->tpl_mainpage = MODULE_PATH . "mdl_gmo-pg/gmo-pg_credit_mobile.tpl";
        } else {
            $this->tpl_mainpage = MODULE_PATH . "mdl_gmo-pg/gmo-pg_credit.tpl";
        }
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
$objView = GC_MobileUserAgent::isMobile() ? new SC_MobileView() : new SC_SiteView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$arrInfo     = sf_getBasisData();
$arrGMOConf  = sfGetPaymentDB();

// アクセスの正当性の判定
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// パラメータ管理クラス
$objFormParam = lfInitParam();

switch(lfGetMode()) {
// 「次へ」ボタン押下時
case 'regist':
    gfPrintLog('### GMO PG MODULE Start ###', GMO_LOG_PATH);
    // 入力値の変換
    $objFormParam->convParam();
    $arrErr = $objFormParam->checkError();

    // 入力エラーの判定
    if (!empty($arrErr)) {
        $objPage->arrErr = $arrErr;
        break;
    }

    // カート集計処理
    $objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
    // 一時受注テーブルの読込
    $arrData = sfGetOrderTemp($uniqid);
    // カート集計を元に最終計算
    $arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);
    // POSTデータを取得
    $arrVal = $objFormParam->getHashArray();

    // エラーフラグ
    $err_flg = false;
    // 通信エラーの判定
    $access_err = false;
    // 店舗情報エラーの判定
    $credit_err = false;
    // エラーメッセージ
    $gmo_err_msg = "";

    // アクセスIDがセットされていない場合、EntryTrainへリクエストを送信する
    if(empty($_SESSION['GMO']['ACCESS_ID'])) {
        gfPrintLog('-> EntryTrain Start.', GMO_LOG_PATH);
        // 店舗情報の送信
        $arrEntryRet = lfSendGMOEntry($arrData['order_id'], $arrData['payment_total']);
        if (empty($arrEntryRet)) {
            gfPrintLog('-> EntryTrain failed. access error.', GMO_LOG_PATH);
            $access_err = true;
        }

        // 店舗情報エラーの判定
        if ($arrEntryRet['ERR_CODE'] == '0' && $arrEntryRet['ERR_INFO'] == 'OK') {
            gfPrintLog('-> EntryTrain success.', GMO_LOG_PATH);
            $_SESSION['GMO']['ACCESS_ID'] = $arrEntryRet['ACCESS_ID'];
            $_SESSION['GMO']['ACCESS_PASS'] = $arrEntryRet['ACCESS_PASS'];
        } else {
            gfPrintLog('-> EntryTrain failed. credit error.', GMO_LOG_PATH);
            unset($_SESSION['GMO']);
            $credit_err = true;
            $detail_code01 = substr($arrEntryRet['ERR_INFO'], 0, 5);
            $detail_code02 = substr($arrEntryRet['ERR_INFO'], 5, 4);
            $gmo_err_msg = $detail_code01 . "-" . $detail_code02;
        }
    }

    // EntryTrainでエラーなしの場合はExecTrainを実行する
    if(!$access_err && !$credit_err) {
        gfPrintLog('-> ACESS_ID check Start.', GMO_LOG_PATH);
        // 店舗情報送信結果
        $sqlval['memo04'] = $arrEntryRet['ERR_CODE'];
        $sqlval['memo05'] = $arrEntryRet['ERR_INFO'];

        // 店舗情報エラーの判定
        if(!empty($_SESSION['GMO']['ACCESS_ID']) && !empty($_SESSION['GMO']['ACCESS_PASS'])) {
            gfPrintLog('-> ACESS_ID check OK.', GMO_LOG_PATH);
            gfPrintLog('-> ExecTrain Start.', GMO_LOG_PATH);
            // 決済情報の送信
            $arrExecRet = lfSendGMOExec(
                $_SESSION['GMO']['ACCESS_ID'],
                $_SESSION['GMO']['ACCESS_PASS'],
                $arrData['order_id'],
                $arrVal['card_no01'],
                $arrVal['card_no02'],
                $arrVal['card_no03'],
                $arrVal['card_no04'],
                $arrVal['card_month'],
                $arrVal['card_year'],
                $arrVal['paymethod']
            );
            if (empty($arrExecRet)) {
                gfPrintLog('-> ExecTrain failed. access error.', GMO_LOG_PATH);
                $access_err = true;
            } else {
                gfPrintLog('-> ExecTrain success.', GMO_LOG_PATH);
            }
        } else {
            gfPrintLog('-> ACESS_ID check failed.', GMO_LOG_PATH);
        }
    }

    // ExecTrainで通信エラーなしの場合
    if (!$access_err && !$credit_err) {
        // 3Dセキュアが有効な場合はリダイレクトページを出力する
        if (lfEnable3D()) {
            gfPrintLog('-> 3D secure is enable.', GMO_LOG_PATH);
            $_SESSION['GMO']['3d']['memo'] = array(
                'memo02' => serialize(array()),
                'memo03' => $arrVal['card_name01'] . " " . $arrVal['card_name02'],
            );

            $objPage->tpl_onload = 'OnLoadEvent();';
            // TODO mobile
            $objPage->tpl_mainpage = MODULE_PATH . "mdl_gmo-pg/gmo-pg-3d.tpl";
            $objPage->ACSUrl = $arrExecRet['ACSUrl'];
            $objPage->PaReq = $arrExecRet['PaReq'];;
            $objPage->TermUrl = GMO_RETURL;
            $objPage->MD = $arrExecRet['MD'];
            break;

        // 3Dセキュア無効時は、エラーチェック・購入完了ページへリダイレクト
        } else {
            gfPrintLog('-> 3D secure is not enable.', GMO_LOG_PATH);
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
            if($arrExecRet['Html'] == "Receipt" && empty($arrExecRet['ErrType']) && empty($arrExecRet['ErrInfo'])) {
                gfPrintLog('-> ExecTrain results success.', GMO_LOG_PATH);
                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // アクセスIDをクリアする。
                unset($_SESSION['GMO']);
                // 処理完了ページへ
                if (GC_MobileUserAgent::isMobile()) {
                    header("Location: " . gfAddSessionId(URL_SHOP_COMPLETE));
                } else {
                    header("Location: " . URL_SHOP_COMPLETE);
                }
            } else {
                gfPrintLog('-> ExecTrain results error.', GMO_LOG_PATH);
                $credit_err = true;
                $detail_code01 = substr($arrExecRet['ErrInfo'], 0, 5);
                $detail_code02 = substr($arrExecRet['ErrInfo'], 5, 4);
                $gmo_err_msg = $detail_code01 . "-" . $detail_code02;
            }
        }
    }

    if($access_err || $credit_err) {
        if ($access_err) {
            $objPage->tpl_error = "※ クレジット承認に失敗しました：通信エラー";
        } else {
            if (!empty($gmo_err_msg)) {
                $objPage->tpl_error = "※ クレジット承認に失敗しました：" . $gmo_err_msg;
            } else {
                $objPage->tpl_error = "※ クレジット承認に失敗しました：不明なエラー";
            }
        }
    }

    break;

// 3D認証結果を受け取る
case '3dVerify':
    gfPrintLog('-> 3D secure Post param check start.', GMO_LOG_PATH);
    if (PEAR::isError($e = lfValidate3dVerify())) {
        $objPage->tpl_error = $e->getMessage();
        gfPrintLog('-> 3D secure Post param check error.', GMO_LOG_PATH);
        break;
    }
    gfPrintLog('-> 3D secure Post param check success.', GMO_LOG_PATH);
    gfPrintLog('-> 3dVerify start.', GMO_LOG_PATH);
    $arr3dRet = lfSend3dVerify();

    if (PEAR::isError($e = lf3dVerifyIsSuccess($arr3dRet))) {
        gfPrintLog('-> 3dVerify failed. ' . $e->getMessage(), GMO_LOG_PATH);
        $objPage->tpl_error = $e->getMessage();
        break;
    }
    gfPrintLog('-> 3dVerify success.', GMO_LOG_PATH);

    //
    $sqlval = $_SESSION['GMO']['3d']['memo'];

    // 決済情報送信結果
    $sqlval['memo06'] = $arr3dRet['ErrType'];
    $sqlval['memo07'] = $arr3dRet['ErrInfo'];

    $objQuery = new SC_Query();
    $objQuery->update("dtb_order_temp", $sqlval, "order_temp_id = ?", array($uniqid));

    // 正常に登録されたことを記録しておく
    $objSiteSess->setRegistFlag();
    // GMO用のセッション情報をクリアする。
    unset($_SESSION['GMO']);
    // 処理完了ページへ
    if (GC_MobileUserAgent::isMobile()) {
        header("Location: " . gfAddSessionId(MOBILE_URL_SHOP_COMPLETE));
    } else {
        header("Location: " . URL_SHOP_COMPLETE);
    }

break;

// 確認ページに戻る
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

/**
 * modeを取得する.
 *
 * @retrun string
 */
function lfGetMode() {
    $mode = '';

    if (isset($_POST['PaRes']) && isset($_POST['MD'])) {
        $mode = '3dVerify';
    } elseif (isset($_POST['mode'])) {
        $mode = $_POST['mode'];
    }

    return $mode;
}

/**
 * パラメータ情報の初期化
 *
 * @return SC_FormParam
 */
function lfInitParam() {
    $objFormParam = new SC_FormParam();
    $objFormParam->addParam("カード番号1", "card_no01", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("カード番号2", "card_no02", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("カード番号3", "card_no03", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("カード番号4", "card_no04", CREDIT_NO_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("カード期限年", "card_year", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("カード期限月", "card_month", 2, "n", array("EXIST_CHECK", "NUM_COUNT_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("姓", "card_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
    $objFormParam->addParam("名", "card_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "ALPHA_CHECK"));
    $objFormParam->addParam("支払方法", "paymethod", STEXT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));

    $objFormParam->setParam($_POST);

    return $objFormParam;
}

/**
 * 受注情報を登録する.
 *
 * @param integer $order_id
 * @param integer $amount
 * @param integer $tax
 * @return array
 */
function lfSendGMOEntry($order_id, $amount, $tax = 0) {
    $arrGMOConf = sfGetPaymentDB();

    $TdTenantName = '';
    // 3Dセキュアが有効な場合は、店舗名を設定する
    if (!empty($arrGMOConf[0]['gmo_3d'])) {
        $arrSiteInfo = sf_getBasisData();
        $TdTenantName = base64_encode($arrSiteInfo['shop_name']);
    }

    $arrSendData = array(
        'OrderId' => $order_id,                      // 店舗ごとに一意な注文IDを送信する。
        'TdTenantName' => $TdTenantName,             // 3D認証時表示用店舗名
        'TdFlag' => $arrGMOConf[0]['gmo_3d'],        // 3Dフラグ
        'ShopId' => $arrGMOConf[0]['gmo_shopid'],    // ショップID
        'ShopPass' => $arrGMOConf[0]['gmo_shoppass'],// ショップパスワード
        'Currency' => 'JPN',                         // 通貨コード
        'Amount' => $amount,                         // 金額
        'Tax' => $tax,                               // 消費税
        'JobCd' => 'AUTH',                           // 処理区分
        'TenantNo' => $arrGMOConf[0]['gmo_tenantno'],// 店舗IDを送信する。
    );
gfPrintLog('EntryTrain Request', GMO_LOG_PATH);
gfPrintLog(print_r($arrSendData, true), GMO_LOG_PATH);
    $req = new HTTP_Request(GMO_ENTRY_URL);
    $req->setMethod(HTTP_REQUEST_METHOD_POST);
    $req->addPostDataArray($arrSendData);

    if (!PEAR::isError($req->sendRequest())) {
        $respBody = $req->getResponseBody();
    }
gfPrintLog('EntryTrain Response', GMO_LOG_PATH);
gfPrintLog(print_r(lfGetPostArray($respBody), true), GMO_LOG_PATH);
    return lfGetPostArray($respBody);
}

/**
 * 決済の実行
 *
 * @param string $access_id
 * @param string $access_pass
 * @param integer $order_id
 * @param integer $cardno1
 * @param integer $cardno2
 * @param integer $cardno3
 * @param integer $cardno4
 * @param integer $ex_mm
 * @param integer $ex_yy
 * @param integer $paymethod
 * @return array
 */
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
        'CardType' => 'VISA, 11111, 111111111111111111111111111111111111, 1111111111',
        'Method' => $method,
        'PayTimes' => $paytimes,
        'CardNo' => $cardno1 . $cardno2 . $cardno3 . $cardno4,
        'ExpireYYMM' => $ex_yy . $ex_mm ,
        'ClientFieldFlag' => '1',
        'ClientField1' => 'f1',
        'ClientField2' => 'f2',
        'ClientField3' => 'f3',
        'ModiFlag' => '1',
        'HTTP_ACCEPT' => $_SERVER['HTTP_ACCEPT'],
        'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT']
    );
gfPrintLog('ExecTrain Request', GMO_LOG_PATH);
gfPrintLog(print_r($arrData, true), GMO_LOG_PATH);
    $req = new HTTP_Request(GMO_EXEC_URL);
    $req->setMethod(HTTP_REQUEST_METHOD_POST);

    $req->addPostDataArray($arrData);

    if (!PEAR::isError($req->sendRequest())) {
        $response = $req->getResponseBody();
    }

    $arrRet = lfParseExecResponse($response);
gfPrintLog('ExecTrain Response', GMO_LOG_PATH);
gfPrintLog(print_r($arrRet, true), GMO_LOG_PATH);
gfPrintLog($response, GMO_LOG_PATH);
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

function lfParseExecResponse($queryString) {
    // 3Dセキュアでない場合はlfGetPostArray()でparseする
    if (!lfEnable3D()) {
        return lfGetPostArray($queryString);
    }

    $arrRet = array();
    // 3Dセキュアを使用する場合は正規表現で。
    if (!empty($queryString)) {
        $queryString = trim($queryString);
        $regex = '|^ACSUrl\=(.+?)&PaReq\=(.+?)&MD\=(.+?)$|';
        $ret = preg_match_all($regex, $queryString, $matches);

        gfPrintLog(print_r($matches, true), GMO_LOG_PATH);

        if ($ret !== false && $ret > 0) {
            gfPrintLog('REG STATUS:' . $ret, GMO_LOG_PATH);
            $arrRet['ACSUrl'] = $matches[1][0];
            $arrRet['PaReq']  = $matches[2][0];
            $arrRet['MD']     = $matches[3][0];
        } else {
            gfPrintLog(' STATUS:Failed', GMO_LOG_PATH);
        }
    }

    return $arrRet;
}


/**
 * 3Dベリファイ時のリクエストパラメータを検証する
 *
 * @return array
 */
function lfValidate3dVerify() {

}
/**
 * 3Dベリファイを実行する
 *
 * @return array
 */
function lfSend3dVerify() {
    $arrSendData = array(
        'PaRes' => $_POST['PaRes'],
        'MD'    => $_POST['MD']
    );
    $objReq =& new HTTP_Request(GMO_3D_URL);
    $objReq->setMethod('POST');
    $objReq->addPostDataArray($arrSendData);

    $respBody = '';

    if (!PEAR::isError($objReq->sendRequest())) {
        $respBody = $objReq->getResponseBody();
    }
gfPrintLog('##############POST PARAM###############', GMO_LOG_PATH);
gfPrintLog(print_r($_POST, true), GMO_LOG_PATH);

    return lfGetPostArray($respBody);
}

/**
 * 3Dベリファイの実行結果を判定する
 *
 * @param array $arr3dRet
 * @return boolean|PEAR::ERROR 成功時:true|失敗時:PEAR::ERRORオブジェクト
 */
function lf3dVerifyIsSuccess($arr3dRet) {
    // 通信エラー
    if (empty($arr3dRet)) {
        return PEAR::raiseError('通信エラーが発生しました。');
    }

    // SSL未対応時は
    if (!is_array($arr3dRet)) {
        return PEAR::raiseError('通信エラーが発生しました。');
    }

    // 成功
    if ($arr3dRet['Html'] == "Receipt"
        && empty($arr3dRet['ErrType'])
        && empty($arr3dRet['ErrInfo'])) {

        return true;

    // 失敗
    } else {
        $detail_code01 = substr($arr3dRet['ErrInfo'], 0, 5);
        $detail_code02 = substr($arr3dRet['ErrInfo'], 5, 4);
        $gmo_err_msg = $detail_code01 . "-" . $detail_code02;
        gfPrintLog(print_r($arr3dRet, true), GMO_LOG_PATH);
        return PEAR::raiseError('通信エラーが発生しました。エラーコード：' . $gmo_err_msg);
    }
}

/**
 * 3Dセキュアが有効かどうかを判定する.
 * モバイルはGMO側が未対応のため、UserAgetntがモバイル時には3Dセキュア無効と見なす
 *
 * @return unknown
 */
function lfEnable3D() {
    global $arrGMOConf;

    if (isset($arrGMOConf[0]['gmo_3d'])
        && $arrGMOConf[0]['gmo_3d'] == '1'
        && GC_MobileUserAgent::isMobile() == false) {

        return true;
    }

    return false;
}
?>
