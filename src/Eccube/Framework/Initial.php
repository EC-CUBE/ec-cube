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

namespace Eccube\Framework;

use Eccube\Application;

/**
 * アプリケーションの初期設定クラス.
 *
 * @author LOCKON CO.,LTD.
 */
class Initial
{
    /**
     * コンストラクタ.
     */
    public function __construct()
    {
        /** EC-CUBEのバージョン */
        define('ECCUBE_VERSION', '3.0.0-dev');
    }

    /**
     * 初期設定を行う.
     *
     * @access protected
     * @return void
     */
    public function init()
    {
        $this->requireInitialConfig();
        $this->defineDSN();                 // requireInitialConfig メソッドより後で実行
        $this->defineDirectoryIndex();
        $this->defineConstants();
        $this->defineParameter();           // defineDirectoryIndex メソッドより後で実行
        $this->complementParameter();       // defineConstants メソッドより後で実行
        $this->phpconfigInit();             // defineConstants メソッドより後で実行
        $this->createCacheDir();            // defineConstants メソッドより後で実行
        $this->stripslashesDeepGpc();
        $this->resetSuperglobalsRequest();  // stripslashesDeepGpc メソッドより後で実行
        $this->setTimezone();               // 本当はエラーハンドラーより先に読みたい気も
        $this->normalizeHostname();         // defineConstants メソッドより後で実行
    }

    /**
     * 初期設定ファイルを読み込み, パスの設定を行う.
     *
     * @access protected
     * @return void
     */
    public function requireInitialConfig()
    {
        define('CONFIG_REALFILE', realpath(dirname(dirname(__FILE__))) . '/../../app/config/eccube/config.php');
        if (file_exists(CONFIG_REALFILE)) {
            require_once CONFIG_REALFILE;
        }
    }

    /**
     * DSN を定義する.
     *
     * @access protected
     * @return void
     * @deprecated 下位互換用
     */
    public function defineDSN()
    {
        if (defined('DB_TYPE') && defined('DB_USER') && defined('DB_PASSWORD')
            && defined('DB_SERVER') && defined('DB_PORT') && defined('DB_NAME')
        ) {
            $dsn = DB_TYPE . '://' . DB_USER . ':' . DB_PASSWORD . '@' . DB_SERVER . ':' . DB_PORT . '/' . DB_NAME;
            /** サイト用DB */
            // ここで生成した DSN は使用せず, \Eccube\Query のコンストラクタでパラメータを設定する.
            define('DEFAULT_DSN', $dsn);
        }
    }

