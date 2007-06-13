<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

///////////////////////////////////////////////////////////
/*
	サイトごとに必ず変更する設定
*/
///////////////////////////////////////////////////////////

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

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// WEB負荷分散設定

define ("MULTI_WEB_SERVER_MODE", false);    // 負荷分散モード(true:ON false:OFF)

/*
 *  host:IPアドレスで指定する。
 *  user:FTPユーザ
 *  pass:FTPパスワード
 */
$arrWEB_SERVERS = array(
    array('host_name' => 'web1', 'host' => '127.0.0.1',   'user' => 'users', 'pass' => 'pass'),        // WEBサーバ1
    array('host_name' => 'web2', 'host' => 'host2',   'user' => 'users', 'pass' => 'pass'),        // WEBサーバ2
);
//////////////////////////////////////////////////////////////////////////////////?///////////////////////////////////////////


?>