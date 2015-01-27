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
use Eccube\Framework\Display;
use Eccube\Framework\SessionFactory;
use Eccube\Framework\Helper\PluginHelper;
use Eccube\Framework\Helper\SessionHelper;
use Eccube\Framework\Util\Utils;

/**
 * HttpResponse を扱うクラス.
 *
 * @author Ryuichi Tokugami
 */
class Response
{
    /**
     * コンテンツタイプ
     * Enter description here ...
     * @var unknown_type
     */
    public $contentType;
    public $body;
    public $statusCode;
    public $header = array();

    /**
     *
     * Enter description here ...
     */
    public $encoding;

    /**
     * レスポンス出力を書き込む.
     */
    public function write()
    {
        $this->sendHeader();
        echo $this->body;
    }

    public function sendHeader()
    {
        // HTTPのヘッダ
        foreach ($this->header as $name => $head) {
            header($name.': '.$head);
        }
        if (strlen($this->statusCode) >= 1) {
            $this->sendHttpStatus($this->statusCode);
        }
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->header['Content-Type'] = $contentType;
    }

    public function setResposeBody($body)
    {
        $this->body = $body;
    }

    public function addHeader($name, $value)
    {
        $this->header[$name] = $value;
    }

    public function containsHeader($name)
    {
        return isset($this->header[$name]);
    }

    /**
     * アプリケーションのexit処理をする。以降の出力は基本的に停止する。
     * 各クラス内では、exit を直接呼び出さない。
     */
    public function actionExit()
    {
        // ローカルフックポイント処理
        $objPlugin = PluginHelper::getSingletonInstance();

        if (is_object($objPlugin)) {
            $arrBacktrace = debug_backtrace();
            if (is_object($arrBacktrace[0]['object'])) {
                $parent_class_name = get_parent_class($arrBacktrace[0]['object']);
                $objPlugin->doAction($parent_class_name . '_action_' . $arrBacktrace[0]['object']->getMode(), array($arrBacktrace[0]['object']));
                $class_name = get_class($arrBacktrace[0]['object']);
                if ($class_name != $parent_class_name) {
                    $objPlugin->doAction($class_name . '_action_' . $arrBacktrace[0]['object']->getMode(), array($arrBacktrace[0]['object']));
                }
            }
        }

        exit;
        // デストラクタが実行される。
    }

