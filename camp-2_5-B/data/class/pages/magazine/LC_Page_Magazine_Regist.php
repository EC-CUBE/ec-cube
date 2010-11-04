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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * メルマガ登録 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Magazine_Regist extends LC_Page {

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
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->tpl_mainpage = 'magazine/regist.tpl';
        $this->tpl_title .= 'メルマガ登録完了';
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {

        $objQuery = new SC_Query();

        // secret_keyの取得
        $key = $_GET['id'];

        if (empty($key) or !lfExistKey($key, $objQuery))  {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR);
        } else {
            $this->lfChangeData($key, $objQuery);
        }

        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, DEF_LAYOUT);

        $objView = new SC_MobileView();
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

    // メルマガの登録を完了させる
    function lfChangeData($key, &$objQuery) {
        $arrUpdate['mail_flag'] = 2;
        $arrUpdate['secret_key'] = NULL;
        $result = $objQuery->update("dtb_customer_mail", $arrUpdate, "secret_key = " . SC_Utils_Ex::sfQuoteSmart($key));
    }

    // キーが存在するかどうか
    function lfExistKey($key, &$objQuery) {
        $sql = "SELECT count(*) FROM dtb_customer_mail WHERE secret_key = ?";
        $result = $objQuery->getOne($sql, array($key));

        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }
}
?>
