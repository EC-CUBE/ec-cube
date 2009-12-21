<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_PATH . "pages/error/LC_Page_Error.php");

/**
 * エラー表示のページクラス
 * SC_DbConnでエラーが発生した場合の表示ページ
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Error_SystemError extends LC_Page_Error {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->tpl_title = 'システムエラー';
        $this->adminPage = $this->isAdminPage();

        if ($this->adminPage) {
            $this->tpl_mainpage = 'login_error.tpl';
            $this->flame = LOGIN_FRAME;
        } else {
            $this->flame = SITE_FRAME;
        }
    }

    /**
     * Page のプロセス。
     *
     * @return void
     */
    function process() {
        require_once CLASS_PATH . 'SC_MobileUserAgent.php';

        $objView = null;
        if (SC_MobileUserAgent::isMobile() && $this->adminPage == false) {
            $objView = new SC_InstallView(MOBILE_TEMPLATE_DIR, MOBILE_COMPILE_DIR);
        } elseif($this->adminPage) {
            $objView = new SC_InstallView(TEMPLATE_ADMIN_DIR, COMPILE_ADMIN_DIR);
        } else {
            $objView = new SC_InstallView(TEMPLATE_DIR, COMPILE_DIR);
        }

        $this->tpl_error = "システムエラーが発生しました。<br />大変お手数ですが、サイト管理者までご連絡ください。";

        $objView->assignobj($this);
        $objView->display($this->flame);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * 管理ページかどうかを判定する.
     *
     * @return boolean
     */
    function isAdminPage() {
        return preg_match('|/admin/|', $_SERVER['PHP_SELF']);
    }
}
?>