    /**
     * アプリケーション内でリダイレクトする
     *
     * 内部で生成する URL の searchpart は、下記の順で上書きしていく。(後勝ち)
     * 1. 引数 $inheritQueryString が true の場合、$_SERVER['QUERY_STRING']
     * 2. $location に含まれる searchpart
     * 3. 引数 $arrQueryString
     * @param  string    $location           「url-path」「現在のURLからのパス」「URL」のいずれか。「../」の解釈は行なわない。
     * @param  array     $arrQueryString     URL に付加する searchpart
     * @param  bool      $inheritQueryString 現在のリクエストの searchpart を継承するか
     * @param  bool|null $useSsl             true:HTTPSを強制, false:HTTPを強制, null:継承
     * @return void
     * @static
     */
    public function sendRedirect($location, $arrQueryString = array(), $inheritQueryString = false, $useSsl = null)
    {
        // ローカルフックポイント処理
        $objPlugin = PluginHelper::getSingletonInstance();

        if (is_object($objPlugin)) {
            $arrBacktrace = debug_backtrace();
            if (is_object($arrBacktrace[0]['object']) && method_exists($arrBacktrace[0]['object'], 'getMode')) {
                $parent_class_name = get_parent_class($arrBacktrace[0]['object']);
                $objPlugin->doAction($parent_class_name . '_action_' . $arrBacktrace[0]['object']->getMode(), array($arrBacktrace[0]['object']));
                $class_name = get_class($arrBacktrace[0]['object']);
                if ($class_name != $parent_class_name) {
                    $objPlugin->doAction($class_name . '_action_' . $arrBacktrace[0]['object']->getMode(), array($this));
                }
            } elseif (is_object($arrBacktrace[0]['object'])) {
                $pattern = '/^[a-zA-Z0-9_]+$/';
                $mode = null;
                if (isset($_GET['mode']) && preg_match($pattern, $_GET['mode'])) {
                    $mode =  $_GET['mode'];
                } elseif (isset($_POST['mode']) && preg_match($pattern, $_POST['mode'])) {
                    $mode = $_POST['mode'];
                }
                $parent_class_name = get_parent_class($arrBacktrace[0]['object']);
                $objPlugin->doAction($parent_class_name . '_action_' . $mode, array($arrBacktrace[0]['object']));
                $class_name = get_class($arrBacktrace[0]['object']);
                if ($class_name != $parent_class_name) {
                    $objPlugin->doAction($class_name . '_action_' . $mode, array($this));
                }
            }
        }

        // url-path → URL 変換
        if ($location[0] === '/') {
            $netUrl = new \Net_URL($location);
            $location = $netUrl->getUrl();
        }

        // URL の場合
        if (preg_match('/^https?:/', $location)) {
            $url = $location;
            if (is_bool($useSsl)) {
                if ($useSsl) {
                    $pattern = '/^' . preg_quote(HTTP_URL, '/') . '(.*)/';
                    $replacement = HTTPS_URL . '\1';
                    $url = preg_replace($pattern, $replacement, $url);
                } else {
                    $pattern = '/^' . preg_quote(HTTPS_URL, '/') . '(.*)/';
                    $replacement = HTTP_URL . '\1';
                    $url = preg_replace($pattern, $replacement, $url);
                }
            }
        // 現在のURLからのパス
        } else {
            if (!is_bool($useSsl)) {
                $useSsl = Utils::sfIsHTTPS();
            }
            $netUrl = new \Net_URL($useSsl ? HTTPS_URL : HTTP_URL);
            $netUrl->path = dirname($_SERVER['SCRIPT_NAME']) . '/' . $location;
            $url = $netUrl->getUrl();
        }

        $pattern = '/^(' . preg_quote(HTTP_URL, '/') . '|' . preg_quote(HTTPS_URL, '/') . ')/';

        // アプリケーション外へのリダイレクトは扱わない
        if (preg_match($pattern, $url) === 0) {
            trigger_error('', E_USER_ERROR);
        }

        $netUrl = new \Net_URL($url);

        if ($inheritQueryString && !empty($_SERVER['QUERY_STRING'])) {
            $arrQueryStringBackup = $netUrl->querystring;
            // XXX メソッド名は add で始まるが、実際には置換を行う
            $netUrl->addRawQueryString($_SERVER['QUERY_STRING']);
            $netUrl->querystring = array_merge($netUrl->querystring, $arrQueryStringBackup);
        }

        $netUrl->querystring = array_merge($netUrl->querystring, $arrQueryString);

        $session = SessionFactory::getInstance();
        if ((Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE)
            || ($session->useCookie() == false)
        ) {
            $netUrl->addQueryString(session_name(), session_id());
        }

        $netUrl->addQueryString(TRANSACTION_ID_NAME, SessionHelper::getToken());
        $url = $netUrl->getURL();

        header("Location: $url");
        exit;
    }

    /**
     * /html/ からのパスを指定してリダイレクトする
     *
     * FIXME メソッド名を分かりやすくしたい。現状だと、引数が「url-path より後」とも「url-path」とも読み取れる。(前者が意図したいところ)
     * @param  string $location /html/ からのパス。先頭に / を含むかは任意。「../」の解釈は行なわない。
     * @return void
     * @static
     */
    public function sendRedirectFromUrlPath($location, $arrQueryString = array(), $inheritQueryString = false, $useSsl = null)
    {
        $location = ROOT_URLPATH . ltrim($location, '/');
        $this->sendRedirect($location, $arrQueryString, $inheritQueryString, $useSsl);
    }

    /**
     * @static
     */
    public function reload($arrQueryString = array(), $removeQueryString = false)
    {
        // 現在の URL を取得
        $netUrl = new \Net_URL($_SERVER['REQUEST_URI']);

        if (!$removeQueryString) {
            $arrQueryString = array_merge($netUrl->querystring, $arrQueryString);
        }
        $netUrl->querystring = array();

        $this->sendRedirect($netUrl->getURL(), $arrQueryString);
    }

    public function setHeader($headers)
    {
        $this->header = $headers;
    }

