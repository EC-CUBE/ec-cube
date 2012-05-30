<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * 退会手続き のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_Refusal extends LC_Page_AbstractMypage_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_subtitle = '退会手続き(入力ページ)';
        $this->tpl_mypageno = 'refusal';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {

        switch ($this->getMode()) {
            case 'confirm':
                $this->tpl_mainpage     = 'mypage/refusal_confirm.tpl';
                $this->tpl_subtitle     = '退会手続き(確認ページ)';
                break;

            case 'complete':
                $objCustomer = new SC_Customer_Ex();
                $this->lfDeleteCustomer($objCustomer->getValue('customer_id'));
                $objCustomer->EndSession();


                SC_Response_Ex::sendRedirect('refusal_complete.php');

            default:
                break;
        }

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
     * 会員情報を削除する
     *
     * @access private
     * @return void
     */
    function lfDeleteCustomer($customer_id) {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        $sqlval['del_flg']      = 1;
        $sqlval['update_date']  = 'CURRENT_TIMESTAMP';
        $where                  = 'customer_id = ?';
        $objQuery->update('dtb_customer', $sqlval, $where, array($customer_id));
    }

}
