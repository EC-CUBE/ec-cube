<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 退会手続 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_RefusalComplate extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = TEMPLATE_DIR . 'mypage/refusal_complete.tpl';
        $this->tpl_title = "MYページ/退会手続き(完了ページ)";
        $this->tpl_navi = TEMPLATE_DIR . 'mypage/navi.tpl';
        $this->tpl_mypageno = 'refusal';
        $this->tpl_column_num = 1;
        $this->point_disp = false;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();

        $objCustomer = new SC_Customer();
        //マイページトップ顧客情報表示用
        $this->CustomerName1 = $objCustomer->getvalue('name01');
        $this->CustomerName2 = $objCustomer->getvalue('name02');
        $this->CustomerPoint = $objCustomer->getvalue('point');

        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, "mypage/index.php");

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
