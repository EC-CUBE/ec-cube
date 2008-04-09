<?php
/**
 *
 * @copyright   2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version CVS: $Id$
 * @link        http://www.lockon.co.jp/
 *
 */
require_once '../require.php';
require_once MODULE_PATH . "mdl_remise/mdl_remise.inc";

if (REMISE_IP_ADDRESS_DENY == 1) {
    if (!isset($_SERVER["REMOTE_ADDR"]) || !lfIpAddressDenyCheck($_SERVER["REMOTE_ADDR"])) {
        print("NOT REMISE SERVER");
        exit;
    }
}

switch (lfGetMode()) {
case 'credit_complete':
    // ルミーズカードクレジット決済結果通知処理
    lfRemiseCreditResultCheck();
    break;

case 'conveni_mobile_complete':
    // モバイル完了テンプレート
    lfRemiseConveniComplete();
    break;

case 'conveni_check':
    // コンビニ入金チェック
    lfRemiseConveniCheck();
    break;

default:
    break;
}

//-------------------------------------------------------------------------------------------------------
function lfGetMode() {
    $mode = '';
    if (isset($_POST["X-TRANID"]) && isset($_POST["X-PARTOFCARD"])) {
        $mode = 'credit_complete';

    // モバイルコンビニ完了テンプレート
    } elseif (isset($_POST['X-JOB_ID'])) {
        $mode = 'conveni_mobile_complete';

    // コンビニ入金確認
    } elseif (isset($_POST["JOB_ID"]) && isset($_POST["REC_FLG"]) && REMISE_CONVENIENCE_RECIVE == 1) {
        $mode = 'conveni_check';
    }
    return $mode;
}
// ルミーズカードクレジット決済結果通知処理
function lfRemiseCreditResultCheck(){
    $objQuery = new SC_Query;

    $log_path = DATA_PATH . "logs/remise_card_result.log";
    gfPrintLog("remise card result : ".$_POST["X-TRANID"] , $log_path);

    // TRAN_ID を指定されていて、カード情報がある場合
    if (isset($_POST["X-TRANID"]) && isset($_POST["X-PARTOFCARD"])) {

        $errFlg = FALSE;

        gfPrintLog("remise card result start----------", $log_path);
        foreach($_POST as $key => $val){
            gfPrintLog( "\t" . $key . " => " . $val, $log_path);
        }
        gfPrintLog("remise credit result end  ----------", $log_path);

        // IPアドレス制御する場合
        if (REMISE_IP_ADDRESS_DENY == 1) {
            gfPrintLog("remise remoto ip address : ".$_SERVER["REMOTE_HOST"]."-".$_SERVER["REMOTE_ADDR"], $log_path);
            if (!isset($_SERVER["REMOTE_ADDR"]) || !lfIpAddressDenyCheck($_SERVER["REMOTE_ADDR"])) {
                print("NOT REMISE SERVER");
                exit;
            }
        }

        // 請求番号と金額の取得
        $order_id = 0;
        $payment_total = 0;

        if (isset($_POST["X-S_TORIHIKI_NO"])) {
            $order_id = $_POST["X-S_TORIHIKI_NO"];
        }

        if (isset($_POST["X-TOTAL"])) {
            $payment_total = $_POST["X-TOTAL"];
        }

        gfPrintLog("order_id : ".$order_id, $log_path);
        gfPrintLog("payment_total : ".$payment_total, $log_path);

        // 注文データ取得
        $arrTempOrder = $objQuery->getall("SELECT payment_total FROM dtb_order_temp WHERE order_id = ? ", array($order_id));

        // 金額の相違
        if (count($arrTempOrder) > 0) {
            gfPrintLog("ORDER payment_total : ".$arrTempOrder[0]['payment_total'], $log_path);
            if ($arrTempOrder[0]['payment_total'] == $payment_total) {
                $errFlg = TRUE;
            }
        }

        if ($errFlg) {
            // モバイルの場合は、購入完了処理を行う
            $arrCarier = array('imode', 'ezweb', 'jsky');
            if (isset($_POST["CARIER_TYPE"]) && in_array($_POST["CARIER_TYPE"], $arrCarier)) {
                gfPrintLog("Mobile Complete Start", $log_path);
                if (lfMobileComplete()) {
                    gfPrintLog("Mobile Complete Success", $log_path);
                    print(REMISE_PAYMENT_CHARGE_OK_MOBILE);
                } else {
                    gfPrintLog("Mobile Complete Error", $log_path);
                    print("ERROR");
                }
                gfPrintLog("Mobile Complete End", $log_path);
                exit;
            }

            // PC版は購入完了画面で完了するため、ここでは成功コードを返す
            print(REMISE_PAYMENT_CHARGE_OK);
            exit;
        }
        print("ERROR");
        exit;
    }
}

