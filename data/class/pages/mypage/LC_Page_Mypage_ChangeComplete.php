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
 * 登録内容変更完了 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_ChangeComplete extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = TEMPLATE_DIR . 'mypage/change_complete.tpl';
        $this->tpl_title = 'MYページ/会員登録内容変更(完了ページ)';
        $this->tpl_navi = TEMPLATE_DIR . 'mypage/navi.tpl';
        $this->tpl_mypageno = 'change';
        $this->tpl_column_num = 1;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $objCustomer = new SC_Customer();

        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, "mypage/index.php");

        //ログイン判定
        if (!$objCustomer->isLoginSuccess()){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        } else {
            //マイページトップ顧客情報表示用
            $this->CustomerName1 = $objCustomer->getvalue('name01');
            $this->CustomerName2 = $objCustomer->getvalue('name02');
            $this->CustomerPoint = $objCustomer->getvalue('point');
        }

        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->tpl_mainpage = 'mypage/change_complete.tpl';
        $this->tpl_title = 'MYページ/会員登録内容変更(完了ページ)';
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $objView = new SC_MobileView();
        $objCustomer = new SC_Customer();


        //セッション情報を最新の状態に更新する
        $objCustomer->updateSession();

        //ログイン判定
        if (!$objCustomer->isLoginSuccess(true)){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR, "", false, "", true);
        }else {
            //マイページトップ顧客情報表示用
            $this->CustomerName1 = $objCustomer->getvalue('name01');
            $this->CustomerName2 = $objCustomer->getvalue('name02');
            $this->CustomerPoint = $objCustomer->getvalue('point');
        }

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
