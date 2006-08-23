<?php
##################################################
# Veritrans CVS Merchant Development Kit.
# SEJInvoice.php　Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: ベリトランスコンビニゲートウェイへ
#       接続するためのサンプル
#       << セブンイレブン用サンプル >>
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
$logger->logprint('DEBUG', '<<< 支払画面(SEJInvoice.php)処理開始... >>>');

# ダミー取引IDを作成
# 注意：ショッピングカートなどから取得するようにカスタマイズしてください。
$order_id = "sj-" . $cart->getOrderId();

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
  <form action="<?=$PAY_URL_SEJ?>" method="POST"
                                   onsubmit="disableSubmit(this)">
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
      <td>株式会社　セブン・イレブン・ジャパンは払込依頼票に記載されている商品等の購入額情報を</td>
    </tr>
    <tr>
      <td>顧客のプライバシーに配慮の上、管理・統計上の目的において個人を特定できない様式にて利用します。</td>
    </tr>
  </table>
  <table>
    <tr>
      <td>１．１　必須の掲載条項 </td>
    </tr>
    <tr>
      <td>（１）株式会社セブン−イレブン・ジャパンがセブン−イレブン店を通じて、顧客の商品等購入代金の代理受領業務を行っていること </td>
    </tr>
    <tr>
      <td>（２）加盟店の住所、商号または名称、ならびに代表者の氏名 </td>
    </tr>
    <tr>
      <td>（３）顧客によるセブン−イレブン店での申込みの取消と商品等の購入またはサービス提供の拒絶に関する条項 </td>
    </tr>
    <tr>
      <td>（４）払込依頼票等によるセブンーイレブン店における支払は日本国内において円貨で行う事 </td>
    </tr>
    <tr>
      <td>（５）商品等の内容、引渡条件、価格、支払および申込みの取消条件その他取引条件 </td>
    </tr>
    <tr>
      <td>（６）商品等についての問い合わせ窓口、連絡先ならびに電子メールアドレス </td>
    </tr>
    <tr>
      <td>（７）商品等に対するクレーム等の連絡先 </td>
    </tr>
    <tr>
      <td>（８）商品等の返品、クーリングオフに必要とされる手続き </td>
    </tr>
    <tr>
      <td>（９）損害賠償責任の制限 </td>
    </tr>
    <tr>
      <td>１．２推奨の掲載条項 </td>
    </tr>
    <tr>
      <td>顧客に対して、以下の件外契約条件をショップのインターネットホームページ上または他のマルチメディアで明示することを推奨するが望ましい。 </td>
    </tr>
    <tr>
      <td>（１）顧客は、極力成人とすること、及び、架空名義、匿名等本人以外の名義による申込みを禁止すること </td>
    </tr>
    <tr>
      <td>（２）件外契約成立の時期 </td>
    </tr>
    <tr>
      <td>（３）顧客の個人情報の登録、利用 </td>
    </tr>
    <tr>
      <td>（４）件外契約が附合契約のため随時変更があることの承認 </td>
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
$logger->logprint('DEBUG', '<<< 支払画面(SEJInvoice.php)処理終了. >>>');
?>