// モバイル完了テンプレート
function lfRemiseConveniComplete() {
    require_once DATA_PATH . 'conf/mobile_conf.php';

    gfPrintLog('remise mobile conveni finish start----------:', REMISE_LOG_PATH_CONVENI_RET);
    foreach($_POST as $key => $val){
        gfPrintLog( "\t" . $key . " => " . $val, REMISE_LOG_PATH_CONVENI_RET);
    }
    gfPrintLog("remise mobile conveni finish end  ----------", REMISE_LOG_PATH_CONVENI_RET);

    $objForm = lfInitParamMobileCompleteConveni();
    // パラメータチェック
    if ($arrErr = $objForm->checkError()) {
        gfPrintLog("Param Invalid", REMISE_LOG_PATH_CONVENI_RET);
        foreach ($arrErr as $k => $v) {
            gfPrintLog("\t$k => $v", REMISE_LOG_PATH_CONVENI_RET);
        }
        mb_http_output(REMISE_SEND_ENCODE);
        sfDispSiteError(FREE_ERROR_MSG, "", true, "購入処理中に以下のエラーが発生しました。<br /><br /><br />・パラメータが不正です。", true);
    }
    $arrForm = $objForm->getHashArray();
    $arrOrderTemp = lfGetOrderTempConveni($arrForm, new SC_Query);
    $arrOrderTemp = $arrOrderTemp[0];

    // 処理結果のエラーチェック
    global $arrRemiseErrorWord;
    gfPrintLog("\terror check", REMISE_LOG_PATH_CONVENI_RET);
    if ($arrForm["X-R_CODE"] !== $arrRemiseErrorWord["OK"]) {
        $err_detail = $arrForm["X-R_CODE"];
        gfPrintLog("\t error check result: $err_detail", REMISE_LOG_PATH_CONVENI_RET);
        mb_http_output(REMISE_SEND_ENCODE);
        sfDispSiteError(FREE_ERROR_MSG, "", true, "購入処理中に以下のエラーが発生しました。<br /><br /><br />・" . $err_detail, true);
    }
    // 金額の整合性チェック
    gfPrintLog("\tpayment total check", REMISE_LOG_PATH_CONVENI_RET);
    if ($arrOrderTemp["payment_total"] != $arrForm["X-TOTAL"]) {
        $xtotal = $arrForm["X-TOTAL"];
        $paytotal = $arrOrderTemp["payment_total"];
        gfPrintLog("\t payment total check result: X-TOTAL($xtotal) != payment_total($paytotal)", REMISE_LOG_PATH_CONVENI_RET);
        mb_http_output(REMISE_SEND_ENCODE);
        sfDispSiteError(FREE_ERROR_MSG, "", true, "購入処理中に以下のエラーが発生しました。<br /><br /><br />・請求金額と支払い金額が違います。", true);
    }

    gfPrintLog("\tdtb_order_temp update...", REMISE_LOG_PATH_CONVENI_RET);
    // ルミーズからの値の取得
    $job_id = lfSetConvMSG("ジョブID(REMISE)", $arrForm["X-JOB_ID"]);
    $payment_limit = lfSetConvMSG("支払い期限", $arrForm["X-PAYDATE"]);
    global $arrConvenience;
    $conveni_type = lfSetConvMSG("支払いコンビニ", $arrConvenience[$arrForm["X-PAY_CSV"]]);
    $payment_total = lfSetConvMSG("合計金額", $arrForm["X-TOTAL"]);
    $receipt_no = lfSetConvMSG("コンビニ払い出し番号", $arrForm["X-PAY_NO1"]);

    // ファミリーマートのみURLがない
    if ($arrForm["X-PAY_CSV"] != "D030") {
        $payment_url = lfSetConvMSG("コンビニ払い出しURL", $arrForm["X-PAY_NO2"]);
    } else {
        $payment_url = lfSetConvMSG("注文番号", $arrForm["X-PAY_NO2"]);
    }

    $arrRet['cv_type'] = $conveni_type;             // コンビニの種類
    $arrRet['cv_payment_url'] = $payment_url;       // 払込票URL(PC)
    $arrRet['cv_receipt_no'] = $receipt_no;         // 払込票番号
    $arrRet['cv_payment_limit'] = $payment_limit;   // 支払い期限
    $arrRet['title'] = lfSetConvMSG("コンビニ決済", true);

    // 決済送信データ作成
    $arrModule['module_id'] = MDL_REMISE_ID;
    $arrModule['payment_total'] = $arrOrderTemp["payment_total"];
    $arrModule['payment_id'] = PAYMENT_CONVENIENCE_ID;

    // ステータスは未入金にする
    $sqlval['status'] = 2;

    // コンビニ決済情報を格納
    $sqlval['conveni_data'] = serialize($arrRet);
    $sqlval['memo01'] = PAYMENT_CONVENIENCE_ID;
    $sqlval['memo02'] = serialize($arrRet);
    $sqlval['memo03'] = MDL_REMISE_ID;
    $sqlval['memo04'] = $arrForm["X-JOB_ID"];
    $sqlval['memo05'] = serialize($arrModule);

    // 受注一時テーブルに更新
    sfRegistTempOrder($arrForm['OPT'], $sqlval);
    gfPrintLog("\tdtb_order_temp update done", REMISE_LOG_PATH_CONVENI_RET);

    gfPrintLog("Mobile Complete Start", REMISE_LOG_PATH_CONVENI_RET);
    if (lfMobileComplete(REMISE_PAY_TYPE_CONVENI)) {
        gfPrintLog("Mobile Complete Success", REMISE_LOG_PATH_CONVENI_RET);
    } else {
        gfPrintLog("Mobile Complete Error", REMISE_LOG_PATH_CONVENI_RET);
        mb_http_output(REMISE_SEND_ENCODE);
        sfDispSiteError(FREE_ERROR_MSG, "", true, "購入処理中にエラーが発生しました。<br>お手数ですがサイト管理者までお問い合わせ下さい", true);
    }
    gfPrintLog("Mobile Complete End", $log_path);


}

