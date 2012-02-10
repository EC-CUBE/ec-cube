<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
 * エラーハンドリングのクラス
 *
 * @package Page
 * @version $Id$
 */
class SC_Helper_HandleError {

    /**
     * 処理の読み込みを行う
     *
     * @return void
     */
    static function load() {
        // E_DEPRECATED 定数 (for PHP < 5.3)
        // TODO バージョン互換処理に統合したい。
        if (!defined('E_DEPRECATED')) {
            define('E_DEPRECATED', 8192);
        }

        // エラーレベル設定
        // 開発時は E_ALL を推奨
        error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE & ~E_DEPRECATED);

        if (!defined('SAFE') || SAFE !== true || !defined('INSTALL_FUNCTION') || INSTALL_FUNCTION !== true) {
            // E_WARNING, E_USER_WARNING を捕捉した場合にログを残すためのエラーハンドラ
            set_error_handler(array(__CLASS__, 'handle_warning'));

            // 実質的に PHP 5.2 以降かで処理が分かれる
            if (function_exists('error_get_last')) {
                // E_USER_ERROR を捕捉した場合にエラー画面を表示させるためのエラーハンドラ
                register_shutdown_function(array(__CLASS__, 'handle_error'));
                // 以降の処理では画面へのエラー表示は行なわない
                ini_set('display_errors' , 0);
            } else {
                // エラー捕捉用の出力バッファリング
                ob_start(array(__CLASS__, '_fatal_error_handler'));
                ini_set('display_errors' , 1);
            }
        }
    }

    /**
     * 警告や E_USER_ERROR を捕捉した場合にエラー画面を表示させるエラーハンドラ関数.
     *
     * この関数は, set_error_handler() 関数に登録するための関数である.
     * trigger_error にて E_USER_ERROR が生成されると, エラーログを出力した後,
     * エラー画面を表示させる.
     * E_WARNING, E_USER_WARNING が発生した場合、ログを記録して、true を返す。
     * (エラー画面・エラー文言は表示させない。)
     *
     * @param integer $errno エラーコード
     * @param string $errstr エラーメッセージ
     * @param string $errfile エラーが発生したファイル名
     * @param integer $errline エラーが発生した行番号
     * @return void|boolean E_USER_ERROR が発生した場合は, エラーページへリダイレクト;
     *                      E_WARNING, E_USER_WARNING が発生した場合、true を返す
     */
    static function handle_warning($errno, $errstr, $errfile, $errline) {

        // error_reporting 設定に含まれていないエラーコードは処理しない
        if (!(error_reporting() & $errno)) {
            return;
        }

        $now = date("Y/m/d H:i:s");
        // 本来 realpath() で正規化したいところだが、NULL を返すケースがあるため避けている (#1618)
        $log_file_path = DATA_REALDIR . 'logs/site.log';
        switch ($errno) {
            case E_USER_ERROR:
                $message = $now . " [$errfile:$errline] FATAL Error($errno) $errstr from ". $_SERVER['REMOTE_ADDR'] . "\n";
                error_log($message, 3, $log_file_path);

                self::displaySystemError($errstr);
                exit(1);
                break;

            case E_WARNING:
            case E_USER_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                $message = $now . " [$errfile:$errline] WARNING($errno) $errstr from ". $_SERVER['REMOTE_ADDR'] . "\n";
                error_log($message, 3, $log_file_path);
                return true;
                break;

            default:
        }
    }

    /**
     * エラーを捕捉するための関数. (for PHP < 5.2.0)
     *
     * PHP4 では, try/catch が使用できず, かつ set_error_handler で Fatal Error は
     * 捕捉できないため, ob_start にこの関数を定義し, Fatal Error が発生した場合
     * に出力される HTML 出力を捕捉する.
     * この関数が実行され, エラーが捕捉されると, DEBUG_MODE が無効な場合,
     * エラーページへリダイレクトする.
     *
     * @param string $buffer 出力バッファリングの内容
     * @return string|void エラーが捕捉された場合は, エラーページへリダイレクトする;
     *                     エラーが捕捉されない場合は, 出力バッファリングの内容を返す
     */
    static function &_fatal_error_handler(&$buffer) {
        if (preg_match('/<b>(Fatal) error<\/b>: +(.+) in <b>(.+)<\/b> on line <b>(\d+)<\/b><br \/>/i', $buffer, $matches)) {
            $now = date("Y/m/d H:i:s");
            // 本来 realpath() で正規化したいところだが、NULL を返すケースがあるため避けている (#1618)
            $log_file_path = DATA_REALDIR . 'logs/site.log';
            $message = $now . " [$matches[3]:$matches[4]] FATAL Error: $matches[2] from ". $_SERVER['REMOTE_ADDR'] . "\n";
            error_log($message, 3, $log_file_path);
            if (DEBUG_MODE !== true) {
                $url = HTTP_URL . "error.php";
                if (defined('ADMIN_FUNCTION') && ADMIN_FUNCTION) {
                    $url .= "?admin";
                }
                header("Location: $url");
                exit;
            }
        }
        return $buffer;
    }

    /**
     * エラー捕捉時のエラーハンドラ関数 (for PHP >= 5.2.0)
     *
     * この関数は, register_shutdown_function() 関数に登録するための関数である。
     * PHP 5.1 対応処理との互換運用ため E_USER_ERROR は handle_warning で捕捉する。
     *
     * @return void
     */
    static function handle_error() {
        // 最後のエラーを確実に捉えるため、先頭で呼び出す。
        $arrError = error_get_last();

        $is_error = false;
        if (isset($arrError)) {
             switch ($arrError['type']) {
                 case E_ERROR:
                 case E_PARSE:
                 case E_CORE_ERROR:
                 case E_COMPILE_ERROR:
                      $is_error = true;
                 break;
             }
        }

        if (!$is_error) {
            return;
        }

        $errstr = "[{$arrError[file]}:{$arrError[line]}] FATAL Error({$arrError[type]}) {$arrError[message]}";

        // ログの書き出し
        $now = date("Y/m/d H:i:s");
        // 本来 realpath() で正規化したいところだが、NULL を返すケースがあるため避けている (#1618)
        $log_file_path = DATA_REALDIR . 'logs/site.log';
        error_log($now . " $errstr from ". $_SERVER['REMOTE_ADDR'] . "\n", 3, $log_file_path);

        // エラー画面を表示する
        self::displaySystemError($errstr);
    }

    /**
     * エラー画面を表示する
     *
     * @param string|null $errstr エラーメッセージ
     * @return void
     */
    static function displaySystemError($errstr = null) {
        ob_clean();

        // 絵文字変換・除去フィルターが有効か評価する。
        $loaded_ob_emoji = false;
        $arrObs = ob_get_status(true);
        foreach ($arrObs as $arrOb) {
            if ($arrOb['name'] === 'SC_MobileEmoji::handler') {
                $loaded_ob_emoji = true;
                break;
            }
        }

        // 絵文字変換・除去フィルターが無効で、利用できる場合、有効にする。
        if (!$loaded_ob_emoji && class_exists('SC_MobileEmoji')) {
            ob_start(array('SC_MobileEmoji', 'handler'));
        }

        require_once CLASS_EX_REALDIR . 'page_extends/error/LC_Page_Error_SystemError_Ex.php';
        $objPage = new LC_Page_Error_SystemError_Ex();
        register_shutdown_function(array($objPage, 'destroy'));
        $objPage->init();
        if (isset($errstr)) {
            $objPage->arrDebugMsg[]
                = "▼▼▼ エラーメッセージ ▼▼▼\n"
                . $errstr . "\n"
                . "▲▲▲ エラーメッセージ ▲▲▲\n"
            ;
        }
        $objPage->process();
    }
}
