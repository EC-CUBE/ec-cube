<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once(CLASS_PATH . "pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php");

/**
 * ログイン のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_FrontParts_Bloc_Login.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_FrontParts_Bloc_Login extends LC_Page_FrontParts_Bloc {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $bloc_file = 'login.tpl';
        $this->setTplMainpage($bloc_file);
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
        $this->transactionid = $this->getToken();
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
