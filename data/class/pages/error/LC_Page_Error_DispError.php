<!-- -*- coding: utf-8 -*- -->
<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/error/LC_Page_Error.php");

/**
 * エラー表示のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Error.php 15141 2007-07-27 10:59:11Z nanasess $
 */
class LC_Page_Error_DispError extends LC_Page_Error {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'login_error.tpl';
        $this->tpl_css = URL_DIR.'css/layout/error.css';
        $this->tpl_title = 'エラー';
    }

    /**
     * Page のプロセス。
     *
     * @return void
     */
    function process() {
        parent::process();
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
