<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework\Util;

use Eccube\Application;

/**
 * 各種ユーティリティクラス.
 *
 * このクラスはエラーハンドリング処理でも使用している。
 * よって、このファイルで構文エラーが発生すると、EC-CUBE はエラーを捕捉できない。
 * @package Util
 * @author LOCKON CO.,LTD.
 */
class GcUtils
{
    /**
     * ログファイルに変数の詳細を出力
     *
     * @param  mixed $obj
     * @return void
     */
    public function gfDebugLog($obj)
    {
        if (USE_VERBOSE_LOG === true) {
            $msg = "DEBUG\n"
                 . print_r($obj, true);
            static::gfPrintLog($msg, DEBUG_LOG_REALFILE);
        }
    }

    /**
     * 呼び出し元関数名を返します
     *
     * @param  int    $forLogInfo ログ出力用に利用するかどうか(1:ログ出力用に利用する)
     * @return string 呼び出し元クラス、関数名、行数の文字列表現
     */
    public function gfGetCallerInfo($forLogInfo = true)
    {
        // バックトレースを取得する
        $traces = debug_backtrace(false);
        $bklv = 1;
        if ($forLogInfo === true) {
            $bklv = 3;
            if (($traces[3]['class'] === '\\Ecube\\Page\\AbstractPage' || $traces[3]['class'] === '\\Eccube\\Page\\Admin\\AbstractAdminPage')
                && $traces[3]['function'] === 'log'
            ) {
                $bklv = 4;
            }
        }
        $str = $traces[$bklv]['class'] . '::' . $traces[$bklv]['function'] . '(' . $traces[$bklv - 1]['line'] . ') ';

        return $str;
    }

    /**
     * デバッグ情報として必要な範囲のバックトレースを取得する
     *
     * エラーハンドリングに関わる情報を切り捨てる。
     */
    public function getDebugBacktrace($arrBacktrace = null)
    {
        if (is_null($arrBacktrace)) {
            $arrBacktrace = debug_backtrace(false);
        }
        $arrReturn = array();
        foreach (array_reverse($arrBacktrace) as $arrLine) {
            // 言語レベルの致命的エラー時。発生元の情報はトレースできない。(エラーハンドリング処理のみがトレースされる)
            // 実質的に何も返さない(空配列を返す)意図。
            if (strlen($arrLine['file']) === 0
                && ($arrLine['class'] === '\\Eccube\\Framework\\Helper\\HandleErrorHelper')
                && ($arrLine['function'] === 'handle_error' || $arrLine['function'] === 'handle_warning')
            ) {
                break 1;
            }

            $arrReturn[] = $arrLine;

            // エラーハンドリング処理に引き渡した以降の情報は通常不要なので含めない。
            if (!isset($arrLine['class']) && $arrLine['function'] === 'trigger_error') {
                break 1;
            }
            if (($arrLine['class'] === '\\Eccube\\Framework\\Helper\\HandleErrorHelper')
                && ($arrLine['function'] === 'handle_error' || $arrLine['function'] === 'handle_warning')
            ) {
                break 1;
            }
            if (($arrLine['class'] === '\\Eccube\\Framework\\Util\\Utils')
                && $arrLine['function'] === 'sfDispException'
            ) {
                break 1;
            }
            if (($arrLine['class'] === '\\Eccube\\Framework\\Util\\GcUtils')
                && ($arrLine['function'] === 'gfDebugLog' || $arrLine['function'] === 'gfPrintLog')
            ) {
                break 1;
            }
        }

        return array_reverse($arrReturn);
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0
     */
    public function gfGetLogStr($mess, $log_level = 'Info')
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        // メッセージの前に、ログ出力元関数名とログ出力関数呼び出し部分の行数を付与
        $mess = static::gfGetCallerInfo(true) . $mess;

        // ログレベル=Debugの場合は、[Debug]を先頭に付与する
        if ($log_level === 'Debug') {
            $mess = '[Debug]' . $mess;
        }

        return $mess;
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 static::gfPrintLog を使用すること
     */
    public function gfAdminLog($mess, $log_level = 'Info')
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        // ログレベル=Debugの場合は、DEBUG_MODEがtrueの場合のみログ出力する
        if ($log_level === 'Debug' && DEBUG_MODE === false) {
            return;
        }

        // ログ出力
        static::gfPrintLog($mess, '', true);
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 static::gfPrintLog を使用すること
     */
    public function gfFrontLog($mess, $log_level = 'Info')
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        // ログレベル=Debugの場合は、DEBUG_MODEがtrueの場合のみログ出力する
        if ($log_level === 'Debug' && DEBUG_MODE === false) {
            return;
        }

        // ログ出力
        static::gfPrintLog($mess, '', true);
    }

