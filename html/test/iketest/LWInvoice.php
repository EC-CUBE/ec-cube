<?php
##################################################
# Veritrans CVS Merchant Development Kit.
# LWInvoice.php　Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: ベリトランスコンビニゲートウェイへ
#       接続するためのサンプル
#       << ローソン/セイコーマート用サンプル >>
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
$logger->logprint('DEBUG', '<<< 支払画面(LWInvoice.php)処理開始... >>>');

# ダミー取引IDを作成
# 注意：ショッピングカートなどから取得するようにカスタマイズしてください。
$order_id = "lw-" . $cart->getOrderId();

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
  <meta http-equiv="Content-Type" content="text/html; charset=<?=$ENCODE?>">
</head>
<body bgcolor="#FFFFFF" text="#000000">
  <hr>
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
  <form action="<?=$PAY_URL_LW?>" method="POST" onsubmit="disableSubmit(this)">
    <table>
      <tr>
        <td>姓：</td>
        <td><input type="text" name="REQ_NAME1"></td>
      </tr>
      <tr>
        <td>名：</td>
        <td><input type="text" name="REQ_NAME2"></td>
      </tr>
      <tr>
        <td>電話番号：</td>
        <td><input type="text" name="REQ_TEL_NO"></td>
      </tr>
    </table>
    <input type="hidden" name="REQ_ORDER" value="<?=$params_str?>" />
    <input type="hidden" name="REQ_ORDER_SIG" value="<?=$hash?>" />
    <p><input type="submit" value=" 申込 ">
  </form>
  <hr>  

  <h3>必ず下記の事項をWebページのどこかに記載してください。</h3>
  <table>
    <tr>
      <td>1.1 収納業務について</td>
    </tr>
    <tr>
      <td>(1) 加盟店の名称</td>
    </tr>
    <tr>
      <td>(2) 加盟店の所在地</td>
    </tr>
    <tr>
      <td>(3) 加盟店の電話番号、メールアドレス</td>
    </tr>
    <tr>
      <td>(4) 加盟店の販売責任者名及び責任者への連絡方法</td>
    </tr>
    <tr>
      <td>(5) 商品の販売価格、税金、送料その他必要とされる料金（以下、これらを総称して商品代金等という）</td>
    </tr>
    <tr>
      <td>(6) 商品の引渡し期間</td>
    </tr>
    <tr>
      <td>(7) 商品代金等の支払時期及び方法</td>
    </tr>
    <tr>
      <td>(8) 商品の返品・取消に関する事項</td>
    </tr>
    <tr>
      <td>(9) 顧客からの送信データ等は乙により安全に保護されている旨の表示</td>
    </tr>
    <tr>
      <td>(10) 上記以外で特定商取引法で定められた事項</td>
    </tr>
    <tr>
      <td>1.2 申込取消・返品・交換について </td>
    </tr>
    <tr>
      <td> 顧客に販売するすべての商品について、最終受領者に引渡されてから加盟店が設定する一定期間に </td>
    </tr>
    <tr>
      <td> おいては顧客からの商品の返品又は交換を受け付けるものとし、その旨を販売時点に加盟店の </td>
    </tr>
    <tr>
      <td> サイト上に明記するものとします。 </td>
    </tr>
    <tr>
      <td> 但し、商品の特性に鑑みて返品又は交換を受け付けない場合はあらかじめ販売時点 </td>
    </tr>
    <tr>
      <td> に加盟店のサイト上にその旨を明記するものとします。 </td>
    </tr>
 </table>
 <table>
   <tr>
     <td> コンビニで費用支払後決済を行なったコンビニ店舗で返金を受けれない旨の注意書きを記述してください。</td>
   </tr>
 </table>
</body>
</html>
<?php
# ログ出力サンプル
$logger->logprint('DEBUG', '<<< 支払画面(LWInvoice.php)処理終了. >>>');
?>
