<?php

///////////////////////////////////////////////////////////
/*
	サイトごとに必ず変更する設定
*/
///////////////////////////////////////////////////////////

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

// サイト用DB
define ("DEFAULT_DSN", "pgsql://" . DB_USER . ":" . DB_PASSWORD . "@" . DB_SERVER . "/" . DB_NAME);
//define ("DEFAULT_DSN", "mysql://" . DB_USER . ":" . DB_PASSWORD . "@" . DB_SERVER . "/" . DB_NAME);

// 郵便番号専用DB
define ("ZIP_DSN", DEFAULT_DSN);

define ("USER_URL", SITE_URL."user_data/");					// ユーザー作成ページ等	
?>