    /**
     * ログの出力を行う
     *
     * エラー・警告は trigger_error() を経由して利用すること。(補足の出力は例外。)
     * @param string $msg
     * @param string $path
     * @param bool   $verbose 冗長な出力を行うか
     */
    public static function gfPrintLog($msg, $path = '', $verbose = USE_VERBOSE_LOG)
    {
        // 日付の取得
        $today = date('Y/m/d H:i:s');
        // 出力パスの作成

        if (strlen($path) === 0) {
            $path = static::isAdminFunction() ? ADMIN_LOG_REALFILE : LOG_REALFILE;
        }

        $msg = "$today [{$_SERVER['SCRIPT_NAME']}] $msg from {$_SERVER['REMOTE_ADDR']}\n";
        if ($verbose) {
            if (static::isFrontFunction()) {
                $msg .= 'customer_id = ' . $_SESSION['customer']['customer_id'] . "\n";
            }
            if (static::isAdminFunction()) {
                $msg .= 'login_id = ' . $_SESSION['login_id'] . '(' . $_SESSION['authority'] . ')' . '[' . session_id() . ']' . "\n";
            }
            $msg .= static::toStringBacktrace(static::getDebugBacktrace());
        }

        error_log($msg, 3, $path);

        // ログテーション
        static::gfLogRotation(MAX_LOG_QUANTITY, MAX_LOG_SIZE, $path);
    }

    /**
     * ログローテーション機能
     *
     * XXX この類のローテーションは通常 0 開始だが、本実装は 1 開始である。
     * この中でログ出力は行なわないこと。(無限ループの懸念あり)
     * @param  integer $max_log  最大ファイル数
     * @param  integer $max_size 最大サイズ
     * @param  string  $path     ファイルパス
     * @return void
     */
    public function gfLogRotation($max_log, $max_size, $path)
    {
        // ファイルが存在しない場合、終了
        if (!file_exists($path)) return;

        // ファイルが最大サイズを超えていない場合、終了
        if (filesize($path) <= $max_size) return;

        // Windows 版 PHP への対策として明示的に事前削除
        $path_max = "$path.$max_log";
        if (file_exists($path_max)) {
            $res = unlink($path_max);
            // 削除に失敗時した場合、ログローテーションは見送り
            if (!$res) return;
        }

        // アーカイブのインクリメント
        for ($i = $max_log; $i >= 2; $i--) {
            $path_old = "$path." . ($i - 1);
            $path_new = "$path.$i";
            if (file_exists($path_old)) {
                rename($path_old, $path_new);
            }
        }

        // 現在ファイルのアーカイブ
        rename($path, "$path.1");
    }

    /*----------------------------------------------------------------------
     * [名称] gfMakePassword
     * [概要] ランダムパスワード生成（英数字）
     * [引数] パスワードの桁数
     * [戻値] ランダム生成されたパスワード
     * [依存] なし
     * [注釈] -
     *----------------------------------------------------------------------*/
    /**
     * @param integer $pwLength
     */
    public static function gfMakePassword($pwLength)
    {
        // 乱数表のシードを決定
        srand((double) microtime() * 54234853);

        // パスワード文字列の配列を作成
        $character = 'abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ2345679';
        $pw = preg_split('//', $character, 0, PREG_SPLIT_NO_EMPTY);

        $password = '';
        for ($i = 0; $i<$pwLength; $i++) {
            $password .= $pw[array_rand($pw, 1)];
        }

        return $password;
    }

    /*----------------------------------------------------------------------------------------------------------------------
     * [名称] gfMailHeaderAddr
     * [概要] 入力されたメールアドレスをメール関数用の宛先に変換
     * [引数] 「メールアドレス」または「名前<メールアドレス>」、複数アドレス指定時はカンマ区切りで指定する。
     * [戻値] 「メールアドレス」または「JIS_MIMEにコード変換した名前 <メールアドレス>」、複数アドレス指定時はカンマ区切りで返却する。
     * [依存] なし
     * [注釈] -
     *----------------------------------------------------------------------------------------------------------------------*/

