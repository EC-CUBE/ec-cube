<?php
##################################################
# Veritrans CVS Merchant Development Kit.
# CKSInvoice.php　Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: ベリトランスコンビニゲートウェイへ
#       接続するためのサンプル
#       << サークル K サンクス用サンプル >>
##################################################

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
require_once("Cart.php");


class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = 'test/iketest/CKSInvoice.tpl';
		$this->tpl_title = 'お買い上げありがとうございます';
		global $PAY_URL_CKS;
		$this->PAY_URL_CKS = $PAY_URL_CKS;
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

# トランザクションインスタンスを作成
$t = new Transaction;

# 設定ファイル cvsgwlib.conf によりインスタンスを初期化
$t->setServer($CONFIG);
# ログ出力インスタンスを取得
$logger = $t->getLogger();

# ログ出力サンプル
$logger->logprint('DEBUG', '<<< 支払結果画面(CKSInvoice.php)処理開始... >>>');

# カートインスタンスを作成
$cart = new Cart;

# ダミー取引IDを作成
# 注意：ショッピングカートなどから取得するようにカスタマイズしてください。
$order_id = "ck-" . $cart->getOrderId();

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
$objPage->params_str = $t->URLEncode($ENCODE, $params);

# ページ改ざんを防止するためにハッシュを計算する
$objPage->hash = $t->genHash($MERCHANT_ID, $SIG_KEY, $objPage->params_str);

$objPage->order_id = $order_id;
$objPage->amount = $amount;
$objPage->pay_limit = $pay_limit;

# ログ出力サンプル
$logger->logprint(DBGLVL_DEBUG, ' REQ_ORDER = ' . $objPage->params_str);
$logger->logprint(DBGLVL_DEBUG, ' REQ_ORDER_SIG = ' . $objPage->hash);

$logger->logprint('DEBUG', '<<< 支払画面(CKSInvoice.php)処理終了... >>>');

# 画面を出力
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>
