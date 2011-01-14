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
 * HttpResponse を扱うクラス.
 *
 * @author Ryuichi Tokugami
 * @version $Id$
 */
class SC_Response{

    /**
     * コンテンツタイプ
     * Enter description here ...
     * @var unknown_type
     */
    var $contentType;
    var $body;
    var $statuscode;
    var $header = array();

    var $statusTexts = array(
            '100' => 'Continue',
            '101' => 'Switching Protocols',
            '200' => 'OK',
            '201' => 'Created',
            '202' => 'Accepted',
            '203' => 'Non-Authoritative Information',
            '204' => 'No Content',
            '205' => 'Reset Content',
            '206' => 'Partial Content',
            '300' => 'Multiple Choices',
            '301' => 'Moved Permanently',
            '302' => 'Found',
            '303' => 'See Other',
            '304' => 'Not Modified',
            '305' => 'Use Proxy',
            '306' => '(Unused)',
            '307' => 'Temporary Redirect',
            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '402' => 'Payment Required',
            '403' => 'Forbidden',
            '404' => 'Not Found',
            '405' => 'Method Not Allowed',
            '406' => 'Not Acceptable',
            '407' => 'Proxy Authentication Required',
            '408' => 'Request Timeout',
            '409' => 'Conflict',
            '410' => 'Gone',
            '411' => 'Length Required',
            '412' => 'Precondition Failed',
            '413' => 'Request Entity Too Large',
            '414' => 'Request-URI Too Long',
            '415' => 'Unsupported Media Type',
            '416' => 'Requested Range Not Satisfiable',
            '417' => 'Expectation Failed',
            '500' => 'Internal Server Error',
            '501' => 'Not Implemented',
            '502' => 'Bad Gateway',
            '503' => 'Service Unavailable',
            '504' => 'Gateway Timeout',
            '505' => 'HTTP Version Not Supported',
        );


    /**
     *
     * Enter description here ...
     */
    var $encoding;

    function SC_Response() {
    }

    /**
     * レスポンス出力を書き込む.
     */
    function write() {
        $this->sendHeader();
        echo $this->body;
    }

    function sendHeader() {
        // HTTPのヘッダ
        //        header('HTTP/1.1 '.$this->statuscode.' '.$this->statusTexts[$this->statuscode]);
        foreach ($this->header as $name => $head){
            header($name.': '.$head);
        }
    }


    function setContentType($contentType) {
        $this->header['Content-Type'] = $contentType;
    }

    function setResposeBody($body){
        $this->body = $body;
    }

    function addHeader($name, $value) {
        $this->header[$name] = $value;
    }

    function containsHeader($name) {
        return isset($this->header[$name]);
    }

    function sendError($errorcode) {
        header('HTTP/1.1 '.$errorcode.' '.$this->statusTexts[$errorcode]);
    }

    /**
     * アプリケーション内でリダイレクトする
     *
     * @param string $location 「url-path」「現在のURLからのパス」「URL」のいずれか。「../」の解釈は行なわない。
     * @return void
     * @static
     */
    function sendRedirect($location, $arrQueryString = array(), $inheritQueryString = false, $useSsl = null) {

        // url-path → URL 変換
        if ($location[0] === '/') {
            $netUrl = new Net_URL();
            $netUrl->path = $location;
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
                }
                else {
                    $pattern = '/^' . preg_quote(HTTPS_URL, '/') . '(.*)/';
                    $replacement = HTTP_URL . '\1';
                    $url = preg_replace($pattern, $replacement, $url);
                }
            }
        }
        // 現在のURLからのパス
        else {
            if (!is_bool($useSsl)) {
                $useSsl = SC_Utils_Ex::sfIsHTTPS();
            }
            $netUrl = new Net_URL($useSsl ? HTTPS_URL : HTTP_URL);
            $netUrl->path = dirname($_SERVER['PHP_SELF']) . '/' . $location;
            $url = $netUrl->getUrl();
        }

        $pattern = '/^(' . preg_quote(HTTP_URL, '/') . '|' . preg_quote(HTTPS_URL, '/') . ')/';

        // アプリケーション外へのリダイレクトは扱わない
        if (preg_match($pattern, $url) === 0) {
            SC_Utils_Ex::sfDispException();
        }

        $netUrl = new Net_URL($url);
        $arrQueryString = array_merge($netUrl->querystring, $arrQueryString);
        $netUrl->querystring = array();

        if ($inheritQueryString) {
            if (!empty($_SERVER['QUERY_STRING'])) {
                $netUrl->addRawQueryString($_SERVER['QUERY_STRING']);
            }
        }

        foreach ($arrQueryString as $key => $val) {
            $netUrl->addQueryString($key, $val);
        }

        $url = $netUrl->getURL();

        $session = SC_SessionFactory::getInstance();
        if (SC_MobileUserAgent::isMobile() || $session->useCookie() == false) {
            $netUrl->addQueryString(session_name(), session_id());
        }

        $netUrl->addQueryString(TRANSACTION_ID_NAME, SC_Helper_Session_Ex::getToken());
        $url = $netUrl->getURL();

        header("Location: $url");
        exit;
    }

    /**
     * /html/ からのパスを指定してリダイレクトする
     *
     * FIXME メソッド名を分かりやすくしたい。現状だと、引数が「url-path より後」とも「url-path」とも読み取れる。(前者が意図したいところ)
     * @param string $location /html/ からのパス。先頭に / を含むかは任意。「../」の解釈は行なわない。
     * @return void
     * @static
     */
    function sendRedirectFromUrlPath($location, $arrQueryString = array(), $inheritQueryString = false, $useSsl = null) {
        $location = URL_PATH . ltrim($location, '/');
        SC_Response_Ex::sendRedirect($location, $arrQueryString, $inheritQueryString, $useSsl);
    }

    /**
     * @static
     */
    function reload($arrQueryString = array(), $removeQueryString = false) {
        // 現在の URL を取得
        $netUrl = new Net_URL($_SERVER['REQUEST_URI']);

        if (!$removeQueryString) {
            $arrQueryString = array_merge($netUrl->querystring, $arrQueryString);
        }
        $netUrl->querystring = array();

        SC_Response_Ex::sendRedirect($netUrl->getURL(), $arrQueryString);
    }

    function setHeader($headers) {
        $this->header = $headers;
    }

    function setStatus($sc = 202) {
        $this->statuscode = $sc;
    }

    /**
     * HTTPステータスコードを送出する。
     *
     * @param integer $code HTTPステータスコード
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
    function sendHttpStatus($code) {
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
            // Server Error 5xx                         // 【サーバエラー】
            500 => 'Internal Server Error',             // サーバ内部エラー
            501 => 'Not Implemented',                   // 実装されていない
            502 => 'Bad Gateway',                       // 不正なゲートウェイ
            503 => 'Service Unavailable',               // サービス利用不可
            504 => 'Gateway Timeout',                   // ゲートウェイタイムアウト
            505 => 'HTTP Version Not Supported',        // サポートしていないHTTPバージョン
            509 => 'Bandwidth Limit Exceeded'           // 帯域幅制限超過
        );
        if (isset($messages[$code])) {
            if ($httpVersion !== '1.1') {
                // HTTP/1.0
                $messages[302] = 'Moved Temporarily';
            }
            header("HTTP/{$httpVersion} {$code} {$messages[$code]}");
            header("Status: {$code} {$messages[$code]}", true, $code);
        }
    }
}
