<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");
require_once(realpath(dirname( __FILE__)) . "/include.php");

/**
 * メルマガ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_MDL_SPEEDMAIL extends LC_Page {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
    	parent::init();
        $this->tpl_mainpage = MODULE2_PATH . THIS_MODULE_NAME . "/config.tpl";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $objSess = new SC_Session();

        // 認証可否の判定
        //SC_Utils_Ex::sfIsSuccess($objSess);

        $objView->assignobj($this);
        $objView->display($this->tpl_mainpage);
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