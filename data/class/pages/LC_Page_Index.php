<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * Index のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Index extends LC_Page {

    // {{{ properties

    /**メインテンプレート */
    var $tpl_mainpage;

    /** CSS のパス */
    var $tpl_css;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = HTML_PATH . "user_data/templates/top.tpl";
        $this->tpl_css = URL_DIR . 'css/layout/index.css';
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->tpl_mainpage = 'top.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {

        $objView = new SC_SiteView();

        // レイアウトデザインを取得
        $layout = new SC_Helper_PageLayout_Ex();
        $objPage = $layout->sfGetPageLayout($this, false, "index.php");

        $objView->assignobj($objPage);
        $objView->display(SITE_FRAME);
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $conn = new SC_DBConn();
        $objCustomer = new SC_Customer();

        $objView = new SC_MobileView();
        $objView->assign("isLogin", $objCustomer->isLoginSuccess());
        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
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