    public function gfMailHeaderAddr($str)
    {
        $addrs = explode(',', $str); //アドレスを配列に入れる
        $mailaddrs = array();
        foreach ($addrs as $addr) {
            if (preg_match("/^(.+)<(.+)>$/", $addr, $matches)) {
                //引数が「名前<メールアドレス>」の場合
                $mailaddrs[] = mb_encode_mimeheader(trim($matches[1])).' <'.trim($matches[2]).'>';
            } else {
                //メールアドレスのみの場合
                $mailaddrs[] =  trim($addr);
            }
        }

        return implode(', ', $mailaddrs); //複数アドレスはカンマ区切りにする
    }

    /**
     * バックトレースをテキスト形式で出力する
     *
     * 現状スタックトレースの形で出力している。
     * @param  array  $arrBacktrace バックトレース
     * @return string テキストで表現したバックトレース
     */
    public function toStringBacktrace($arrBacktrace)
    {
        $string = '';

        foreach (array_reverse($arrBacktrace) as $backtrace) {
            if (strlen($backtrace['class']) >= 1) {
                $func = $backtrace['class'] . $backtrace['type'] . $backtrace['function'];
            } else {
                $func = $backtrace['function'];
            }

            $string .= $backtrace['file'] . '(' . $backtrace['line'] . '): ' . $func . "\n";
        }

        return $string;
    }

    /**
     * エラー型から該当する定数名を取得する
     *
     * 該当する定数がない場合、$error_type を返す。
     * @param  integer        $error_type エラー型
     * @return string|integer エラー定数名
     */
    public function getErrorTypeName($error_type)
    {
        $arrDefinedConstants = get_defined_constants(true);

        // PHP の歴史対応
        $arrDefinedCoreConstants = array();
        // PHP >= 5.3.1, PHP == 5.3.0 (not Windows)
        if (isset($arrDefinedConstants['Core'])) {
            $arrDefinedCoreConstants = $arrDefinedConstants['Core'];
        // PHP < 5.3.0
        } elseif (isset($arrDefinedConstants['internal'])) {
            $arrDefinedCoreConstants = $arrDefinedConstants['internal'];
        // PHP == 5.3.0 (Windows)
        } elseif (isset($arrDefinedConstants['mhash'])) {
            $arrDefinedCoreConstants = $arrDefinedConstants['mhash'];
        }

        foreach ($arrDefinedCoreConstants as $constant_name => $constant_value) {
            if (substr($constant_name, 0, 2) === 'E_' && $constant_value == $error_type) {
                return $constant_name;
            }
        }

        return $error_type;
    }

    /**
     * 現在の URL を取得する
     *
     * @return string 現在のURL
     */
    public function getUrl()
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $url = 'https://';
        } else {
            $url = 'http://';
        }

        $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (strlen($_SERVER['QUERY_STRING']) >= 1) {
            $url .= '?' . $_SERVER['QUERY_STRING'];
        }

        return $url;
    }

    /**
     * 管理機能かを判定
     *
     * @return bool 管理機能か
     */
    public function isAdminFunction()
    {
        return defined('ADMIN_FUNCTION') && ADMIN_FUNCTION === true;
    }

    /**
     * フロント機能かを判定
     *
     * @return bool フロント機能か
     */
    public static function isFrontFunction()
    {
        return defined('FRONT_FUNCTION') && FRONT_FUNCTION === true;
    }

    /**
     * インストール機能かを判定
     *
     * @return bool インストール機能か
     */
    public static function isInstallFunction()
    {
        return defined('INSTALL_FUNCTION') && INSTALL_FUNCTION === true;
    }

    /**
     * XML宣言を出力する.
     *
     * XML宣言があると問題が発生する UA は出力しない.
     *
     * @return string XML宣言の文字列
     */
    public function printXMLDeclaration()
    {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        if (!preg_match('/MSIE/', $ua) || preg_match('/MSIE 7/', $ua)) {
            echo '<?xml version="1.0" encoding="' . CHAR_CODE . '"?>' . "\n";
        }
    }
}
