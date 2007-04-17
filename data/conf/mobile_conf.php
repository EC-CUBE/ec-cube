<?php
/**
 * モバイルサイト共有設定ファイル
 */

// モバイルサイト設定ファイルを読み込む。
require_once(dirname(__FILE__) . '/../install_mobile.inc');


//--------------------------------------------------------------------------------------------------------
// conf.php で定義される定数のうち、モバイルサイト用に変更が必要なもの

define('TEMPLATE_DIR', DATA_PATH . 'Smarty/templates/mobile');	// SMARTYテンプレート
define('COMPILE_DIR', DATA_PATH . 'Smarty/templates_c/mobile');	// SMARTYコンパイル
define('IMAGE_TEMP_DIR', PC_HTML_PATH . 'upload/temp_image/');	// 画像一時保存
define('IMAGE_SAVE_DIR', PC_HTML_PATH . 'upload/save_image/');	// 画像保存先
define('IMAGE_TEMP_URL', PC_URL_DIR . 'upload/temp_image/');	// 画像一時保存URL
define('IMAGE_SAVE_URL', PC_URL_DIR . 'upload/save_image/');	// 画像保存先URL


//--------------------------------------------------------------------------------------------------------
// モバイルサイト専用の設定

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
define('MOBILE_ADDITIONAL_MAIL_DOMAINS', 'example.co.jp');

/**
 * 携帯電話向け変換画像保存ディレクトリ
 */
define('MOBILE_IMAGE_DIR', HTML_PATH . 'converted_images');
define('MOBILE_IMAGE_URL', URL_DIR . 'converted_images');


//--------------------------------------------------------------------------------------------------------
// conf.php から残りの設定を読み込む。
// 定数の定義が重複するため、error_reporting のレベルを調整する。

$error_reporting = error_reporting();
error_reporting($error_reporting & ~E_NOTICE);
require_once(DATA_PATH . 'conf/conf.php');
error_reporting($error_reporting);
unset($error_reporting);
?>
