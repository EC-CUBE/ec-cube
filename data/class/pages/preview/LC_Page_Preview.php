<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * プレビュー のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Preview extends LC_Page {

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
        $objSess = new SC_Session();

        if ($_SESSION['preview'] === "ON") {
            // レイアウトデザインを取得
            $objLayout = new SC_Helper_PageLayout_Ex();
            $objLayout->sfGetPageLayout($this, true);

            // top ページは 3カラム
            if (isset($_GET['filename']) && $_GET['filename'] == "top") {
                $this->tpl_column_num = 3;
            }

            // 画面の表示
            $objView->assignobj($this);
            $objView->display(SITE_FRAME);
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
