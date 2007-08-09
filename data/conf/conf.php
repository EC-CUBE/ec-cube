<?php
/**
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 */

$CONF_PHP_PATH = realpath( dirname( __FILE__) );
require_once($CONF_PHP_PATH ."/../install.php");
require_once($CONF_PHP_PATH ."/core.php" );

//--------------------------------------------------------------------------------------------------------
/** エラーレベル設定
/*
 *	'E_ERROR'             => 大な実行時エラー。これは、メモリ確保に関する問題のように復帰で きないエラーを示します。スクリプトの実行は中断されます。
 *	'E_WARNING'           => 実行時の警告 (致命的なエラーではない)。スクリプトの実行は中断さ れません
 *	'E_PARSE'             => コンパイル時のパースエラー。パースエラーはパーサでのみ生成されま す。
 *	'E_NOTICE'            => 実行時の警告。エラーを発しうる状況に遭遇したことを示す。 ただし通常のスクリプト実行の場合にもこの警告を発することがありうる。
 *	'E_CORE_ERROR'        => PHPの初期始動時点での致命的なエラー。E_ERRORに 似ているがPHPのコアによって発行される点が違う。
 *	'E_CORE_WARNING'      => （致命的ではない）警告。PHPの初期始動時に発生する。 E_WARNINGに似ているがPHPのコアによって発行される 点が違う。
 *	'E_COMPILE_ERROR'     => コンパイル時の致命的なエラー。E_ERRORに 似ているがZendスクリプティングエンジンによって発行される点が違う。
 *	'E_COMPILE_WARNING'   => コンパイル時の警告（致命的ではない）。E_WARNINGに 似ているがZendスクリプティングエンジンによって発行される点が違う。
 *	'E_USER_ERROR'        => ユーザーによって発行されるエラーメッセージ。E_ERROR に似ているがPHPコード上でtrigger_error()関数を 使用した場合に発行される点が違う。
 *	'E_USER_WARNING'      => ユーザーによって発行される警告メッセージ。E_WARNING に似ているがPHPコード上でtrigger_error()関数を 使用した場合に発行される点が違う。
 *	'E_USER_NOTICE'       => ユーザーによって発行される注意メッセージ。E_NOTICEに に似ているがPHPコード上でtrigger_error()関数を 使用した場合に発行される点が違う。
 *	'E_ALL'               => サポートされる全てのエラーと警告。PHP < 6 では E_STRICT レベルのエラーは除く。
 *	'E_STRICT'            => ※PHP5からサポート 実行時の注意。コードの相互運用性や互換性を維持するために PHP がコードの変更を提案する。
 *	'E_RECOVERABLE_ERROR' => ※PHP5からサポート キャッチできる致命的なエラー。危険なエラーが発生したが、 エンジンが不安定な状態になるほどではないことを表す。 ユーザ定義のハンドラでエラーがキャッチされなかった場合 (set_error_handler() も参照ください) は、 E_ERROR として異常終了する。
 */
//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);

if (is_file($CONF_PHP_PATH . "/cache/mtb_constants.php")) {
    require_once($CONF_PHP_PATH . "/cache/mtb_constants.php");
} else {
    // TODO インストーラで設定する
}


?>
