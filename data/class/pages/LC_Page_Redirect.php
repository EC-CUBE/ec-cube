<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * リダイレクト のページクラス.
 *
 * メールからのリダイレクト用
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Redirect extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->init();
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        define('SKIP_MOBILE_INIT', true);

        if (isset($_GET['token'])) {
            $next_url = GC_Utils_Ex::gfFinishKaraMail($_GET['token']);
        }

        // $next_url には, セッションID付与済み
        if (isset($next_url) && $next_url !== false) {
            $this->sendRedirect($next_url);
        } else {
            $this->sendRedirect(MOBILE_SITE_URL, true);
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
