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

/**
 * エラーレベル設定
 *
 * ・推奨値
 *   開発時 - E_ALL
 *   運用時 - E_ALL & ~E_NOTICE
 */
error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(E_ALL);

// 定数を設定する
defineConstants();

// Smarty コンパイルディレクトリを生成する.
if (defined("HTML_PATH")) {
    createCacheDir();
}

/**
 * マルチバイト文字列設定
 *
 * TODO SJIS-win や, eucJP-win への対応
 */
ini_set("mbstring.http_input", CHAR_CODE);
ini_set("mbstring.http_output", CHAR_CODE);
ini_set("auto_detect_line_endings", 1);
ini_set("default_charset", CHAR_CODE);
ini_set("mbstring.internal_encoding", CHAR_CODE);
ini_set("mbstring.detect_order", "auto");
ini_set("mbstring.substitute_character", "none");

/** EC-CUBEのバージョン */
define('ECCUBE_VERSION', "2.0");

/**
 * 定数を設定する.
 *
 * 注意: この関数を外部で使用することは推奨しません.
 *
 * mtb_constants.php を読み込んで定数を設定する.
 * キャッシュディレクトリに存在しない場合は, 初期データからコピーする.
 *
 * @access private
 * @return void
 */
function defineConstants() {
    $CONF_PHP_PATH = realpath( dirname( __FILE__) );
    $errorMessage = "<div style='color: #F00; font-weight: bold; "
        . "background-color: #FEB; text-align: center'>"
        . $CONF_PHP_PATH
        . "/cacheにユーザ書込み権限(777等)を付与して下さい。</div>";

    // 定数を設定
    if (is_file($CONF_PHP_PATH . "/cache/mtb_constants.php")) {
        require_once($CONF_PHP_PATH . "/cache/mtb_constants.php");

    // キャッシュが無ければ, 初期データからコピー
    } elseif (is_file($CONF_PHP_PATH
                      . "/mtb_constants_init.php")) {

        $mtb_constants = file_get_contents($CONF_PHP_PATH . "/mtb_constants_init.php");
        if (is_writable($CONF_PHP_PATH . "/cache/")) {
            $handle = fopen($CONF_PHP_PATH . "/cache/mtb_constants.php", "w");
            if (!$handle) {
                die($errorMessage);
            }
            if (fwrite($handle, $mtb_constants) === false) {
                die($errorMessage);
            }
            fclose($handle);

            require_once($CONF_PHP_PATH . "/cache/mtb_constants.php");
        } else {
            die($errorMessage);
        }
    } else {
        die($CONF_PHP_PATH . "/mtb_constants_init.php");
    }
}

/**
 * 各種キャッシュディレクトリを生成する.
 *
 * 注意: この関数を外部で使用することは推奨しません.
 *
 * Smarty キャッシュディレクトリを生成する.
 *
 * @access private
 * @return void
 */
function createCacheDir() {

    if (!file_exists(COMPILE_DIR)) {
        mkdir(COMPILE_DIR);
    }

    if (!file_exists(MOBILE_COMPILE_DIR)) {
        mkdir(MOBILE_COMPILE_DIR);
    }

    if (!file_exists(COMPILE_ADMIN_DIR)) {
        mkdir(COMPILE_ADMIN_DIR);
    }

    if (!file_exists(COMPILE_FTP_DIR)) {
        mkdir(COMPILE_FTP_DIR);
    }
}
?>