// コンビニ入金確認処理
function lfRemiseConveniCheck(){
    $objQuery = new SC_Query;

    $log_path = DATA_PATH . "logs/remise_cv_charge.log";
    gfPrintLog("remise conveni result : ".$_POST["JOB_ID"] , $log_path);

    // 必要なデータが送信されていて、収納通知の自動受信を許可している場合
    if(isset($_POST["JOB_ID"]) && isset($_POST["REC_FLG"]) && REMISE_CONVENIENCE_RECIVE == 1){

        $errFlg = FALSE;

        // 収納済みの場合
        if ($_POST["REC_FLG"] == REMISE_CONVENIENCE_CHARGE) {
            // POSTの内容を全てログ保存
            gfPrintLog("remise conveni charge start----------", $log_path);
            foreach($_POST as $key => $val){
                gfPrintLog( "\t" . $key . " => " . $val, $log_path);
            }
            gfPrintLog("remise conveni charge end  ----------", $log_path);

            // IPアドレス制御する場合
            if (REMISE_IP_ADDRESS_DENY == 1) {
                gfPrintLog("remise remoto ip address : ".$_SERVER["REMOTE_HOST"]."-".$_SERVER["REMOTE_ADDR"], $log_path);
                if (!isset($_SERVER["REMOTE_ADDR"]) || !lfIpAddressDenyCheck($_SERVER["REMOTE_ADDR"])) {
                    print("NOT REMISE SERVER");
                    exit;
                }
            }

            // 請求番号と金額の取得
            $order_id = 0;
            $payment_total = 0;

            if (isset($_POST["S_TORIHIKI_NO"])) {
                $order_id = $_POST["S_TORIHIKI_NO"];
            }

            if (isset($_POST["TOTAL"])) {
                $payment_total = $_POST["TOTAL"];
            }

            gfPrintLog("order_id : ".$order_id, $log_path);
            gfPrintLog("payment_total : ".$payment_total, $log_path);

            // 注文データ取得
            $arrTempOrder = $objQuery->getall("SELECT payment_total FROM dtb_order_temp WHERE order_id = ? ", array($order_id));

            // 金額の相違
            if (count($arrTempOrder) > 0) {
                gfPrintLog("ORDER payment_total : ".$arrTempOrder[0]['payment_total'], $log_path);
                if ($arrTempOrder[0]['payment_total'] == $payment_total) {
                    $errFlg = TRUE;
                }
            }

            // JOB_IDと請求番号。入金金額が一致する場合のみ、ステータスを入金済みに変更する
            if ($errFlg) {
                $sql = "UPDATE dtb_order SET status = 6, update_date = now() ".
                    "WHERE order_id = ? AND memo04 = ? ";
                $objQuery->query($sql, array($order_id, $_POST["JOB_ID"]));

                //応答結果を表示
                print(REMISE_CONVENIENCE_CHARGE_OK);
                exit;
            }
        }
        print("ERROR");
        exit;
    }
}

/**
 * IPアドレス帯域チェック
 * @param $ip IPアドレス
 * @return boolean
 */
function lfIpAddressDenyCheck($ip) {

    // IPアドレス範囲に入ってない場合
    if (ip2long(REMISE_IP_ADDRESS_S) > ip2long($ip) ||
        ip2long(REMISE_IP_ADDRESS_E) < ip2long($ip)) {
        return FALSE;
    }
    return TRUE;
}

/**
 * 商品購入を完了する(モバイル)
 *
 * @param string $type クレジットかコンビニか
 * @return boolean
 */
