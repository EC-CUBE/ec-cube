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
require_once(CLASS_REALDIR . "pages/LC_Page.php");

/**
 * 受注管理メール確認 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
class LC_Page_Mypage_MailView extends LC_Page {

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
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
        $objCustomer = new SC_Customer();

        // ログインチェック
        if(!$objCustomer->isLoginSuccess(true)) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        if(SC_Utils_Ex::sfIsInt($_GET['send_id'])) {
            $objQuery = new SC_Query();
            $col = "subject, mail_body";
            $where = "send_id = ? AND customer_id = ?";
            $arrval = array($_GET['send_id'], $objCustomer->getValue('customer_id'));
            $arrRet = $objQuery->select($col, "dtb_mail_history LEFT JOIN dtb_order USING(order_id)", $where, $arrval);

            if (empty($arrRet)) {
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
            }
            $this->tpl_subject = $arrRet[0]['subject'];
            $this->tpl_body = $arrRet[0]['mail_body'];
        }

        if (Net_UserAgent_Mobile::isMobile() === true){
            $this->tpl_title = 'メール履歴詳細';
            $this->tpl_mainpage = 'mypage/mail_view.tpl';
        } else {
            $this->setTemplate('mypage/mail_view.tpl');
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
}
?>
