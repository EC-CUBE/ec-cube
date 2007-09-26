<?php //-*- coding: utf-8 -*-
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * エラー表示のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Error.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Error extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'error.tpl';
        $this->tpl_column_num = 1;
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
