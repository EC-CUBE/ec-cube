<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
 * @version $Id$
 */
class LC_Page {

    // {{{ properties

    /** メインテンプレート */
    var $tpl_mainpage;

    /** メインナンバー */
    var $tpl_mainno;

    /** CSS のパス */
    var $tpl_css;

    /** タイトル */
    var $tpl_title;

    /** カテゴリ */
    var $tpl_page_category;

    /** ログインメールアドレス */
    var $tpl_login_email;

    /** body タグの onload 属性 */
    var $tpl_onload;

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
     * 遷移元が自サイトかどうかチェックする.
     *
     * 遷移元が自サイト以外の場合はエラーページへ遷移する.
     *
     * @return void
     */
    function checkPreviousURI() {
        // TODO 必要性検討
    }

    /**
     * 指定の URL へリダイレクトする.
     *
     * リダイレクト先 URL に SITE_URL 及び SSL_URL を含むかチェックし,
     * LC_Page::getToken() の値を URLパラメータで自動的に付与する.
     *
     * @param string $url リダイレクト先 URL
     * @return void|boolean $url に SITE_URL 及び, SSL_URL を含まない場合 false,
     * 						 正常に遷移可能な場合は, $url の URL へ遷移する.
     */
    function sendRedirect($url) {

        if (preg_match("/(" . preg_quote(SITE_URL, '/')
                          . "|" . preg_quote(SSL_URL, '/') . ")/", $url)) {

            $suffix = "?";
            if (!empty($_SERVER['QUERY_STRING'])) {
                $suffix = "&";
            }
            header("Location: " . $url . $suffix . TRANSACTION_ID_NAME . "=" . $this->getToken());
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
     * @param string $path 結果を取得するためのパス
     * @param array $param URL に付与するパラメータの配列
     * @param boolean $useSSL 結果に SSL_URL を使用する場合 true,
     * 				           SITE_URL を使用する場合 false,
     * 						   デフォルト false
     * @param string $documentRoot DocumentRoot の文字列. 指定しない場合は,
     *                              $_SERVER['DOCUMENT_ROOT'] が付与される.
     * @return string $path の存在する http(s):// から始まる絶対パス
     */
    function getLocation($path, $param = array(), $useSSL = false, $documentRoot = "") {

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
        if ($useSSL) {
            $url = SSL_URL . $root;
        } else {
            $url = SITE_URL . $root;
        }

        // QUERY_STRING 生成
        $queryString = "";
        $i = count($param);
        foreach ($param as $key => $val) {
            $queryString .= $key . "=" . $val;

            if ($i > 1) {
                $queryString .= "&";
            }
            $i--;
        }

        // QUERY_STRING が存在する場合は付与して返す.
        if (empty($queryString)) {
            return $url;
        } else {
            return $url . "?" . $queryString;
        }
    }

    // }}}
    // {{{ private functions

    /**
     * トランザクショントークン用の予測困難な文字列を生成して返す.
     *
     * @return string トランザクショントークン用の文字列
     */
    function createToken() {
        return sha1(uniqid(rand(), true));
    }
}
?>
