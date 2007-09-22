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
 * @version $Id:LC_Page_Index.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Index extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage =  TEMPLATE_DIR . "top.tpl";
        $this->tpl_column_num = 3;
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
        $layout->sfGetPageLayout($this, false, "index.php");

        $objView->assignobj($this);
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
