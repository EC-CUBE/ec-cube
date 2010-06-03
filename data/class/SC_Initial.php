<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
        define('ECCUBE_VERSION', "2.4.2");
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
        $this->defineConstants();
        $this->mbstringInit();
        $this->checkConvertEncodingAll();
        $this->createCacheDir();
    }

    /**
     * 初期設定ファイルを読み込む.
     *
     * @access protected
     * @return void
     */
    function requireInitialConfig() {

        require_once(realpath(dirname( __FILE__)) ."/../install.php");
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
        } else {
            define("DEFAULT_DSN", "pgsql://nobody:password@localhost:5432/eccubedb");
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
    function mbstringInit() {
        ini_set("mbstring.http_input", CHAR_CODE);
        ini_set("mbstring.http_output", CHAR_CODE);
        ini_set("auto_detect_line_endings", 1);
        ini_set("default_charset", CHAR_CODE);
        ini_set("mbstring.internal_encoding", CHAR_CODE);
        ini_set("mbstring.detect_order", "auto");
        ini_set("mbstring.substitute_character", "none");
        //ロケールを明示的に設定
        setlocale(LC_ALL, LOCALE);
    }

    /**
     * 文字エンコーディングをチェックし, CHAR_CODE に変換する.
     *
     * $_GET, $_POST, $_REQUEST の文字エンコーディングをチェックし, CHAR_CODE と
     * 一致しない場合は, CHAR_CODE へ変換する.
     *
     * @access protected
     * @return void
     * @see $this->checkConvertEncoding()
     */
    function checkConvertEncodingAll() {
        $_GET = $this->checkConvertEncoding($_GET);
        $_POST = $this->checkConvertEncoding($_POST);
        $_REQUEST = $this->checkConvertEncoding($_REQUEST);
    }

    /**
     * 配列の要素の文字エンコーディングをチェックし, CHAR_CODE に変換して返す.
     *
     * 引数の配列の要素の文字エンコーディングをチェックし, CHAR_CODE と一致しない
     * 場合は, CHAR_CODE へ変換して返す.
     *
     * 文字エンコーディングの判別は, mb_detect_encoding に依存します.
     *
     * @access private
     * @param array $arrMethod チェック対象の配列
     * @return array 変換後の配列
     * @see mb_detect_encoding()
     * @see mb_convert_encoding()
     */
    function checkConvertEncoding($arrMethod) {
        $arrResult = array();
        foreach ($arrMethod as $key => $val) {
            if (is_array($val)) {
                $arrResult[$key] = $this->checkConvertEncoding($val);
            } else {
                $encoding = mb_detect_encoding($val);
                if ($encoding !== false && $encoding != CHAR_CODE) {
                    $arrResult[$key] = mb_convert_encoding($val, CHAR_CODE, $encoding);
                } else {
                    $arrResult[$key] = $val;
                }
            }
        }
        return $arrResult;
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
            . CACHE_PATH
            . " にユーザ書込み権限(777等)を付与して下さい。</div>";

        // 定数を設定
        if (is_file(CACHE_PATH . "mtb_constants.php")) {
            require_once(CACHE_PATH . "mtb_constants.php");

            // キャッシュが無ければ, 初期データからコピー
        } elseif (is_file(CACHE_PATH . "../mtb_constants_init.php")) {

            $mtb_constants = file_get_contents(CACHE_PATH . "../mtb_constants_init.php");
            if (is_writable(CACHE_PATH)) {
                $handle = fopen(CACHE_PATH . "mtb_constants.php", "w");
                if (!$handle) {
                    die($errorMessage);
                }
                if (fwrite($handle, $mtb_constants) === false) {
                    die($errorMessage);
                }
                fclose($handle);

                require_once(CACHE_PATH . "mtb_constants.php");
            } else {
                die($errorMessage);
            }
        } else {
            die(CACHE_PATH . "../mtb_constants_init.php が存在しません");
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
        if (defined("HTML_PATH")) {
            umask(0);
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
    }
}
?>
