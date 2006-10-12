<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

///////////////////////////////////////////////////////////
/*
	サイトごとに必ず変更する設定
*/
///////////////////////////////////////////////////////////

// DATAディレクトリパス(HTMLディレクトリからの相対パス)
define("DATA_PATH", ROOT_DIR . "../data");

// テンプレートファイル保存先
define("USER_DIR", "html/user_data/");

// テンプレートファイル保存先
define("INCLUDE_DIR", USER_DIR."include/");

// ブロックファイル保存先
define("BLOC_DIR", "html/user_data/include/bloc/");

// ユーザー作成画面のデフォルトPHPファイル
define("USER_DEF_PHP", ROOT_DIR."html/__default.php");

// その他画面のデフォルトページレイアウト
define("DEF_LAYOUT", "products/list.php");

// DBエラーメール送信先
define ("DB_ERROR_MAIL_TO", "error-ml@lockon.co.jp");

// DBエラーメール件名
define ("DB_ERROR_MAIL_SUBJECT", "OS_TEST_ERROR");

if(defined('DB_TYPE') && defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_SERVER') && defined('DB_PORT') && defined('DB_NAME')) {
	// サイト用DB
	define ("DEFAULT_DSN", DB_TYPE . "://" . DB_USER . ":" . DB_PASSWORD . "@" . DB_SERVER . ":" .DB_PORT . "/" . DB_NAME);
}

// 郵便番号専用DB
define ("ZIP_DSN", DEFAULT_DSN);

define ("USER_URL", SITE_URL."user_data/"); // ユーザー作成ページ等 

// 認証用 magic
define ("AUTH_MAGIC", "31eafcbd7a81d7b401a7fdc12bba047c02d1fae6");

?>