    /**
     * @deprecated
     */
    public function setErrorReporting()
    {
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
    public function phpconfigInit()
    {
        ini_set('html_errors', '1');
        ini_set('mbstring.http_input', CHAR_CODE);
        ini_set('mbstring.http_output', CHAR_CODE);
        ini_set('auto_detect_line_endings', 1);
        ini_set('default_charset', CHAR_CODE);
        ini_set('mbstring.detect_order', 'auto');
        ini_set('mbstring.substitute_character', 'none');
        ini_set('pcre.backtrack_limit', 1000000);

        mb_language('ja'); // mb_internal_encoding() より前に
        // TODO .htaccess の mbstring.language を削除できないか検討

        mb_internal_encoding(CHAR_CODE); // mb_language() より後で

        ini_set('arg_separator.output', '&');

        //ロケールを明示的に設定
        $res = setlocale(LC_ALL, LOCALE);
        if ($res === FALSE) {
            // TODO: Windows上のロケール設定が正常に働かない場合があることに暫定的に対応
            // ''を指定するとApache実行環境の環境変数が使われる
            // See also: http://php.net/manual/ja/function.setlocale.php
            setlocale(LC_ALL, '');
        }

        // #1849 (文字エンコーディングの検出を制御する)
        mb_detect_order(array('UTF-8', 'SJIS-win', 'eucJP-win'));
    }

    /**
     * 定数 DIR_INDEX_PATH を設定する.
     *
     * @access protected
     * @return void
     */
    public function defineDirectoryIndex()
    {
        // DirectoryIndex の実ファイル名
        static::defineIfNotDefined('DIR_INDEX_FILE', 'index.php');

        $useFilenameDirIndex = is_bool(USE_FILENAME_DIR_INDEX)
            ? USE_FILENAME_DIR_INDEX
            : (isset($_SERVER['SERVER_SOFTWARE']) ? substr($_SERVER['SERVER_SOFTWARE'], 0, 13) == 'Microsoft-IIS' : false)
        ;

        // DIR_INDEX_FILE にアクセスする時の URL のファイル名部を定義する
        if ($useFilenameDirIndex === true) {
            // ファイル名を使用する
            define('DIR_INDEX_PATH', DIR_INDEX_FILE);
        } else {
            // ファイル名を使用しない
            define('DIR_INDEX_PATH', '');
        }
    }

    /**
     * パラメータを設定する.
     *
     * mtb_constants.php を読み込んで定数として定義する.
     * キャッシュディレクトリに存在しない場合は, 初期データからコピーする.
     *
     * @access protected
     * @return void
     */
    public function defineParameter()
    {
        $errorMessage
            = '<div style="color: #F00; font-weight: bold; background-color: #FEB; text-align: center">'
            . CACHE_REALDIR
            . ' にユーザ書込み権限(777等)を付与して下さい。</div>';

        // 定数を設定
        if (is_file(CACHE_REALDIR . 'mtb_constants.php')) {
            require_once CACHE_REALDIR . 'mtb_constants.php';

            // キャッシュが無ければ, 初期データからコピー
        } elseif (is_file(CACHE_REALDIR . '../../mtb_constants_init.php')) {
            $mtb_constants = file_get_contents(CACHE_REALDIR . '../../mtb_constants_init.php');
            if (is_writable(CACHE_REALDIR)) {
                $handle = fopen(CACHE_REALDIR . 'mtb_constants.php', 'w');
                if (!$handle) {
                    die($errorMessage);
                }
                if (fwrite($handle, $mtb_constants) === false) {
                    die($errorMessage);
                }
                fclose($handle);

                require_once CACHE_REALDIR . 'mtb_constants.php';
            } else {
                die($errorMessage);
            }
        } else {
            die(CACHE_REALDIR . '../mtb_constants_init.php が存在しません');
        }
    }

    /**
     * パラメーターの補完
     *
     * ソースのみ差し替えたバージョンアップを考慮したもの。
     * static::defineIfNotDefined() で定義することを想定
     *
     * @access protected
     * @return void
     */
    public function complementParameter()
    {
        // 2.13.0 のデータとの互換用
        /** サイトトップ */
        static::defineIfNotDefined('TOP_URL', HTTP_URL . DIR_INDEX_PATH);
        /** カートトップ */
        static::defineIfNotDefined('CART_URL', HTTP_URL . 'cart/' . DIR_INDEX_PATH);

        // 2.13.0 のテンプレートとの互換用
        // @deprecated 2.13.1
        /** サイトトップ */
        static::defineIfNotDefined('TOP_URLPATH', ROOT_URLPATH . DIR_INDEX_PATH);
        /** カートトップ */
        static::defineIfNotDefined('CART_URLPATH', ROOT_URLPATH . 'cart/' . DIR_INDEX_PATH);
    }

    /**
     * 各種キャッシュディレクトリを生成する.
     *
     * Smarty キャッシュディレクトリを生成する.
     *
     * @access protected
     * @return void
     */
    public function createCacheDir()
    {
        if (defined('HTML_REALDIR')) {
            umask(0);
            if (!file_exists(COMPILE_REALDIR)) {
                mkdir(COMPILE_REALDIR);
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
     * 定数定義
     *
     * @access protected
     * @return void
     */
    public function defineConstants()
    {
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
        /** ダウンロードファイル存在エラー */
        define('DOWNFILE_NOT_FOUND', 22);
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

        // オーナーズストア通信関連
        /** オーナーズストア通信ステータス */
        define('OSTORE_STATUS_ERROR', 'ERROR');
        /** オーナーズストア通信ステータス */
        define('OSTORE_STATUS_SUCCESS', 'SUCCESS');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_UNKNOWN', '1000');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_INVALID_PARAM', '1001');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_NO_CUSTOMER', '1002');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_WRONG_URL_PASS', '1003');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_NO_PRODUCTS', '1004');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_NO_DL_DATA', '1005');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_DL_DATA_OPEN', '1006');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_DLLOG_AUTH', '1007');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_C_ADMIN_AUTH', '2001');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_C_HTTP_REQ', '2002');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_C_HTTP_RESP', '2003');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_C_FAILED_JSON_PARSE', '2004');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_C_NO_KEY', '2005');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_C_INVALID_ACCESS', '2006');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_C_INVALID_PARAM', '2007');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_C_AUTOUP_DISABLE', '2008');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_C_PERMISSION', '2009');
        /** オーナーズストア通信エラーコード */
        define('OSTORE_E_C_BATCH_ERR', '2010');

        // プラグイン関連
        /** プラグインの状態：アップロード済み */
        define('PLUGIN_STATUS_UPLOADED', '1');
        /** プラグインの状態：インストール済み */
        define('PLUGIN_STATUS_INSTALLED', '2');
        /** プラグイン有効/無効：有効 */
        define('PLUGIN_ENABLE_TRUE', '1');
        /** プラグイン有効/無効：無効 */
        define('PLUGIN_ENABLE_FALSE', '2');

        // CSV入出力関連
        /** CSV入出力列設定有効無効フラグ: 有効 */
        define('CSV_COLUMN_STATUS_FLG_ENABLE', 1);
        /** CSV入出力列設定有効無効フラグ: 無効 */
        define('CSV_COLUMN_STATUS_FLG_DISABLE', 2);
        /** CSV入出力列設定読み書きフラグ: 読み書き可能 */
        define('CSV_COLUMN_RW_FLG_READ_WRITE', 1);
        /** CSV入出力列設定読み書きフラグ: 読み込みのみ可能 */
        define('CSV_COLUMN_RW_FLG_READ_ONLY', 2);
        /** CSV入出力列設定読み書きフラグ: キー列 */
        define('CSV_COLUMN_RW_FLG_KEY_FIELD', 3);

        // 配置ID
        /** 配置ID: 未使用 */
        define('TARGET_ID_UNUSED', 0);
        /** 配置ID: LeftNavi */
        define('TARGET_ID_LEFT', 1);
        /** 配置ID: MainHead */
        define('TARGET_ID_MAIN_HEAD', 2);
        /** 配置ID: RightNavi */
        define('TARGET_ID_RIGHT', 3);
        /** 配置ID: MainFoot */
        define('TARGET_ID_MAIN_FOOT', 4);
        /** 配置ID: TopNavi */
        define('TARGET_ID_TOP', 5);
        /** 配置ID: BottomNavi */
        define('TARGET_ID_BOTTOM', 6);
        /** 配置ID: HeadNavi */
        define('TARGET_ID_HEAD', 7);
        /** 配置ID: HeadTopNavi */
        define('TARGET_ID_HEAD_TOP', 8);
        /** 配置ID: FooterBottomNavi */
        define('TARGET_ID_FOOTER_BOTTOM', 9);
        /** 配置ID: HeaderInternalNavi */
        define('TARGET_ID_HEADER_INTERNAL', 10);

        // 他
        /** アクセス成功 */
        define('SUCCESS', 0);
        /** 無制限フラグ： 無制限 */
        define('UNLIMITED_FLG_UNLIMITED', '1');
        /** 無制限フラグ： 制限有り */
        define('UNLIMITED_FLG_LIMITED', '0');
    }

    /**
     * クォートされた文字列のクォート部分を再帰的に取り除く.
     *
     * {@link http://jp2.php.net/manual/ja/function.get-magic-quotes-gpc.php PHP Manual} の記事を参考に実装。
     * $_REQUEST は後続の処理で再構成されるため、本処理では外している。
     * この関数は, PHP5以上を対象とし, PHP4 の場合は何もしない.
     *
     * @return void
     */
    public function stripslashesDeepGpc()
    {
        // Strip magic quotes from request data.
        if (get_magic_quotes_gpc()
            && version_compare(PHP_VERSION, '5.0.0', '>=')) {
            // Create lamba style unescaping function (for portability)
            $quotes_sybase = strtolower(ini_get('magic_quotes_sybase'));
            $unescape_function = (empty($quotes_sybase) || $quotes_sybase === 'off') ? 'stripslashes($value)' : 'str_replace("\'\'","\'",$value)';
            $stripslashes_deep = create_function('&$value, $fn', '
                if (is_string($value)) {
                    $value = ' . $unescape_function . ';
                } elseif (is_array($value)) {
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
    public function resetSuperglobalsRequest()
    {
        $_REQUEST = array_merge($_GET, $_POST);
    }

    /**
     * 指定された名前の定数が存在しない場合、指定された値で定義
     *
     * @param  string $name  定数の名前。
     * @param  string  $value 定数の値。
     * @return void
     */
    public function defineIfNotDefined($name, $value = null)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * タイムゾーンを設定
     *
     * @return void
     */
    public function setTimezone()
    {
        date_default_timezone_set('Asia/Tokyo');
    }

    /**
     * ホスト名を正規化する
     *
     * @return void
     */
    public function normalizeHostname()
    {
        if (
            // パラメーター
            !USE_NORMALIZE_HOSTNAME
            // コマンドライン実行の場合
            || !isset($_SERVER['REQUEST_URI'])
            // POSTの場合
            || $_SERVER['REQUEST_METHOD'] === 'POST'
        ) {
            // 処理せず戻る
            return;
        }

        $netUrlRequest = new \Net_URL($_SERVER['REQUEST_URI']);
        // 要求を受けたホスト名
        $request_hostname = $netUrlRequest->host;

        $netUrlCorrect = new \Net_URL(\Eccube\Framework\Util\Utils::sfIsHTTPS() ? HTTPS_URL : HTTP_URL);
        // 設定上のホスト名
        $correct_hostname = $netUrlCorrect->host;

        // ホスト名が不一致の場合
        if ($request_hostname !== $correct_hostname) {
            // ホスト名を書き換え
            $netUrlRequest->host = $correct_hostname;
            // 正しい URL
            $correct_url = $netUrlRequest->getUrl();
            // 警告
            $msg = 'ホスト名不一致を検出。リダイレクト実行。';
            $msg .= '要求値=' . var_export($request_hostname, true) . ' ';
            $msg .= '設定値=' . var_export($correct_hostname, true) . ' ';
            $msg .= 'リダイレクト先=' . var_export($correct_url, true) . ' ';
            trigger_error($msg, E_USER_WARNING);
            // リダイレクト(恒久的)
            Application::alias('eccube.response')->sendHttpStatus(301);
            Application::alias('eccube.response')->sendRedirect($correct_url);
        }
    }
}
