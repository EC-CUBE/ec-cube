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
 * 受注管理メール確認 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_MailView extends LC_Page_AbstractMypage_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->httpCacheControl('nocache');
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
        $objCustomer = new SC_Customer_Ex();
        if (!SC_Utils_Ex::sfIsInt($_GET['send_id'])) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $arrMailView = $this->lfGetMailView($_GET['send_id'], $objCustomer->getValue('customer_id'));

        if (empty($arrMailView)) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $this->tpl_subject  = $arrMailView[0]['subject'];
        $this->tpl_body     = $arrMailView[0]['mail_body'];

        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_PC) {
            $this->setTemplate('mypage/mail_view.tpl');
        } else {
            $this->tpl_title    = 'メール履歴詳細';
            $this->tpl_mainpage = 'mypage/mail_view.tpl';
        }

        switch ($this->getMode()) {
        case 'getDetail':
            echo SC_Utils_Ex::jsonEncode($arrMailView);
            exit;
            break;
        default:
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
     * GETで指定された受注idのメール送信内容を返す
     *
     * @param mixed $send_id
     * @param mixed $customer_id
     * @access private
     * @return array
     */
    function lfGetMailView($send_id, $customer_id) {
        $objQuery   = SC_Query_Ex::getSingletonInstance();
        $col        = "subject, mail_body";
        $where      = "send_id = ? AND customer_id = ?";
        $arrval     = array($send_id, $customer_id);
        return $objQuery->select($col, "dtb_mail_history LEFT JOIN dtb_order USING(order_id)", $where, $arrval);
    }
}
