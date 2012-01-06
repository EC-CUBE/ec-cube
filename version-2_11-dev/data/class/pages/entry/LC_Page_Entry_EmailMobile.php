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
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 携帯メールアドレス登録のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Entry_EmailMobile extends LC_Page_Ex {

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
        parent::process();
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
        $objFormParam   = new SC_FormParam_Ex();

        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->arrErr = $this->lfCheckError($objFormParam);

            if (empty($this->arrErr)) {
                $email_mobile = $this->lfRegistEmailMobile(strtolower($objFormParam->getValue('email_mobile')),
                    $objCustomer->getValue('customer_id'));

                $objCustomer->setValue('email_mobile', $email_mobile);
                $this->tpl_mainpage = 'entry/email_mobile_complete.tpl';
                $this->tpl_title = '携帯メール登録完了';
            }
        }

        $this->tpl_name = $objCustomer->getValue('name01');
        $this->arrForm  = $objFormParam->getFormParamList();
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
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('メールアドレス', 'email_mobile', null, 'a',
                                array('NO_SPTAB', 'EXIST_CHECK', 'CHANGE_LOWER', 'EMAIL_CHAR_CHECK', 'EMAIL_CHECK', 'MOBILE_EMAIL_CHECK'));
    }

    /**
     * エラーチェックする
     *
     * @param mixed $objFormParam
     * @param mixed $objCustomer
     * @access private
     * @return array エラー情報の配列
     */
    function lfCheckError(&$objFormParam) {
        $objFormParam->convParam();
        $objErr         = new SC_CheckError_Ex();
        $objErr->arrErr = $objFormParam->checkError();

        // FIXME: lfInitParam() で設定すれば良いように感じる
        $objErr->doFunc(array("メールアドレス", "email_mobile"), array("CHECK_REGIST_CUSTOMER_EMAIL"));

        return $objErr->arrErr;
    }

    /**
     *
     * 携帯メールアドレスが登録されていないユーザーに携帯アドレスを登録する
     *
     * 登録完了後にsessionのemail_mobileを更新する
     *
     * @param mixed $objFormParam
     * @param mixed $objCustomer
     * @access private
     * @return void
     */
    function lfRegistEmailMobile($email_mobile, $customer_id) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_customer',
                          array('email_mobile' => $email_mobile),
                          'customer_id = ?', array($customer_id));

        return $email_mobile;
    }
}
?>
