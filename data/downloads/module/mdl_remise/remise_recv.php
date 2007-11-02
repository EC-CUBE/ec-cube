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

// ルミーズカードクレジット決済結果通知処理
lfRemiseCreditResultCheck();

// コンビニ入金チェック
lfRemiseConveniCheck();

//-------------------------------------------------------------------------------------------------------

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
            print(REMISE_PAYMENT_CHARGE_OK);
            exit;
        }
        print("ERROR");
        exit;
    }
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

?>
