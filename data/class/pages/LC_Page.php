<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(DATA_PATH . 'module/Net/URL.php');

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
class LC_Page {

    // {{{ properties

    /** メインテンプレート */
    var $tpl_mainpage;

    /** メインナンバー */
    var $tpl_mainno;

    /** CSS のパス */
    var $tpl_css;

    /** JavaScript */
    var $tpl_javascript;

    /** タイトル */
    var $tpl_title;

    /** カテゴリ */
    var $tpl_page_category;

    /** ログインメールアドレス */
    var $tpl_login_email;

    /** body タグの onload 属性 */
    var $tpl_onload;

    /** 送料合計 */
    var $tpl_total_deliv_fee;

    /** トランザクションID */
    var $transactionid;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {}

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {}

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {}

    /**
     * 指定の URL へリダイレクトする.
     *
     * リダイレクト先 URL に SITE_URL 及び SSL_URL を含むかチェックし,
     * LC_Page::getToken() の値を URLパラメータで自動的に付与する.
     *
     * @param string $url リダイレクト先 URL
     * @return void|boolean $url に SITE_URL 及び, SSL_URL を含まない場合 false,
     * 						 正常に遷移可能な場合は, $url の URL へ遷移する.
     * @see Net_URL
     */
    function sendRedirect($url) {

        if (preg_match("/(" . preg_quote(SITE_URL, '/')
                          . "|" . preg_quote(SSL_URL, '/') . ")/", $url)) {

            $netURL = new Net_URL($url);
            if (!empty($_SERVER['QUERY_STRING'])) {
                $netURL->addRawQueryString($_SERVER['QUERY_STRING']);
            }
            $netURL->addQueryString(TRANSACTION_ID_NAME, $this->getToken());
            header("Location: " . $netURL->getURL());
        }
        return false;
    }

    // }}}
    // {{{ protected functions

    /**
     * トランザクショントークンを生成し, 取得する.
     *
     * 悪意のある不正な画面遷移を防止するため, 予測困難な文字列を生成して返す.
     * 同時に, この文字列をセッションに保存する.
     *
     * この関数を使用するためには, 生成した文字列を次画面へ渡すパラメータとして
     * 出力する必要がある.
     *
     * 例)
     * <input type="hidden" name="transactionid" value="この関数の返り値" />
     *
     * 遷移先のページで, LC_Page::isValidToken() の返り値をチェックすることにより,
     * 画面遷移の妥当性が確認できる.
     *
     * @access protected
     * @return string トランザクショントークンの文字列
     */
    function getToken() {
        if (empty($_SESSION[TRANSACTION_ID_NAME])) {
            $_SESSION[TRANSACTION_ID_NAME] = $this->createToken();
        }
        return $_SESSION[TRANSACTION_ID_NAME];
    }

    /**
     * トランザクショントークンの妥当性をチェックする.
     *
     * 前画面で生成されたトランザクショントークンの妥当性をチェックする.
     * この関数を使用するためには, 前画面のページクラスで LC_Page::getToken()
     * を呼んでおく必要がある.
     *
     * @access protected
     * @return boolean トランザクショントークンが有効な場合 true
     */
    function isValidToken() {

        $checkToken = "";

        // $_POST の値を優先する
        if (isset($_POST[TRANSACTION_ID_NAME])) {

            $checkToken = $_POST[TRANSACTION_ID_NAME];
        } elseif (isset($_GET[TRANSACTION_ID_NAME])) {

            $checkToken = $_GET[TRANSACTION_ID_NAME];
        }

        $ret = false;
        // token の妥当性チェック
        if ($checkToken === $_SESSION[TRANSACTION_ID_NAME]) {

            $ret = true;
        }

        unset($_SESSION[TRANSACTION_ID_NAME]);
        return $ret;
    }

    /**
     * $path から URL を取得する.
     *
     * 以下の順序で 引数 $path から URL を取得する.
     * 1. realpath($path) で $path の 絶対パスを取得
     * 2. $_SERVER['DOCUMENT_ROOT'] と一致する文字列を削除
     * 3. $useSSL の値に応じて, SITE_URL 又は, SSL_URL を付与する.
     *
     * 返り値に, QUERY_STRING を含めたい場合は, key => value 形式
     * の配列を $param へ渡す.
     *
     * @access protected
     * @param string $path 結果を取得するためのパス
     * @param array $param URL に付与するパラメータの配列
     * @param mixed $useSSL 結果に SSL_URL を使用する場合 true,
     * 				           SITE_URL を使用する場合 false,
     * 						   デフォルト "escape" 現在のスキーマを使用
     * @param string $documentRoot DocumentRoot の文字列. 指定しない場合は,
     *                              $_SERVER['DOCUMENT_ROOT'] が付与される.
     * @return string $path の存在する http(s):// から始まる絶対パス
     * @see Net_URL
     */
    function getLocation($path, $param = array(), $useSSL = "escape", $documentRoot = "") {

        // TODO $_SERVER['DOCUMENT_ROOT'] をインストーラでチェックする.
        if (empty($documentRoot)) {
            $documentRoot = $_SERVER['DOCUMENT_ROOT'];

            if (empty($documentRoot)) {
                die("[BUG] can't get DOCUMENT_ROOT");
            }
        }

        // $path が / で始まっている場合
        if (substr($path, 0, 1) == "/") {
            $realPath = realpath(HTML_PATH . substr_replace($path, "", 0, 1));
        } else {
            // 相対パスの場合
            $realPath = realpath($path);
        }

        // DocumentRoot を削除した文字列を取得.
        $root = str_replace($documentRoot, "", $realPath);
        // 先頭の / を削除
        $root = substr_replace($root, "", 0, 1);

        // スキーマを定義
        if ($useSSL === true) {
            $url = SSL_URL . $root;
        } elseif ($useSSL === false){
            $url = SITE_URL . $root;
        } elseif ($useSSL == "escape") {
            if (SC_Utils_Ex::sfIsHTTPS()) {
                $url = SSL_URL . $root;
            } else {
                $url = SITE_URL . $root;
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
     * ページをリロードする.
     *
     * 引数 $queryString に, $_SERVER['QUERY_STRING'] の値を使用してはならない.
     * この関数は, 内部で LC_Page::sendRedirect() を使用するため,
     * $_SERVER['QUERY_STRING'] の値は自動的に付与される.
     *
     * @param array $queryString QueryString の配列
     * @return void
     * @see Net_URL
     */
    function reload($queryString = array()) {

        // 現在の URL を取得
        $netURL = new Net_URL();

        // QueryString を付与
        if (!empty($queryString)) {
            foreach ($queryString as $key => $val) {
                $netURL->addQueryString($key, $val);
            }
        }
        $this->sendRedirect($netURL->getURL());
    }

    /**
     * クライアントのキャッシュを許可する.
     *
     * session_start時のno-cacheヘッダーを抑制することで
     * 「戻る」ボタン使用時の有効期限切れ表示を抑制する.
     *
     * @access protected
     * @return void
     */
    function allowClientCache() {
        session_cache_limiter('private-no-expire');
    }

    /**
     * デバック出力を行う.
     *
     * デバック用途のみに使用すること.
     *
     * @access protected
     * @param mixed $val デバックする要素
     * @return void
     */
    function p($val) {
        SC_Utils_Ex::sfPrintR($val);
    }

    // }}}
    // {{{ private functions

    /**
     * トランザクショントークン用の予測困難な文字列を生成して返す.
     *
     * @access private
     * @return string トランザクショントークン用の文字列
     */
    function createToken() {
        return sha1(uniqid(rand(), true));
    }
}
?>