    public function setStatusCode($statusCode = null)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * HTTPステータスコードを送出する。
     *
     * @param  integer $statusCode HTTPステータスコード
     * @return void
     * @author Seasoft (新規作成)
     * @see Moony_Action::status() (オリジナル)
     * @link http://moony.googlecode.com/ (オリジナル)
     * @author YAMAOKA Hiroyuki (オリジナル)
     * @copyright 2005-2008 YAMAOKA Hiroyuki (オリジナル)
     * @license http://opensource.org/licenses/bsd-license.php New BSD License (オリジナル)
     * @link http://ja.wikipedia.org/wiki/HTTP%E3%82%B9%E3%83%86%E3%83%BC%E3%82%BF%E3%82%B9%E3%82%B3%E3%83%BC%E3%83%89 (邦訳)
     * @license http://www.gnu.org/licenses/fdl.html GFDL (邦訳)
     * @static
     */
    public function sendHttpStatus($statusCode)
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'];
        $httpVersion = (strpos($protocol, '1.1') !== false) ? '1.1' : '1.0';
        $messages = array(
            // Informational 1xx                        // 【情報】
            100 => 'Continue',                          // 継続
            101 => 'Switching Protocols',               // プロトコル切替え
            // Success 2xx                              // 【成功】
            200 => 'OK',                                // OK
            201 => 'Created',                           // 作成
            202 => 'Accepted',                          // 受理
            203 => 'Non-Authoritative Information',     // 信頼できない情報
            204 => 'No Content',                        // 内容なし
            205 => 'Reset Content',                     // 内容のリセット
            206 => 'Partial Content',                   // 部分的内容
            // Redirection 3xx                          // 【リダイレクション】
            300 => 'Multiple Choices',                  // 複数の選択
            301 => 'Moved Permanently',                 // 恒久的に移動した
            302 => 'Found',  // 1.1                     // 発見した (リクエストしたリソースは一時的に移動されているときに返される)
            303 => 'See Other',                         // 他を参照せよ
            304 => 'Not Modified',                      // 未更新
            305 => 'Use Proxy',                         // プロキシを使用せよ
            // 306 is no longer used but still reserved // 将来のために予約されている
            307 => 'Temporary Redirect',                // 一時的リダイレクト
            // Client Error 4xx                         // 【クライアントエラー】
            400 => 'Bad Request',                       // リクエストが不正である
            401 => 'Unauthorized',                      // 認証が必要である
            402 => 'Payment Required',                  // 支払いが必要である
            403 => 'Forbidden',                         // 禁止されている
            404 => 'Not Found',                         // 未検出
            405 => 'Method Not Allowed',                // 許可されていないメソッド
            406 => 'Not Acceptable',                    // 受理できない
            407 => 'Proxy Authentication Required',     // プロキシ認証が必要である
            408 => 'Request Timeout',                   // リクエストタイムアウト
            409 => 'Conflict',                          // 矛盾
            410 => 'Gone',                              // 消滅した
            411 => 'Length Required',                   // 長さが必要
            412 => 'Precondition Failed',               // 前提条件で失敗した
            413 => 'Request Entity Too Large',          // リクエストエンティティが大きすぎる
            414 => 'Request-URI Too Long',              // リクエストURIが大きすぎる
            415 => 'Unsupported Media Type',            // サポートしていないメディアタイプ
            416 => 'Requested Range Not Satisfiable',   // リクエストしたレンジは範囲外にある
            417 => 'Expectation Failed',                // 期待するヘッダに失敗
            // Server Error 5xx                         // 【サーバーエラー】
            500 => 'Internal Server Error',             // サーバー内部エラー
            501 => 'Not Implemented',                   // 実装されていない
            502 => 'Bad Gateway',                       // 不正なゲートウェイ
            503 => 'Service Unavailable',               // サービス利用不可
            504 => 'Gateway Timeout',                   // ゲートウェイタイムアウト
            505 => 'HTTP Version Not Supported',        // サポートしていないHTTPバージョン
            509 => 'Bandwidth Limit Exceeded'           // 帯域幅制限超過
        );
        if (isset($messages[$statusCode])) {
            if ($httpVersion !== '1.1') {
                // HTTP/1.0
                $messages[302] = 'Moved Temporarily';
            }
            header("HTTP/{$httpVersion} {$statusCode} {$messages[$statusCode]}");
            header("Status: {$statusCode} {$messages[$statusCode]}", true, $statusCode);
        }
    }

    /**
     * ダウンロード用の HTTP ヘッダを出力する
     *
     * @param string $file_name
     * @return void
     */
    public function headerForDownload($file_name) {
        header("Content-disposition: attachment; filename={$file_name}");
        header("Content-type: application/octet-stream; name={$file_name}");
        header('Cache-Control: ');
        header('Pragma: ');
    }
}
