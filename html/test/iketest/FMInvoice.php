<?php
##################################################
# Veritrans CVS Merchant Development Kit.
# FMInvoice.php　Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: ベリトランスコンビニゲートウェイへ
#       接続するためのサンプル
#       << ファミリーマート用サンプル >>
##################################################

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
include_once("Cart.php");

# トランザクションインスタンスを作成
$t = new Transaction;

# 設定ファイル cvsgwlib.conf によりインスタンスを初期化
$t->setServer($CONFIG);

# カートインスタンスを作成
$cart = new Cart;

# ログ出力インスタンスを取得
$logger = $t->getLogger();

# ログ出力サンプル
$logger->logprint('DEBUG', '<<< 支払画面(FMInvoice.php)処理開始... >>>');

# ダミー取引IDを作成
# 注意：ショッピングカートなどから取得するようにカスタマイズしてください。
$order_id = "fm-" . $cart->getOrderId();

# ダミー金額を作成
# 注意：ショッピングカートなどから取得するようにカスタマイズしてください。
$amount = $cart->getPrice();

# ダミー支払期限を作成
# 注意：ショッピングカートなどから取得するようにカスタマイズしてください。
$pay_limit = $cart->getPayLimit();

# リクエストパラメータ配列の作成
$params = array(
    REQ_ORDER_ID => $order_id,
    REQ_AMOUNT => $amount,
    REQ_PAY_LIMIT => $pay_limit
);

# URL エンコードを行う
$params_str = $t->URLEncode($ENCODE, $params);

# ページ改ざんを防止するためにハッシュを計算する
$hash = $t->genHash($MERCHANT_ID, $SIG_KEY, $params_str);

$logger->logprint(DBGLVL_DEBUG, ' REQ_ORDER = ' . $params_str);
$logger->logprint(DBGLVL_DEBUG, ' REQ_ORDER_SIG = ' . $hash);

# 画面を出力
?>
<script language="javascript">
<!--
function disableSubmit(form) {
  var elements = form.elements;
  for (var i = 0; i < elements.length; i++) {
    if (elements[i].type == 'submit') {
      elements[i].disabled = true;
    }
  }
}
//-->
</script>
<html>
<head>
  <title>お買い上げありがとうございます。</title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?=$ENCODE?>" />
</head>
<body bgcolor="#FFFFFF" text="#000000">
  <hr />
  <h1>お買い上げ情報</h1>
  <p>
    取引ID:&nbsp;<strong><?=$order_id?></strong>
  </p>
  <p>
    価格:&nbsp;<strong><?=$amount?></strong>&nbsp;円
  </p>
  <p>
    お支払期限:&nbsp;<strong><?=$pay_limit?></strong>
  </p>
  <h3>お客様情報</h3>
  <form action="<?=$PAY_URL_FM?>" method="POST" onsubmit="disableSubmit(this)">
    <table>
      <tr>
        <td>姓：</td>
        <td><input type="text" name="REQ_NAME1" /></td>
      </tr>
      <tr>
        <td>名：</td>
        <td><input type="text" name="REQ_NAME2" /></td>
      </tr>
      <tr>
        <td>氏名（カナ）：</td>
        <td><input type="text" name="REQ_KANA" /></td>
      </tr>
      <tr>
        <td>電話番号：</td>
        <td><input type="text" name="REQ_TEL_NO" /></td>
      </tr>
    </table>
    <input type="hidden" name="REQ_ORDER" value="<?=$params_str?>" />
    <input type="hidden" name="REQ_ORDER_SIG" value="<?=$hash?>" />
    <p><input type="submit" value=" 申込 " /></p>
  </form>
  <hr />
  <h3>必ず下記の事項をWebページのどこかに記載してください。</h3>
  <table>
    <tr>
      <td> コンビニで費用支払後決済を行なったコンビニ店舗で返金を受けれない旨の注意書きを記述してください。</td>
    </tr>
  </table>
</body>
</html>
<?php
# ログ出力サンプル
$logger->logprint('DEBUG', '<<< 支払画面(FMInvoice.php)処理終了. >>>');
?>
