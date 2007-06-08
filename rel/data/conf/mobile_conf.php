<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 *
 * モバイルサイト共有設定ファイル
 */

//--------------------------------------------------------------------------------------------------------

define('MOBILE_TEMPLATE_DIR', DATA_PATH . 'Smarty/templates/mobile');	// SMARTYテンプレート
define('MOBILE_COMPILE_DIR', DATA_PATH . 'Smarty/templates_c/mobile');	// SMARTYコンパイル

/**
 * モバイルサイトであることを表す定数
 */
define('MOBILE_SITE', true);

/**
 * セッションの存続時間 (秒)
 */
define('MOBILE_SESSION_LIFETIME', 1800);

/**
 * 空メール機能を使用するかどうか
 */
define('MOBILE_USE_KARA_MAIL', false);

/**
 * 空メール受け付けアドレスのユーザー名部分
 */
define('MOBILE_KARA_MAIL_ADDRESS_USER', 'eccube');

/**
 * 空メール受け付けアドレスのユーザー名とコマンドの間の区切り文字
 * qmail の場合は '-'
 */
define('MOBILE_KARA_MAIL_ADDRESS_DELIMITER', '+');

/**
 * 空メール受け付けアドレスのドメイン部分
 */
define('MOBILE_KARA_MAIL_ADDRESS_DOMAIN', 'mobile.ec-cube.net');

/**
 * 携帯のメールアドレスではないが、携帯だとみなすドメインのリスト
 * 任意の数の「,」「 」で区切る。
 */
define('MOBILE_ADDITIONAL_MAIL_DOMAINS', 'rebelt.co.jp, lockon.co.jp');

/**
 * 携帯電話向け変換画像保存ディレクトリ
 */
define('MOBILE_IMAGE_DIR', HTML_PATH . 'upload/mobile_image');
define('MOBILE_IMAGE_URL', URL_DIR . 'upload/mobile_image');

/* URL */
define ('MOBILE_URL_SITE_TOP', MOBILE_URL_DIR . 'index.php');								// サイトトップ
define ("MOBILE_URL_CART_TOP", MOBILE_URL_DIR . "cart/index.php");							// カートトップ
define ("MOBILE_URL_SHOP_TOP", MOBILE_SSL_URL . "shopping/index.php");						// 会員情報入力
define ("MOBILE_URL_SHOP_CONFIRM", MOBILE_URL_DIR . "shopping/confirm.php");				// 購入確認ページ
define ("MOBILE_URL_SHOP_PAYMENT", MOBILE_URL_DIR . "shopping/payment.php");				// お支払い方法選択ページ
define ("MOBILE_DETAIL_P_HTML", MOBILE_URL_DIR . "products/detail.php?product_id=");		// 商品詳細(HTML出力)
define ("MOBILE_URL_SHOP_COMPLETE", MOBILE_URL_DIR . "shopping/complete.php");				// 購入完了画面
define ("MOBILE_URL_SHOP_MODULE", MOBILE_URL_DIR . "shopping/load_payment_module.php");		// モジュール追加用画面

//--------------------------------------------------------------------------------------------------------

?>