<?php
######################################################
# Veritrans CVS Merchant Development Kit.
# CKSPayment.php Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: ベリトランスコンビニゲートウェイからの
#       レスポンスを処理するサンプル
#       << サークル K サンクス用サンプル >>
######################################################

#-----------------------------------------------
# CVS用のパッケージのパス設定
# 注意：お客様の環境に合わせて設定してください。
#-----------------------------------------------
# マーチャント情報設定ファイルをインクルード
require_once("../../require.php");
require("merchant.ini");

# 決済処理パッケージをインクルード
require_once($PHPLIB_PATH . "Transaction.php");
require_once($PHPLIB_PATH . "Config.php");
require_once($PHPLIB_PATH . "Log.php");
class LC_Page{
	function LC_Page() {
		$this->tpl_title = "ご注文誠にありがとうございました。";
		$this->tpl_mainpage = 'test/iketest/CKSPayment.tpl';
	}
}

$objPage = new LC_Page;
$objView = new SC_SiteView;

# トランザクションインスタンスを作成
$t = new Transaction;

# 設定ファイル cvsgwlib.conf によりインスタンスを初期化
$t->setServer($CONFIG);

# ログ出力インスタンスを取得
$logger = $t->getLogger();

# ログ出力サンプル
$logger->logprint('DEBUG', '<<< 支払結果画面(CKSPayment.php)処理開始... >>>');

# 支払ページからのパラメータを取得
$query = $t->getQuery($ENCODE);

# リクエストパラメータ改竄チェック
$hash = $t->genHash($MERCHANT_ID, $SIG_KEY, $query['REQ_ORDER']);

if(strlen($hash) <= 0 || $hash != $query['REQ_ORDER_SIG']) {
	//署名エラーを表示して終了
	sfDispSiteError(E_SIGN_ERROR);
}

# リクエストパラメータをデコード
$orders = $t->URLDecode($ENCODE, $query['REQ_ORDER']);

#-----------------------------------------------
# リクエスト電文($request)にパラメータをセット↓
#-----------------------------------------------
$request = array(
    # 言語選択
    #REQ_ACCEPT_LANGUAGE => ACCEPT_LANGUAGE_JA,
    # 取引コマンド： entry(登録)
    #REQ_COMMAND => CMD_ENTRY,
    # 取引 ID
    REQ_ORDER_ID => $orders[REQ_ORDER_ID],
    # CVSタイプ(CircleKSunks)
    REQ_CVS_TYPE => "04",
    # 金額
    REQ_AMOUNT => $orders[REQ_AMOUNT],
    # 支払期限
    REQ_PAY_LIMIT => $orders[REQ_PAY_LIMIT],
    # 氏名（注意：ベリトランスコンビニゲートウェイは UTF-8 の文字のみを
    # 受け付けるため、ゲートウェイ接続の前に UTF-8 コードへ変換）
    REQ_NAME1 => $t->jCode($query[REQ_NAME1], ENCODE_UTF8),
    REQ_NAME2 => $t->jCode($query[REQ_NAME2], ENCODE_UTF8),
    # 電話番号
    REQ_TEL_NO => $query[REQ_TEL_NO]
);


#------------------------------------------------
# ベリトランスコンビニゲートウェイに取引を投げる
# 取引結果は result にキーと値のペアで格納される
#------------------------------------------------
$result = $t->doTransaction(CMD_ENTRY, $request);

# レスポンス値を取得する
$MStatus = $result[RES_MSTATUS];
#if (substr_count($MStatus, RES_MSTATUS_SC) != 0) {
if ($MStatus == 'success') {
    $objPage->aux_msg = $t->jCode($result[RES_AUX_MSG], $ENCODE);
}
else {
    $objPage->MErrMsg = $t->jCode($result[RES_MERRMSG], $ENCODE);
    $objPage->MErrLoc = $result[RES_MERRLOC];
}
$objPage->MStatus = $MStatus;
$objPage->action_code = $result[RES_ACTION_CODE];
$objPage->order_id = $result[RES_ORDER_ID];
$objPage->order_ctl_id = $result[RES_ORDER_CTL_ID];
$objPage->txn_version = $result[RES_TXN_VERSION];
$objPage->merch_txn = $result[RES_MERCH_TXN];
$objPage->cust_txn = $result[RES_CUST_TXN];
$objPage->receipt_no = $result[RES_RECEIPT_NO];
$objPage->haraikomi_url = $result[RES_HARAIKOMI_URL];
$objPage->err_code = $result[RES_ERR_CODE];
$objPage->payment_type = $result[RES_PAYMENT_TYPE];
$objPage->ref_code = $result[RES_REF_CODE];

# 金額と支払期限がゲートウェイからのレスポンス電文に含まれないため
# リクエスト電文から取得する
$objPage->amount = $request[REQ_AMOUNT];
$objPage->pay_limit = $request[REQ_PAY_LIMIT];

# サークル K サンクス携帯用 URL 生成
$objPage->mobile_url = preg_replace("/https:\/\/.+?\/JLPcon/",
                           "https://w2.kessai.info/JLM/JLMcon",
                           $objPage->haraikomi_url);
#$result{'mobile-url'} = 


# 支払結果により成功・失敗ページを表示
#if (substr_count($MStatus, RES_MSTATUS_SC) != 0) {
if ($MStatus != 'success') {
	$objPage->tpl_title = "ご注文は受付できませんでした。";
	$objPage->tpl_mainpage = "test/iketest/error.tpl";
}


# ログ出力サンプル
$logger->logprint('DEBUG', '<<< 支払結果画面(CKSPayment.php)処理終了. >>>');

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>