function lfMobileComplete($type) {
    $logPath = ($type == REMISE_PAY_TYPE_CONVENI)
        ? REMISE_LOG_PATH_CONVENI_RET
        : REMISE_LOG_PATH_CARD_RET;
    $objForm = ($type == REMISE_PAY_TYPE_CONVENI)
        ? lfInitParamMobileCompleteConveni()
        : lfInitParamMobileCompleteCredit();
    $objSiteSess     = new SC_SiteSession();
    $objCartSess     = new SC_CartSession();
    $objCampaignSess = new SC_CampaignSession();
    $objCustomer     = new SC_Customer();
    $objQuery        = new SC_Query();
    $arrInfo         = sf_getBasisData();

    if ($arrErr = $objForm->checkError()) {
        gfPrintLog("\tParam Invalid", $logPath);
        foreach ($arrErr as $k => $v) {
            gfPrintLog("\t$k => $v", $logPath);
        }
        return false;
    }

    $order_id = $objForm->getValue('X-S_TORIHIKI_NO');

    // 受注一時テーブルの取得
    $getOrderTempFucntion = ($type == REMISE_PAY_TYPE_CONVENI)
        ? 'lfGetOrderTempConveni'
        : 'lfUpdateOrderTemp';
    $arrOrderTemp = $getOrderTempFucntion($objForm->getHashArray(), $objQuery);
    if (empty($arrOrderTemp[0])) {
        gfPrintLog("\tOrder Temp Not Found: $order_id", $logPath);
        return false;
    }
    $arrOrderTemp = $arrOrderTemp[0];
    gfPrintLog("\tOrder Temp Found: $order_id", $logPath);

    // セッションの復元
    $_SESSION = unserialize($arrOrderTemp['session']);

    $uniqid = $arrOrderTemp['order_temp_id'];
    $customer_id = $objCustomer->getValue('customer_id');
    $execSetCustomerPurchase = false;
    $preCustomer = false; // 仮会員登録フラグ、trueなら仮会員登録メールを送信する

    gfPrintLog("\tBegin Transaction...", $logPath);
    $objQuery = new SC_Query();
    $objQuery->begin();

    // 会員情報登録処理
    if ($objCustomer->isLoginSuccess()) {
        // 新お届け先の登録
        gfPrintLog("\tlfSetNewAddr() Start.", $logPath);
        lfSetNewAddr($uniqid, $customer_id, $objQuery);
        $execSetCustomerPurchase = true;
    } else {
        //購入時強制会員登録
        switch(PURCHASE_CUSTOMER_REGIST) {
        //無効
        case '0':
            // 購入時会員登録
            if($arrOrderTemp['member_check'] == '1') {
                // 仮会員登録
                gfPrintLog("\t0: lfRegistPreCustomer() Start.", $logPath);
                $customer_id = lfRegistPreCustomer($arrOrderTemp, $arrInfo, $objQuery);
                $execSetCustomerPurchase = true;
                $preCustomer = true;
            }
            break;
        //有効
        case '1':
            // 仮会員登録
            gfPrintLog("\t1: lfRegistPreCustomer() Start.", $logPath);
            $customer_id = lfRegistPreCustomer($arrOrderTemp, $arrInfo, $objQuery);
            $execSetCustomerPurchase = true;
            $preCustomer = true;
            break;
        }
    }

    // 購入集計を顧客テーブルに反映
    gfPrintLog("\tlfSetCustomerPurchase() Start.", $logPath);
    if ($execSetCustomerPurchase && !lfSetCustomerPurchase($customer_id, $arrOrderTemp, $objQuery)) {
        gfPrintLog("\tFailed lfSetCustomerPurchase();", $logPath);
        $objQuery->rollback();
        return false;
    }
    // 一時テーブルを受注テーブルに格納する
    gfPrintLog("\tlfRegistOrder() Start.", $logPath);
    if (!lfRegistOrder($objQuery, $arrOrderTemp)) {
        gfPrintLog("\t" . 'Failed lfRegistOrder();', $logPath);
        $objQuery->rollback();
        return false;
    }
    // カート商品を受注詳細テーブルに格納する
    gfPrintLog("\tlfRegistOrderDetail() Start.", $logPath);
    if (!lfRegistOrderDetail($objQuery, $order_id, $objCartSess)) {
        gfPrintLog("\t" . 'Failed lfRegistOrderDetail();', $logPath);
        $objQuery->rollback();
        return false;
    }
    // 受注一時テーブルの情報を削除する。
    gfPrintLog("\tlfDeleteTempOrder() Start.", $logPath);
    if (!lfDeleteTempOrder($objQuery, $uniqid)) {
        gfPrintLog("\t" . 'Failed lfDeleteTempOrder();', $logPath);
        $objQuery->rollback();
        return false;
    }
    // キャンペーンからの遷移の場合登録する。
    if ($objCampaignSess->getIsCampaign()) {
        gfPrintLog("\tlfRegistCampaignOrder() Start.", $logPath);
        if (!lfRegistCampaignOrder($objQuery, $objCampaignSess, $order_id)) {
            gfPrintLog("\t" . 'Failed lfRegistCampaignOrder();', $logPath);
            $objQuery->rollback();
            return false;
        }
    }

    // sfSendOrderMail(), sfMakeSubject()内で、LC_Pageクラスを使用しているため、ここでLC_Pageクラスを定義する.
    // ここで定義しないとLC_Pageクラスが未定義なのでFatal Errorになる.
    if (!class_exists('LC_Page')) {
        gfPrintLog("\t" . 'define LC_Page Class.', $logPath);
        class LC_Page {}
    }

    gfPrintLog("\t" . 'Send Mail Start.', $logPath);
    lfSendMail($order_id, $preCustomer, $customer_id); // メール送信

    gfPrintLog("\t" . 'Commit Transaction.', $logPath);
    $objQuery->commit();

    gfPrintLog("\t" . 'Success lfMobileComplete();', $logPath);
    return true;
}
/**
 * モバイルクレジット完了用パラメータの初期化
 *
 * @return SC_FormParam
 */
