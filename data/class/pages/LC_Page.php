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

    // {{{ valiables

    /**
     * 安全に POST するための URL
     */
    var $postURL;

    /**
     * このページで使用する遷移先
     */
    var $transitions;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $this->postURL = $_SERVER['PHP_SELF'];
    }

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
    }

    /**
     * 指定の URL へリダイレクトする.
     *
     * リダイレクト先 URL は自サイトである必要がある.
     *
     * @param string $url リダイレクト先 URL
     * @return void
     */
    function sendRedirect($url) {
        $_SESSION['previousURI'] = $_SESSION['currentURI'];
        Location($url);
    }
}
?>
