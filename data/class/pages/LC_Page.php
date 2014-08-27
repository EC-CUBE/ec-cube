<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
 * Web Page を制御する基底クラス
 *
 * Web Page を制御する Page クラスは必ずこのクラスを継承する.
 * PHP4 ではこのような抽象クラスを作っても継承先で何でもできてしまうため、
 * あまり意味がないが、アーキテクトを統一するために作っておく.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page
{
    /** メインテンプレート */
    public $tpl_mainpage;

    /** テンプレートのカラム数 */
    public $tpl_column_num;

    /** メインナンバー */
    public $tpl_mainno;

    /** CSS のパス */
    public $tpl_css;

    /** JavaScript */
    public $tpl_javascript;

    /** タイトル */
    public $tpl_title;

    /** ログインメールアドレス */
    public $tpl_login_email;

    /** HTML ロード後に実行する JavaScript コード */
    public $tpl_onload;

    /** トランザクションID */
    public $transactionid;

    /** メインテンプレート名 */
    public $template = SITE_FRAME;

    /** 店舗基本情報 */
    public $arrSiteInfo;

    /** プラグインを実行フラグ */
    public $plugin_activate_flg = PLUGIN_ACTIVATE_FLAG;

    /** POST に限定する mode */
    public $arrLimitPostMode = array();

    /** ページレイアウトを読み込むか */
    public $skip_load_page_layout = false;

    /** 2.12.x 以前のJavaScript関数を読み込むかどうか */
    public $load_legacy_js = false;

    public $arrForm;
    public $arrErr;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        // 開始時刻を設定する。
        $this->timeStart = microtime(true);

        $this->tpl_authority = $_SESSION['authority'];

        // ディスプレイクラス生成
        $this->objDisplay = new SC_Display_Ex();

        if (!$this->skip_load_page_layout) {
            $layout = new SC_Helper_PageLayout_Ex();
            $layout->sfGetPageLayout($this, false, $_SERVER['SCRIPT_NAME'],
                                     $this->objDisplay->detectDevice());
        }

        // スーパーフックポイントを実行.
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
        $objPlugin->doAction('LC_Page_preProcess', array($this));

        // 店舗基本情報取得
        $this->arrSiteInfo = SC_Helper_DB_Ex::sfGetBasisData();

        // トランザクショントークンの検証と生成
        $this->doValidToken();
        $this->setTokenTo();

        // ローカルフックポイントを実行.
        $this->doLocalHookpointBefore($objPlugin);
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        // POST に限定された mode か検証する。
        $this->checkLimitPostMode();
    }

    /**
     * Page のレスポンス送信.
     *
     * @return void
     */
    public function sendResponse()
    {
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
        // ローカルフックポイントを実行.
        $this->doLocalHookpointAfter($objPlugin);

        // HeadNaviにpluginテンプレートを追加する.
        $objPlugin->setHeadNaviBlocs($this->arrPageLayout['HeadNavi']);

        // スーパーフックポイントを実行.
        $objPlugin->doAction('LC_Page_process', array($this));

        // ページクラス名をテンプレートに渡す
        $arrBacktrace = debug_backtrace();
        if (strlen($this->tpl_page_class_name) === 0) {
            $this->tpl_page_class_name = preg_replace('/_Ex$/', '', $arrBacktrace[1]['class']);
        }

        $this->objDisplay->prepare($this);
        $this->objDisplay->addHeader('Vary', 'User-Agent');
        $this->objDisplay->response->write();
    }

    /**
     * Page のレスポンス送信(ダウンロード).
     *
     * @param string $file_name
     * @return void
     */
    public function sendResponseCSV($file_name, $data)
    {
        $this->objDisplay->prepare($this);
        $this->objDisplay->addHeader('Content-disposition', "attachment; filename=${file_name}");
        $this->objDisplay->addHeader('Content-type', "application/octet-stream; name=${file_name}");
        $this->objDisplay->addHeader('Cache-Control', '');
        $this->objDisplay->addHeader('Pragma', '');

        $this->objDisplay->response->body = $data;
        $this->objDisplay->response->write();
        SC_Response_Ex::actionExit();
    }

    /**
     * デストラクタ
     *
     * ・ブロックの基底クラス (LC_Page_FrontParts_Bloc) では、継承していない。
     * @return void
     */
    public function __destruct()
    {
        // 一定時間以上かかったページの場合、ログ出力する。
        // エラー画面の表示では $this->timeStart が出力されない
        if (defined('PAGE_DISPLAY_TIME_LOG_MODE') && PAGE_DISPLAY_TIME_LOG_MODE == true && isset($this->timeStart)) {
            $timeEnd = microtime(true);
            $timeExecTime = $timeEnd - $this->timeStart;
            if (defined('PAGE_DISPLAY_TIME_LOG_MIN_EXEC_TIME') && $timeExecTime >= (float) PAGE_DISPLAY_TIME_LOG_MIN_EXEC_TIME) {
                $logMsg = sprintf('PAGE_DISPLAY_TIME_LOG [%.2fsec]', $timeExecTime);
                GC_Utils_Ex::gfPrintLog($logMsg);
            }
        }
    }

    /**
     * ローカルフックポイントを生成し、実行します.
     *
     * @param  SC_Helper_Plugin_Ex $objPlugin
     * @return void
     */
    public function doLocalHookpointBefore(SC_Helper_Plugin_Ex $objPlugin)
    {
        // ローカルフックポイントを実行
        $parent_class_name = get_parent_class($this);
        if ($parent_class_name != 'LC_Page') {
            $objPlugin->doAction($parent_class_name . '_action_before', array($this));
        }
        $class_name = get_class($this);
        if ($parent_class_name != 'LC_Page' && $class_name != $parent_class_name) {
            $objPlugin->doAction($class_name . '_action_before', array($this));
        }
    }

    /**
     * ローカルフックポイントを生成し、実行します.
     *
     * @param  SC_Helper_Plugin_Ex $objPlugin
     * @return void
     */
    public function doLocalHookpointAfter(SC_Helper_Plugin_Ex $objPlugin)
    {
        // ローカルフックポイントを実行
        $parent_class_name = get_parent_class($this);
        if ($parent_class_name != 'LC_Page') {
            $objPlugin->doAction($parent_class_name . '_action_after', array($this));
        }
        $class_name = get_class($this);
        if ($parent_class_name != 'LC_Page' && $class_name != $parent_class_name) {
            $objPlugin->doAction($class_name . '_action_after', array($this));
        }
    }

    /**
     * テンプレート取得
     *
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * テンプレート設定(ポップアップなどの場合)
     *
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * $path から URL を取得する.
     *
     * 以下の順序で 引数 $path から URL を取得する.
     * 1. realpath($path) で $path の 絶対パスを取得
     * 2. $_SERVER['DOCUMENT_ROOT'] と一致する文字列を削除
     * 3. $useSSL の値に応じて, HTTP_URL 又は, HTTPS_URL を付与する.
     *
     * 返り値に, QUERY_STRING を含めたい場合は, key => value 形式
     * の配列を $param へ渡す.
     *
     * @access protected
     * @param string $path   結果を取得するためのパス
     * @param array  $param  URL に付与するパラメーターの配列
     * @param mixed  $useSSL 結果に HTTPS_URL を使用する場合 true,
     *                         HTTP_URL を使用する場合 false,
     *                         デフォルト 'escape' 現在のスキーマを使用
     * @return string $path の存在する http(s):// から始まる絶対パス
     * @see Net_URL
     */
    public function getLocation($path, $param = array(), $useSSL = 'escape')
    {
        $rootPath = $this->getRootPath($path);

        // スキーマを定義
        if ($useSSL === true) {
            $url = HTTPS_URL . $rootPath;
        } elseif ($useSSL === false) {
            $url = HTTP_URL . $rootPath;
        } elseif ($useSSL == 'escape') {
            if (SC_Utils_Ex::sfIsHTTPS()) {
                $url = HTTPS_URL . $rootPath;
            } else {
                $url = HTTP_URL . $rootPath;
            }
        } else {
            die("[BUG] Illegal Parametor of \$useSSL ");
        }

        $netURL = new Net_URL($url);
        // QUERY_STRING 生成
        foreach ($param as $key => $val) {
            $netURL->addQueryString($key, $val);
        }

        return $netURL->getURL();
    }

    /**
     * EC-CUBE のWEBルート(/html/)を / としたパスを返す
     *
     * @param  string $path 結果を取得するためのパス
     * @return string EC-CUBE のWEBルート(/html/)からのパス。
     */
    public function getRootPath($path)
    {
        // realpath 関数は、QUERY_STRING を扱えないため、退避する。
        $query_string = '';
        if (preg_match('/^(.+)\\?(.+)$/', $path, $arrMatch)) {
            $path = $arrMatch[1];
            $query_string = $arrMatch[2];
        }

        // Windowsの場合は, ディレクトリの区切り文字を\から/に変換する
        $path = str_replace('\\', '/', $path);
        $htmlPath = str_replace('\\', '/', HTML_REALDIR);

        // PHP 5.1 対策 ( http://xoops.ec-cube.net/modules/newbb/viewtopic.php?topic_id=4277&forum=9)
        if (strlen($path) == 0) {
            $path = '.';
        }

        // $path が / で始まっている場合
        if (substr($path, 0, 1) == '/') {
            $realPath = realpath($htmlPath . substr_replace($path, '', 0, strlen(ROOT_URLPATH)));
        // 相対パスの場合
        } else {
            $realPath = realpath($path);
        }
        if ($realPath === false) {
            trigger_error('realpath でエラー発生。', E_USER_ERROR);
        }
        $realPath = str_replace('\\', '/', $realPath);

        // $path が / で終わっている場合、realpath によって削られた末尾の / を復元する。
        if (substr($path, -1, 1) == '/' && substr($realPath, -1, 1) != '/') {
            $realPath .= '/';
        }

        // HTML_REALDIR を削除した文字列を取得.
        if (substr($realPath, 0, strlen($htmlPath)) !== $htmlPath) {
            trigger_error('不整合', E_USER_ERROR);
        }
        $rootPath = substr($realPath, strlen($htmlPath));

        // QUERY_STRING を復元する。
        if (strlen($query_string) >= 1) {
            $rootPath .= '?' . $query_string;
        }

        return $rootPath;
    }

    /**
     * 互換性確保用メソッド
     *
     * @access protected
     * @return void
     * @deprecated 決済モジュール互換のため
     */
    public function allowClientCache()
    {
        $this->httpCacheControl('private');
    }

    /**
     * クライアント・プロキシのキャッシュを制御する.
     *
     * @access protected
     * @param  string $mode (nocache/private)
     * @return void
     */
    public function httpCacheControl($mode = '')
    {
        switch ($mode) {
            case 'nocache':
                header('Pragma: no-cache');
                header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Last-Modified:');
                break;

            case 'private':
                $cache_expire = session_cache_expire() * 60;
                header('Pragma: no-cache');                                                            // anti-proxy
                header('Expires:');                                                                    // anti-mozilla
                header("Cache-Control: private, max-age={$cache_expire}, pre-check={$cache_expire}");  // HTTP/1.1 client
                header('Last-Modified:');
                break;

            default:
                break;
        }
    }

    /**
     * リクエストパラメーター 'mode' を取得する.
     *
     * 1. $_REQUEST['mode'] の値を取得する.
     * 2. 存在しない場合は null を返す.
     *
     * mode に, 半角英数字とアンダーバー(_) 以外の文字列が検出された場合は null を
     * 返す.
     *
     * @access protected
     * @return string|null $_REQUEST['mode'] の文字列
     */
    public function getMode()
    {
        $pattern = '/^[a-zA-Z0-9_]+$/';
        $mode = null;
        if (isset($_REQUEST['mode']) && preg_match($pattern, $_REQUEST['mode'])) {
            $mode =  $_REQUEST['mode'];
        }

        return $mode;
    }

    /**
     * POST アクセスの妥当性を検証する.
     *
     * 生成されたトランザクショントークンの妥当性を検証し,
     * 不正な場合はエラー画面へ遷移する.
     *
     * この関数は, 基本的に init() 関数で呼び出され, POST アクセスの場合は自動的に
     * トランザクショントークンを検証する.
     * ページによって検証タイミングなどを制御する必要がある場合は, この関数を
     * オーバーライドし, 個別に設定を行うこと.
     *
     * @access protected
     * @param  boolean $is_admin 管理画面でエラー表示をする場合 true
     * @return void
     */
    public function doValidToken($is_admin = false)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!SC_Helper_Session_Ex::isValidToken(false)) {
                if ($is_admin) {
                    SC_Utils_Ex::sfDispError(INVALID_MOVE_ERRORR);
                } else {
                    SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, '', true);
                }
                SC_Response_Ex::actionExit();
            }
        }
    }

    /**
     * トランザクショントークンを取得し, 設定する.
     *
     * @access protected
     * @return void
     */
    public function setTokenTo()
    {
        $this->transactionid = SC_Helper_Session_Ex::getToken();
    }

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 GC_Utils_Ex::gfPrintLog を使用すること
     */
    public function log($mess, $log_level)
    {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        // ログレベル=Debugの場合は、DEBUG_MODEがtrueの場合のみログ出力する
        if ($log_level === 'Debug' && DEBUG_MODE === false) {
            return;
        }

        // ログ出力
        GC_Utils_Ex::gfPrintLog($mess, '', true);
    }

    /**
     * デバック出力を行う.
     *
     * デバック用途のみに使用すること.
     *
     * @access protected
     * @param  mixed $val デバックする要素
     * @return void
     */
    public function p($val)
    {
        SC_Utils_Ex::sfPrintR($val);
    }

    /**
     * POST に限定された mode か検証する。
     *
     * POST 以外で、POST に限定された mode を実行しようとした場合、落とす。
     * @return void
     */
    public function checkLimitPostMode()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && in_array($mode = $this->getMode(), $this->arrLimitPostMode)) {
            $msg = "REQUEST_METHOD=[{$_SERVER['REQUEST_METHOD']}]では実行不能な mode=[$mode] が指定されました。";
            trigger_error($msg, E_USER_ERROR);
        }
    }
}
