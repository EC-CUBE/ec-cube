<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * アプリケーションの初期設定クラス.
 *
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Initial {

    // {{{ cunstructor

    /**
     * コンストラクタ.
     */
    function SC_Initial() {

        /** EC-CUBEのバージョン */
        define('ECCUBE_VERSION', "2.5.0-dev");
    }

    // }}}
    // {{{ functions

    /**
     * 初期設定を行う.
     *
     * @access protected
     * @return void
     */
    function init() {
        $this->requireInitialConfig();
        $this->defineDSN();
        $this->setErrorReporting();
        $this->defineDirectoryIndex();
        $this->defineErrorType();
        $this->defineConstants();
        $this->phpconfigInit();
        $this->createCacheDir();
        $this->stripslashesDeepGpc();
        $this->resetSuperglobalsRequest();
    }

    /**
     * 初期設定ファイルを読み込み, パスの設定を行う.
     *
     * @access protected
     * @return void
     */
    function requireInitialConfig() {

        define('CONFIG_REALFILE', realpath(dirname(__FILE__)) . '/../config/config.php');
        if (file_exists(CONFIG_REALFILE)) {
            require_once(CONFIG_REALFILE);
        }

        if (defined('ECCUBE_INSTALL')) {
            define('MOBILE_DIR', '');
            define('MOBILE_HTML_REALDIR', HTML_REALDIR . MOBILE_DIR);
            define('MOBILE_HTTP_URL', HTTP_URL . MOBILE_DIR);
            define('MOBILE_HTTPS_URL', HTTPS_URL . MOBILE_DIR);
            define('MOBILE_URLPATH', ROOT_URLPATH . MOBILE_DIR);

            define('SMARTPHONE_DIR', '');
            define('SMARTPHONE_HTML_REALDIR', HTML_REALDIR . SMARTPHONE_DIR);
            define('SMARTPHONE_HTTP_URL', HTTP_URL . SMARTPHONE_DIR);
            define('SMARTPHONE_HTTPS_URL', HTTPS_URL . SMARTPHONE_DIR);
            define('SMARTPHONE_URLPATH', ROOT_URLPATH . SMARTPHONE_DIR);
        }
    }

    /**
     * DSN を定義する.
     *
     * @access protected
     * @return void
     */
    function defineDSN() {
        if(defined('DB_TYPE') && defined('DB_USER') && defined('DB_PASSWORD')
           && defined('DB_SERVER') && defined('DB_PORT') && defined('DB_NAME')) {
            /** サイト用DB */
            define ("DEFAULT_DSN",
                    DB_TYPE . "://" . DB_USER . ":" . DB_PASSWORD . "@"
                    . DB_SERVER . ":" .DB_PORT . "/" . DB_NAME);
        }
    }


    /**
     * エラーレベル設定を行う.
     *
     * ・推奨値
     *   開発時 - E_ALL
     *   運用時 - E_ALL & ~E_NOTICE
     *
     * @access protected
     * @return void
     */
    function setErrorReporting() {
        error_reporting(E_ALL & ~E_NOTICE);
        // PHP 5.3.0対応
        if (error_reporting() > 6143) {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        }
    }

    /**
     * マルチバイト文字列設定を行う.
     *
     * TODO SJIS-win や, eucJP-win への対応
     *
     * @access protected
     * @return void
     */
    function phpconfigInit() {
        ini_set('display_errors', '1');
        ini_set('mbstring.http_input', CHAR_CODE);
        ini_set('mbstring.http_output', CHAR_CODE);
        ini_set('auto_detect_line_endings', 1);
        ini_set('default_charset', CHAR_CODE);
        ini_set('mbstring.internal_encoding', CHAR_CODE);
        ini_set('mbstring.detect_order', 'auto');
        ini_set('mbstring.substitute_character', 'none');

        mb_language('ja'); // mb_internal_encoding() より前に
        // TODO 他に mb_language() している箇所の削除を検討
        // TODO .htaccess の mbstring.language を削除できないか検討

        mb_internal_encoding(CHAR_CODE); // mb_language() より後で
        // TODO 上の「ini_set('mbstring.internal_encoding', CHAR_CODE);」を削除できないか検討
        // TODO .htaccess の mbstring.internal_encoding を削除できないか検討

        ini_set('arg_separator.output', '&');
        
        //ロケールを明示的に設定
        setlocale(LC_ALL, LOCALE);
    }

    /**
     * 定数 DIR_INDEX_URL を設定する.
     *
     * @access protected
     * @return void
     */
    function defineDirectoryIndex() {

        // DirectoryIndex の実ファイル名
        if (!defined('DIR_INDEX_FILE')) {
            define('DIR_INDEX_FILE', 'index.php');
        }

        // DIR_INDEX_FILE にアクセスする時の URL のファイル名部を定義する
        if (USE_FILENAME_DIR_INDEX === true) {
            // ファイル名を使用する
            define('DIR_INDEX_URL', DIR_INDEX_FILE);
        } else {
            // ファイル名を使用しない
            define('DIR_INDEX_URL', '');
        }
    }

    /**
     * 定数を設定する.
     *
     * mtb_constants.php を読み込んで定数を設定する.
     * キャッシュディレクトリに存在しない場合は, 初期データからコピーする.
     *
     * @access protected
     * @return void
     */
    function defineConstants() {

        $errorMessage = "<div style='color: #F00; font-weight: bold; "
            . "background-color: #FEB; text-align: center'>"
            . CACHE_REALDIR
            . " にユーザ書込み権限(777等)を付与して下さい。</div>";

        // 定数を設定
        if (is_file(CACHE_REALDIR . "mtb_constants.php")) {
            require_once(CACHE_REALDIR . "mtb_constants.php");

            // キャッシュが無ければ, 初期データからコピー
        } elseif (is_file(CACHE_REALDIR . "../mtb_constants_init.php")) {

            $mtb_constants = file_get_contents(CACHE_REALDIR . "../mtb_constants_init.php");
            if (is_writable(CACHE_REALDIR)) {
                $handle = fopen(CACHE_REALDIR . "mtb_constants.php", "w");
                if (!$handle) {
                    die($errorMessage);
                }
                if (fwrite($handle, $mtb_constants) === false) {
                    die($errorMessage);
                }
                fclose($handle);

                require_once(CACHE_REALDIR . "mtb_constants.php");
            } else {
                die($errorMessage);
            }
        } else {
            die(CACHE_REALDIR . "../mtb_constants_init.php が存在しません");
        }
    }

    /**
     * 各種キャッシュディレクトリを生成する.
     *
     * Smarty キャッシュディレクトリを生成する.
     *
     * @access protected
     * @return void
     */
    function createCacheDir() {
        if (defined("HTML_REALDIR")) {
            umask(0);
            if (!file_exists(COMPILE_DIR)) {
                mkdir(COMPILE_DIR);
            }

            if (!file_exists(MOBILE_COMPILE_REALDIR)) {
                mkdir(MOBILE_COMPILE_REALDIR);
            }

            if (!file_exists(SMARTPHONE_COMPILE_REALDIR)) {
                mkdir(SMARTPHONE_COMPILE_REALDIR);
            }

            if (!file_exists(COMPILE_ADMIN_REALDIR)) {
                mkdir(COMPILE_ADMIN_REALDIR);
            }
        }
    }

    /**
     * エラー種別を定数定義
     *
     * @access protected
     * @return void
     */
    function defineErrorType() {
        // LC_Page_Error用
        /** 指定商品ページがない */
        define('PRODUCT_NOT_FOUND', 1);
        /** カート内が空 */
        define('CART_EMPTY', 2);
        /** ページ推移エラー */
        define('PAGE_ERROR', 3);
        /** 購入処理中のカート商品追加エラー */
        define('CART_ADD_ERROR', 4);
        /** 他にも購入手続きが行われた場合 */
        define('CANCEL_PURCHASE', 5);
        /** 指定カテゴリページがない */
        define('CATEGORY_NOT_FOUND', 6);
        /** ログインに失敗 */
        define('SITE_LOGIN_ERROR', 7);
        /** 会員専用ページへのアクセスエラー */
        define('CUSTOMER_ERROR', 8);
        /** 購入時の売り切れエラー */
        define('SOLD_OUT', 9);
        /** カート内商品の読込エラー */
        define('CART_NOT_FOUND', 10);
        /** ポイントの不足 */
        define('LACK_POINT', 11);
        /** 仮登録者がログインに失敗 */
        define('TEMP_LOGIN_ERROR', 12);
        /** URLエラー */
        define('URL_ERROR', 13);
        /** ファイル解凍エラー */
        define('EXTRACT_ERROR', 14);
        /** FTPダウンロードエラー */
        define('FTP_DOWNLOAD_ERROR', 15);
        /** FTPログインエラー */
        define('FTP_LOGIN_ERROR', 16);
        /** FTP接続エラー */
        define('FTP_CONNECT_ERROR', 17);
        /** DB作成エラー */
        define('CREATE_DB_ERROR', 18);
        /** DBインポートエラー */
        define('DB_IMPORT_ERROR', 19);
        /** 設定ファイル存在エラー */
        define('FILE_NOT_FOUND', 20);
        /** 書き込みエラー */
        define('WRITE_FILE_ERROR', 21);
        /** DB接続エラー */
        define('DB_CONNECT_ERROR', 22);
        /** フリーメッセージ */
        define('FREE_ERROR_MSG', 999);

        // LC_Page_Error_DispError用
        /** ログイン失敗 */
        define('LOGIN_ERROR', 1);
        /** アクセス失敗（タイムアウト等） */
        define('ACCESS_ERROR', 2);
        /** アクセス権限違反 */
        define('AUTH_ERROR', 3);
        /** 不正な遷移エラー */
        define('INVALID_MOVE_ERRORR', 4);
    }

    /**
     * クォートされた文字列のクォート部分を再帰的に取り除く
     *
     * {@link http://jp2.php.net/manual/ja/function.get-magic-quotes-gpc.php PHP Manual} の記事を参考に実装。
     * $_REQUEST は後続の処理で再構成されるため、本処理では外している。
     * @return void
     */
    function stripslashesDeepGpc() {
        // Strip magic quotes from request data.
        if (get_magic_quotes_gpc()) {
            // Create lamba style unescaping function (for portability)
            $quotes_sybase = strtolower(ini_get('magic_quotes_sybase'));
            $unescape_function = (empty($quotes_sybase) || $quotes_sybase === 'off') ? 'stripslashes($value)' : 'str_replace("\'\'","\'",$value)';
            $stripslashes_deep = create_function('&$value, $fn', '
                if (is_string($value)) {
                    $value = ' . $unescape_function . ';
                } else if (is_array($value)) {
                    foreach ($value as &$v) $fn($v, $fn);
                }
            ');

            // Unescape data
            $stripslashes_deep($_POST, $stripslashes_deep);
            $stripslashes_deep($_GET, $stripslashes_deep);
            $stripslashes_deep($_COOKIE, $stripslashes_deep);
        }
    }

    /**
     * スーパーグローバル変数「$_REQUEST」を再セット
     *
     * variables_order ディレクティブによる差を吸収する。
     *
     * @access protected
     * @return void
     */
    function resetSuperglobalsRequest() {
        $_REQUEST = array_merge($_GET, $_POST);
    }
}
?>
