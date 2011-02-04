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
 * 携帯メールアドレス登録のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
class LC_Page_Entry_EmailMobile extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->objFormParam = new SC_FormParam();
        $this->lfInitParam();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objCustomer    = new SC_Customer;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->objFormParam->setParam($_POST);
            $this->objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($objCustomer);

            if (empty($this->arrErr)) {
                $this->lfRegister($objCustomer);
                $this->tpl_mainpage = 'entry/email_mobile_complete.tpl';
                $this->tpl_title = '携帯メール登録完了';
            }
        }

        $this->tpl_name = $objCustomer->getValue('name01');
        $this->arrForm  = $this->objFormParam->getFormParamList();
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
     * lfInitParam
     *
     * @access public
     * @return void
     */
    function lfInitParam() {
        $this->objFormParam->addParam('メールアドレス', 'email_mobile', MTEXT_LEN, 'a',
                                array('NO_SPTAB', 'EXIST_CHECK', 'MAX_LENGTH_CHECK', 'CHANGE_LOWER', 'EMAIL_CHAR_CHECK', 'EMAIL_CHECK', 'MOBILE_EMAIL_CHECK'));
    }

    /**
     * lfCheckError
     *
     * @param mixed $objCustomer
     * @access public
     * @return void
     */
    function lfCheckError(&$objCustomer) {
        $arrRet         = $this->objFormParam->getHashArray();
        $objErr         = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        $objErr->doFunc(array("メールアドレス", "email_mobile"), array("CHECK_REGIST_CUSTOMER_EMAIL"));

        return $objErr->arrErr;
    }

    /**
     * lfRegister
     *
     * @param mixed $objCustomer
     * @access public
     * @return void
     */
    function lfRegister(&$objCustomer) {
        $customer_id    = $objCustomer->getValue('customer_id');
        $email_mobile   = strtolower($this->objFormParam->getValue('email_mobile'));

        $objQuery       = new SC_Query();
        $objQuery->update('dtb_customer', array('email_mobile' => $email_mobile), 'customer_id = ?', array($customer_id));

        $objCustomer->setValue('email_mobile', $email_mobile);
    }
}
?>
