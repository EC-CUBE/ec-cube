<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * アプリケーション管理:インストールログ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_App_Log extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->tpl_mainpage = 'app/log.tpl';
        $this->tpl_subnavi  = 'app/subnavi.tpl';
        $this->tpl_mainno   = 'app';
        $this->tpl_subno    = 'log';
        $this->tpl_subtitle = 'ログ管理';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {

        // ログインチェック
        SC_Utils::sfIsSuccess(new SC_Session());

        // ページ出力
        $objView = new SC_AdminView();
        $objView->assignObj($this);
        $objView->display(MAIN_FRAME);
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
