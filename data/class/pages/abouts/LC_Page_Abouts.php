<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * サイト概要のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Abouts extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'abouts/index.tpl';
        $this->tpl_page_category = 'abouts';
        $this->tpl_title = '当サイトについて';
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
        $objPage = $layout->sfGetPageLayout($this, false, DEF_LAYOUT);

        $objView->assignobj($objPage);
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
