<?php
######################################################
# Veritrans CVS Merchant Development Kit.
# LWPayment.php Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: ベリトランスコンビニゲートウェイからの
#       レスポンスを処理するサンプル
#       << ローソン/セイコーマート用サンプル >>
######################################################

#-----------------------------------------------
# CVS用のパッケージのパス設定
# 注意：お客様の環境に合わせて設定してください。
#-----------------------------------------------
# マーチャント情報設定ファイルをインクルード
include("merchant.ini");

# 決済処理パッケージをインクルード
include_once($PHPLIB_PATH . "Transaction.php");
include_once($PHPLIB_PATH . "Config.php");
include_once($PHPLIB_PATH . "Log.php");

# トランザクションインスタンスを作成
$t = new Transaction;

# 設定ファイル cvsgwlib.conf によりインスタンスを初期化
$t->setServer($CONFIG);

# ログ出力インスタンスを取得
$logger = $t->getLogger();

# ログ出力サンプル
$logger->logprint('DEBUG', '<<< 支払結果画面(LWPayment.php)処理開始... >>>');

# 支払ページからのパラメータを取得
$query = $t->getQuery($ENCODE);

# リクエストパラメータ改竄チェック
$hash = $t->genHash($MERCHANT_ID, $SIG_KEY, $query['REQ_ORDER']);

#if ($hash != $query['REQ_ORDER_SIG']) {
if (strlen($hash) <= 0 || $hash != $query['REQ_ORDER_SIG']) {
    # 署名エラーを表示して終了
?>
<html>
<head>
  <title>電子署名エラー</title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?=$ENCODE?>">
</head>
<body bgcolor="#FFFFFF" text="#000000">
  <p>電子署名エラーが発生しました。</p>
</body>
</html>
<?php
    exit;
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
    # CVSタイプ(ローソン、セイコマート)
    REQ_CVS_TYPE => "02",
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
    $aux_msg = $t->jCode($result[RES_AUX_MSG], $ENCODE);
}
else {
    $MErrMsg = $t->jCode($result[RES_MERRMSG], $ENCODE);
    $MErrLoc = $result[RES_MERRLOC];
}
$action_code = $result[RES_ACTION_CODE];
$order_id = $result[RES_ORDER_ID];
$order_ctl_id = $result[RES_ORDER_CTL_ID];
$txn_version = $result[RES_TXN_VERSION];
$merch_txn = $result[RES_MERCH_TXN];
$cust_txn = $result[RES_CUST_TXN];
$receipt_no = $result[RES_RECEIPT_NO];
$haraikomi_url = $result[RES_HARAIKOMI_URL];
$err_code = $result[RES_ERR_CODE];
$payment_type = $result[RES_PAYMENT_TYPE];
$ref_code = $result[RES_REF_CODE];

# 金額と支払期限がゲートウェイからのレスポンス電文に含まれないため
# リクエスト電文から取得する
$amount = $request[REQ_AMOUNT];
$pay_limit = $request[REQ_PAY_LIMIT];

# 支払結果により成功・失敗ページを表示
#if (substr_count($MStatus, RES_MSTATUS_SC) != 0) {
if ($MStatus == 'success') {
?>
<html>
  <head>
    <title>ご注文誠にありがとうございました。</title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?=$ENCODE?>">
  </head>
  <body bgcolor="#FFFFFF" text="#000000">
    <h1>ご注文誠にありがとうございました。</h1>
    <hr>
    <p>お客様の取引IDは <?=$order_id?> です。</p>
    <p>お支払い受付番号は <?=$receipt_no?> です。</p>
    <p>支払金額は <?=$amount?> 円です。</p>
    <p>支払期限は <?=$pay_limit?> です。</p>
    <p>受付番号を紙などに控えて全国のローソンまたはセイコーマートにてお支払ください。</p>
    <!-- 以下のパラメータは用途によって使ってください
    <hr>
    <p>txn-version  :  <?=$txn_version?></p>
    <p>merch-txn    :  <?=$merch_txn?></p>
    <p>order-ctl-id :  <?=$order_ctl_id?></p>
    <p>MStatus      :  <?=$MStatus?></p>
    <p>MErrMsg      :  <?=$MErrMsg?></p>
    <p>aux-msg      :  <?=$aux_msg?></p>
    <p>receipt-no   :  <?=$receipt_no?></p>
    <p>action-code  :  <?=$action_code?></p>
    <p>ref-code     :  <?=$ref_code?></p>
    <p>MErrLoc      :  <?=$MErrLoc?></p>
    <p>err-code     :  <?=$err_code?></p>
    <p>cust-txn     :  <?=$cust_txn?> </p>
    <p>order-id     :  <?=$order_id?></p>
    <p>amount       :  <?=$amount?></p>
    <p>pay-limit    :  <?=$pay_limit?></p>
    -->
  <hr>
  </body>
</html>
<?php
} else { 
?>
<html>
  <head>
    <title>ご注文は受付できませんでした。</title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?=$ENCODE?>">
  </head>
  <body bgcolor="#FFFFFF" text="#000000">
    <h1>申し訳ございません。</h1>
    <h2>お客様のご注文は受付できませんでした。</h2>
    <hr>
    <blockquote><?=$MErrMsg?></blockquote>
    <p><blockquote>お客様の取引IDは <?=$order_id?> です。</blockquote></p>
    <!-- 以下のパラメータは用途によって使ってください
    <hr>
    <p>txn-version  :  <?=$txn_version?></p>
    <p>merch-txn    :  <?=$merch_txn?></p>
    <p>order-ctl-id :  <?=$order_ctl_id?></p>
    <p>MStatus      :  <?=$MStatus?></p>
    <p>MErrMsg      :  <?=$MErrMsg?></p>
    <p>aux-msg      :  <?=$aux_msg?></p>
    <p>receipt-no   :  <?=receipt_no?></p>
    <p>action-code  :  <?=$action_code?></p>
    <p>ref-code     :  <?=$ref_code?></p>
    <p>MErrLoc      :  <?=$MErrLoc?></p>
    <p>err-code     :  <?=$err_code?></p>
    <p>cust-txn     :  <?=$cust_txn?></p>
    <p>order-id     :  <?=$order_id?></p>
    <p>amount       :  <?=$amount?></p>
    <p>pay-limit    :  <?=$pay_limit?></p>
    -->
  <hr>
  </body>
</html>
<?php
}
# ログ出力サンプル
$logger->logprint('DEBUG', '<<< 支払結果画面(LWPayment.php)処理終了. >>>');
?>