function lfInitParamMobileCompleteCredit() {
/**
X-TRANID => 0802242008300000873041892525 from 211.0.149.169
X-S_TORIHIKI_NO => 50 from 211.0.149.169
X-REFAPPROVED => 2008224 from 211.0.149.169
X-REFFORWARDED => 15250 from 211.0.149.169
X-ERRCODE =>     from 211.0.149.169
X-ERRINFO => 000000000 from 211.0.149.169
X-ERRLEVEL => 0 from 211.0.149.169
X-R_CODE => 0:0000 from 211.0.149.169
CARIER_TYPE => imode from 211.0.149.169
REC_TYPE => RET from 211.0.149.169
X-REFGATEWAYNO => 1 from 211.0.149.169
 X-AMOUNT => 2733 from 211.0.149.169
X-TAX => 0 from 211.0.149.169
X-TOTAL => 2733 from 211.0.149.169
X-PAYQUICKID =>  from 211.0.149.169
X-PARTOFCARD => 1234 from 211.0.149.169
X-EXPIRE => 1122 from 211.0.149.169
X-NAME => LOCKON from 211.0.149.169
*/
    $objForm = new SC_FormParam();
    $objForm->addParam('トランザクションID', 'X-TRANID',        28, '', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
    $objForm->addParam('請求番号',          'X-S_TORIHIKI_NO', 17, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('金額',             'X-AMOUNT',        8, '',  array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('税送料',            'X-TAX',           7, '',  array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('合計金額',          'X-TOTAL',         8, '',  array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('承認番号',          'X-REFAPPROVED',   7, '',  array('EXIST_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('仕向先コード',      'X-REFFORWARDED',  7, '',  array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('エラーコード',      'X-ERRCODE',       3, '',  array('MAX_LENGTH_CHECK'));
    $objForm->addParam('エラー詳細コード',   'X-ERRINFO',        9, '',   array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
    $objForm->addParam('エラーレベル',       'X-ERRLEVEL',      1, '',  array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
    $objForm->addParam('戻りコード',        'X-R_CODE',        6, '',  array('MAX_LENGTH_CHECK'));
    $objForm->addParam('戻り区分',          'REC_TYPE',        3,'',  array('EXIST_CHECK', 'ALNUM_CHECK', 'NUM_COUNT_CHECK'));
    $objForm->addParam('ゲートウェイ番号',   'X-REFGATEWAYNO',  2, '',  array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('ペイクイックID',     'X-PAYQUICKID',    20, '', array('ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('キャリア',          'CARIER_TYPE',      5, '', array('EXIST_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('カード番号',        'X-PARTOFCARD',     4, '', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
    $objForm->addParam('有効期限',          'X-EXPIRE',         4, '', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));

    $objForm->setParam($_POST);
    return $objForm;
}

/**
 * モバイルコンビニ完了用パラメータの初期化
 *
 * @return SC_FormParam
 */
function lfInitParamMobileCompleteConveni() {
    $objForm = new SC_FormParam();
    $objForm->addParam('ジョブID',        'X-JOB_ID',        17, '', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
    $objForm->addParam('請求番号',        'X-S_TORIHIKI_NO', 17, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('戻りコード',      'X-R_CODE',         6, '',  array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('請求金額',        'X-TOTAL',          6, '',  array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('外税分消費税',     'X-TAX',            6, '',  array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('支払期限',        'X-PAYDATE',        8, '',  array('ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('支払い方法コード', 'X-PAY_WAY',        3, '',  array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('コンビニコード',   'X-PAY_CSV',        4, '',  array('EXIST_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('払い出し番号1',    'X-PAY_NO1',        20, '',  array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('払い出し番号2',    'X-PAY_NO2',        120, '',   array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('オプション',       'OPT',              100, '',  array('EXIST_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->addParam('戻り区分',         'REC_TYPE',        3,'',  array('EXIST_CHECK', 'ALNUM_CHECK', 'NUM_COUNT_CHECK'));
    $objForm->addParam('キャリア',         'CARIER_TYPE',      5, '', array('EXIST_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
    $objForm->setParam($_POST);

    return $objForm;
}
/**
 * 受注一時テーブルを更新する
 *
 * @param array $arrForm
 * @param SC_Query $objQuery
 * @return array|null
 */
function lfUpdateOrderTemp($arrForm, $objQuery) {
    $order_id = $arrForm['X-S_TORIHIKI_NO'];

    // POSTデータを保存
    $arrVal["credit_result"] = $arrForm["X-TRANID"];
    $arrVal["memo01"] = PAYMENT_CREDIT_ID;
    $arrVal["memo03"] = MDL_REMISE_ID;
    $arrVal["memo04"] = $arrForm["X-TRANID"];

    // トランザクションコード
    $arrMemo["trans_code"] = array("name"=>"Remiseトランザクションコード", "value" => $arrForm["X-TRANID"]);
    $arrVal["memo02"] = serialize($arrMemo);

    // 決済送信データ作成
    $arrModule['module_id'] = MDL_REMISE_ID;
    $arrModule['payment_total'] = $arrForm["X-TOTAL"];
    $arrModule['payment_id'] = PAYMENT_CREDIT_ID;
    $arrVal['memo05'] = serialize($arrModule);

    $objQuery->update('dtb_order_temp', $arrVal, 'order_id = ?', array($order_id));
    return $objQuery->select('*', 'dtb_order_temp', 'order_id = ? AND del_flg = 0', array($order_id));
}

/**
 * 受注一時テーブルの住所が登録済みテーブルと異なる場合は、別のお届け先に追加する
 *
 * @param string $uniqid
 * @param integer $customer_id
 */
function lfSetNewAddr($uniqid, $customer_id, $objQuery) {
    $diff = false;
    $find_same = false;

    $col = "deliv_name01,deliv_name02,deliv_kana01,deliv_kana02,deliv_tel01,deliv_tel02,deliv_tel03,deliv_zip01,deliv_zip02,deliv_pref,deliv_addr01,deliv_addr02";
    $where = "order_temp_id = ?";
    $arrRet = $objQuery->select($col, "dtb_order_temp", $where, array($uniqid));

    // 要素名のdeliv_を削除する。
    foreach($arrRet[0] as $key => $val) {
        $keyname = ereg_replace("^deliv_", "", $key);
        $arrNew[$keyname] = $val;
    }

    // 会員情報テーブルとの比較
    $col = "name01,name02,kana01,kana02,tel01,tel02,tel03,zip01,zip02,pref,addr01,addr02";
    $where = "customer_id = ?";
    $arrCustomerAddr = $objQuery->select($col, "dtb_customer", $where, array($customer_id));

    // 会員情報の住所と異なる場合
    if($arrNew != $arrCustomerAddr[0]) {
        // 別のお届け先テーブルの住所と比較する
        $col = "name01,name02,kana01,kana02,tel01,tel02,tel03,zip01,zip02,pref,addr01,addr02";
        $where = "customer_id = ?";
        $arrOtherAddr = $objQuery->select($col, "dtb_other_deliv", $where, array($customer_id));

        foreach($arrOtherAddr as $arrval) {
            if($arrNew == $arrval) {
                // すでに同じ住所が登録されている
                $find_same = true;
            }
        }

        if(!$find_same) {
            $diff = true;
        }
    }

    // 新しいお届け先が登録済みのものと異なる場合は別のお届け先テーブルに登録する
    if($diff) {
        $sqlval = $arrNew;
        $sqlval['customer_id'] = $customer_id;
        $objQuery->insert("dtb_other_deliv", $sqlval);
    }
}

/**
 * 購入情報を会員テーブルに登録する
 *
 * @param integer $customer_id
 * @param array $arrData 受注一時テーブル情報
 * @param SC_Query $objQuery
 * @return booean
 */
function lfSetCustomerPurchase($customer_id, $arrData, $objQuery) {
    $col = "first_buy_date, last_buy_date, buy_times, buy_total, point";
    $where = "customer_id = ?";
    $arrRet = $objQuery->select($col, "dtb_customer", $where, array($customer_id));
    $sqlval = $arrRet[0];

    if($sqlval['first_buy_date'] == "") {
        $sqlval['first_buy_date'] = "Now()";
    }
    $sqlval['last_buy_date'] = "Now()";
    $sqlval['buy_times']++;
    $sqlval['buy_total']+= $arrData['total'];
    $sqlval['point'] = ($sqlval['point'] + $arrData['add_point'] - $arrData['use_point']);

    // ポイントが不足している場合
    if($sqlval['point'] < 0) {
        return false;
    }
    $objQuery->update("dtb_customer", $sqlval, $where, array($customer_id));
    return true;
}
/**
 * 会員登録（仮登録）を実行する
 *
 * @param array $arrData 受注一時テーブル情報
 * @param array $arrInfo サイト情報
 * @return integer customer_id 顧客ID
 */
function lfRegistPreCustomer($arrData, $arrInfo, $objQuery) {
    foreach ($arrData as $k => $v) {
        gfPrintLog("$k -> $v",  REMISE_LOG_PATH_CARD_RET);
    }
    // 購入時の会員登録
    $sqlval['name01'] = $arrData['order_name01'];
    $sqlval['name02'] = $arrData['order_name02'];
    $sqlval['kana01'] = $arrData['order_kana01'];
    $sqlval['kana02'] = $arrData['order_kana02'];
    $sqlval['zip01'] = $arrData['order_zip01'];
    $sqlval['zip02'] = $arrData['order_zip02'];
    $sqlval['pref'] = $arrData['order_pref'];
    $sqlval['addr01'] = $arrData['order_addr01'];
    $sqlval['addr02'] = $arrData['order_addr02'];
    $sqlval['email'] = $arrData['order_email'];
    $sqlval['tel01'] = $arrData['order_tel01'];
    $sqlval['tel02'] = $arrData['order_tel02'];
    $sqlval['tel03'] = $arrData['order_tel03'];
    $sqlval['fax01'] = $arrData['order_fax01'];
    $sqlval['fax02'] = $arrData['order_fax02'];
    $sqlval['fax03'] = $arrData['order_fax03'];
    $sqlval['sex'] = $arrData['order_sex'];
    $sqlval['password'] = $arrData['password'];
    $sqlval['reminder'] = $arrData['reminder'];
    $sqlval['reminder_answer'] = $arrData['reminder_answer'];

    // メルマガ配信用フラグの判定
    switch($arrData['mail_flag']) {
    case '1':   // HTMLメール
        $mail_flag = 4;
        break;
    case '2':   // TEXTメール
        $mail_flag = 5;
        break;
    case '3':   // 希望なし
        $mail_flag = 6;
        break;
    default:
        $mail_flag = 6;
        break;
    }
    // メルマガフラグ
    $sqlval['mailmaga_flg'] = $mail_flag;

    // 会員仮登録
    $sqlval['status'] = 1;
    // URL判定用キー
    $sqlval['secret_key'] = sfGetUniqRandomId("t");

    $objQuery = new SC_Query();
    $sqlval['create_date'] = "now()";
    $sqlval['update_date'] = "now()";
    $objQuery->insert("dtb_customer", $sqlval);

    // 顧客IDの取得
    $arrRet = $objQuery->select("customer_id", "dtb_customer", "secret_key = ?", array($sqlval['secret_key']));
    $customer_id = $arrRet[0]['customer_id'];

    return $customer_id;
}
/**
 * 受注テーブルへ登録
 *
 * @param SC_Query $objQuery
 * @param array $arrData
 * @return $order_id
 */
function lfRegistOrder($objQuery, $arrData) {
    $objCampaignSess = new SC_CampaignSession();
    $sqlval = $arrData;

    // 注文ステータス:指定が無ければ新規受付に設定
    if(!isset($sqlval["status"])) {
        $sqlval['status'] = '1';
    }

    // 別のお届け先を指定していない場合、配送先に登録住所をコピーする。
    if(empty($arrData["deliv_check"]) || $arrData["deliv_check"] == "-1") {
        $sqlval['deliv_name01'] = $arrData['order_name01'];
        $sqlval['deliv_name02'] = $arrData['order_name02'];
        $sqlval['deliv_kana01'] = $arrData['order_kana01'];
        $sqlval['deliv_kana02'] = $arrData['order_kana02'];
        $sqlval['deliv_pref'] = $arrData['order_pref'];
        $sqlval['deliv_zip01'] = $arrData['order_zip01'];
        $sqlval['deliv_zip02'] = $arrData['order_zip02'];
        $sqlval['deliv_addr01'] = $arrData['order_addr01'];
        $sqlval['deliv_addr02'] = $arrData['order_addr02'];
        $sqlval['deliv_tel01'] = $arrData['order_tel01'];
        $sqlval['deliv_tel02'] = $arrData['order_tel02'];
        $sqlval['deliv_tel03'] = $arrData['order_tel03'];
    }

    $order_id = $arrData['order_id'];       // オーダーID
    $sqlval['create_date'] = 'now()';       // 受注日

    // キャンペーンID
    if($objCampaignSess->getIsCampaign()) $sqlval['campaign_id'] = $objCampaignSess->getCampaignId();

    // 受注テーブルに書き込まない列を除去
    unset($sqlval['mailmaga_flg']);     // メルマガチェック
    unset($sqlval['deliv_check']);      // 別のお届け先チェック
    unset($sqlval['point_check']);      // ポイント利用チェック
    unset($sqlval['member_check']);     // 購入時会員チェック
    unset($sqlval['password']);         // ログインパスワード
    unset($sqlval['reminder']);         // リマインダー質問
    unset($sqlval['reminder_answer']);  // リマインダー答え
    unset($sqlval['mail_flag']);        // メールフラグ
    unset($sqlval['session']);          // セッション情報

    // INSERTの実行
    $objQuery->insert("dtb_order", $sqlval);

    return true;
}
/**
 * 受注詳細テーブルへ登録
 *
 * @param SC_Query $objQuery
 * @param integer $order_id
 * @param boolean
 */
function lfRegistOrderDetail($objQuery, $order_id, $objCartSess) {
    // カート内情報の取得
    $arrCart = $objCartSess->getCartList();
    $max = count($arrCart);

    // 既に存在する詳細レコードを消しておく。
    $objQuery->delete("dtb_order_detail", "order_id = ?", array($order_id));

    // 規格名一覧
    $arrClassName = sfGetIDValueList("dtb_class", "class_id", "name");
    // 規格分類名一覧
    $arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");

    for ($i = 0; $i < $max; $i++) {
        // 商品規格情報の取得
        $arrData = sfGetProductsClass($arrCart[$i]['id']);

        // 存在する商品のみ表示する。
        if($arrData != "") {
            $sqlval['order_id'] = $order_id;
            $sqlval['product_id'] = $arrCart[$i]['id'][0];
            $sqlval['classcategory_id1'] = $arrCart[$i]['id'][1];
            $sqlval['classcategory_id2'] = $arrCart[$i]['id'][2];
            $sqlval['product_name'] = $arrData['name'];
            $sqlval['product_code'] = $arrData['product_code'];
            $sqlval['classcategory_name1'] = $arrClassCatName[$arrData['classcategory_id1']];
            $sqlval['classcategory_name2'] = $arrClassCatName[$arrData['classcategory_id2']];
            $sqlval['point_rate'] = $arrCart[$i]['point_rate'];
            $sqlval['price'] = $arrCart[$i]['price'];
            $sqlval['quantity'] = $arrCart[$i]['quantity'];
            // 在庫の減少処理
            if (!lfReduceStock($objQuery, $arrCart[$i]['id'], $arrCart[$i]['quantity'])) {
                return false;
            }
            // INSERTの実行
            $objQuery->insert("dtb_order_detail", $sqlval);
        } else {
            return false;
        }
    }
    return true;
}
/**
 * 購入商品の在庫を減らす.
 *
 * @param SC_Query $objQuery
 * @param array $arrID 商品ID
 * @param integer $quantity 商品数
 * @return boolean
 */
function lfReduceStock($objQuery, $arrID, $quantity) {
    $where = "product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?";
    $arrRet = $objQuery->select("stock, stock_unlimited", "dtb_products_class", $where, $arrID);

    // 売り切れエラー
    if(($arrRet[0]['stock_unlimited'] != '1' && $arrRet[0]['stock'] < $quantity) || $quantity == 0) {
        return false;

    // 無制限の場合、在庫はNULL
    } elseif($arrRet[0]['stock_unlimited'] == '1') {
        $sqlval['stock'] = null;
        $objQuery->update("dtb_products_class", $sqlval, $where, $arrID);
    // 在庫を減らす
    } else {
        $sqlval['stock'] = ($arrRet[0]['stock'] - $quantity);
        if($sqlval['stock'] == "") {
            $sqlval['stock'] = '0';
        }
        $objQuery->update("dtb_products_class", $sqlval, $where, $arrID);
    }
    return true;
}
/**
 * キャンペーン受注テーブルへ登録
 *
 * @param SC_Query $objQuery
 * @param SC_CampaignSession $objCampaignSess
 * @param integer $order_id
 */
function lfRegistCampaignOrder($objQuery, $objCampaignSess, $order_id) {

    // 受注データを取得
    $cols = "order_id, campaign_id, customer_id, message, order_name01, order_name02,".
            "order_kana01, order_kana02, order_email, order_tel01, order_tel02, order_tel03,".
            "order_fax01, order_fax02, order_fax03, order_zip01, order_zip02, order_pref, order_addr01,".
            "order_addr02, order_sex, order_birth, order_job, deliv_name01, deliv_name02, deliv_kana01,".
            "deliv_kana02, deliv_tel01, deliv_tel02, deliv_tel03, deliv_fax01, deliv_fax02, deliv_fax03,".
            "deliv_zip01, deliv_zip02, deliv_pref, deliv_addr01, deliv_addr02, payment_total";

    $arrOrder = $objQuery->select($cols, "dtb_order", "order_id = ?", array($order_id));

    $sqlval = $arrOrder[0];
    $sqlval['create_date'] = 'now()';

    // INSERTの実行
    $objQuery->insert("dtb_campaign_order", $sqlval);

    // 申し込み数の更新
    $total_count = $objQuery->get("dtb_campaign", "total_count", "campaign_id = ?", array($sqlval['campaign_id']));
    $arrCampaign['total_count'] = $total_count += 1;
    $objQuery->update("dtb_campaign", $arrCampaign, "campaign_id = ?", array($sqlval['campaign_id']));

    return true;
}
/**
 * 受注一時テーブルの削除
 *
 * @param SC_Query $objQuery
 * @param string $uniqid
 */
function lfDeleteTempOrder($objQuery, $uniqid) {
    $where = "order_temp_id = ?";
    $sqlval['del_flg'] = 1;
    $objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
    return true;
}
/**
 * メール送信処理
 *
 * @param integer $order_id
 * @param boolean $preCustomer
 * @param integer $customer_id
 */
function lfSendMail($order_id, $preCustomer = false, $customer_id = null) {
    $objQuery = new SC_Query;
    $arrOrder = $objQuery->select("*", "dtb_order", "order_id = ?", array($order_id));
    $arrOrder = $arrOrder[0];

    $secret_key = $objQuery->get('dtb_customer', 'secret_key', 'customer_id=?', array($customer_id));

    // モバイル仮登録完了メール送信
    if ($preCustomer && $customer_id) {

        gfPrintLog("\tPre Customer Mail Send.", REMISE_LOG_PATH_CARD_RET);
        $arrInfo = sf_getBasisData();
        $objMailPage = new StdClass();
        $objMailPage->to_name01 = $arrOrder['order_name01'];
        $objMailPage->to_name02 = $arrOrder['order_name02'];
        $objMailPage->CONF = $arrInfo;
        $objMailPage->uniqid = $secret_key;
        $objMailView = new SC_SiteView;
        $objMailView->assignobj($objMailPage);
        $body = $objMailView->fetch("mobile/mail_templates/customer_mail.tpl");

        $objMail = new GC_SendMail();
        $objMail->setItem(
                            ''                                      //　宛先
                            , sfMakeSubject("会員登録のご確認")     //　サブジェクト
                            , $body                                 //　本文
                            , $arrInfo['email03']                   //　配送元アドレス
                            , $arrInfo['shop_name']                 //　配送元　名前
                            , $arrInfo["email03"]                   //　reply_to
                            , $arrInfo["email04"]                   //　return_path
                            , $arrInfo["email04"]                   //  Errors_to
                            , $arrInfo["email01"]                   //  Bcc
                                                            );
        // 宛先の設定
        $name = $arrOrder['order_name01'] . $arrOrder['order_name02'] ." 様";
        $objMail->setTo($arrOrder['order_email'], $name);
        $objMail->sendMail();
    }

    // モバイル購入完了メールを送信する
    sfSendOrderMail($order_id, '2');
}

/**
 * 受注一時データを取得する
 *
 * @param array $arrForm
 * @param SC_Query $objQuery
 * @return array|null
 */
function lfGetOrderTempConveni($arrForm, $objQuery) {
    $order_id = $arrForm['X-S_TORIHIKI_NO'];
    $uniqid   = $arrForm['OPT'];
    $where    = 'order_id = ? AND order_temp_id = ? AND del_flg = 0';
    return $objQuery->select('*', 'dtb_order_temp', $where, array($order_id, $uniqid));
}

function lfSetConvMSG($name, $value){
    return array("name" => $name, "value" => $value);
}
?>
