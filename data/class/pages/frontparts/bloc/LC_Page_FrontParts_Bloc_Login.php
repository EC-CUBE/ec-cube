<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * ログイン のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc_Login extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = BLOC_PATH . 'login.tpl';
        $this->tpl_login = false;
        $this->tpl_disable_logout = false;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objCustomer = new SC_Customer();
        // クッキー管理クラス
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);

        // ログイン判定
        if($objCustomer->isLoginSuccess()) {
            $this->tpl_login = true;
            $this->tpl_user_point = $objCustomer->getValue('point');
            $this->tpl_name1 = $objCustomer->getValue('name01');
            $this->tpl_name2 = $objCustomer->getValue('name02');
        } else {
            // クッキー判定
            $this->tpl_login_email = $objCookie->getCookie('login_email');
            if($this->tpl_login_email != "") {
                $this->tpl_login_memory = "1";
            }

            // POSTされてきたIDがある場合は優先する。
            if($_POST['login_email'] != "") {
                $this->tpl_login_email = $_POST['login_email'];
            }
        }

        $this->tpl_disable_logout = $this->lfCheckDisableLogout();
        $objSubView = new SC_SiteView();
        $objSubView->assignobj($this);
        $objSubView->display($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    function lfCheckDisableLogout() {
        $masterData = new SC_DB_MasterData_Ex();
        $arrDISABLE_LOGOUT = $masterData->getMasterData("mtb_disable_logout");
        $nowpage = $_SERVER['PHP_SELF'];

        foreach($arrDISABLE_LOGOUT as $val) {
            if($nowpage == $val) {
                return true;
            }
         }
        return false;
    }
}
?>
