<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 特定商取引に関する法律 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Order extends LC_Page {

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
        $objView = new SC_SiteView();
        $objQuery = new SC_Query();

        // レイアウトデザインを取得
        $layout = new SC_Helper_PageLayout_Ex();
        $this = $layout->sfGetPageLayout($this, false, DEF_LAYOUT);

        $arrRet = $objQuery->getall("SELECT * FROM dtb_baseinfo",array());
        $this->arrRet = $arrRet[0];
        $this->arrPref = $arrPref;

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
