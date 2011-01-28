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
 * ログインチェック のページクラス.
 *
 * TODO frontparts/LC_Page_Frontparts_LoginCheck と抽象化させる
 * FIXME ロジック見なおし...
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_LoginCheck extends LC_Page {

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
     * Page のAction.
     *
     * @return void
     */
    function action() {
        $objCustomer = new SC_Customer();
        // クッキー管理クラス
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);
        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        //パスワード・Eメールにある空白をトリム
        $_POST["mypage_login_email"] = trim($_POST["mypage_login_email"]);
        $_POST["mypage_login_pass"] = trim($_POST["mypage_login_pass"]);  //認証用
        $_POST["mypage_login_pass1"] = trim($_POST["mypage_login_pass"]); //最小桁数比較用
        $_POST["mypage_login_pass2"] = trim($_POST["mypage_login_pass"]); //最大桁数比較用
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        switch($this->getMode()) {
        case 'login':
            $this->objFormParam->toLower('mypage_login_email');
            $arrErr = $this->objFormParam->checkError();

            // エラーの場合はエラー画面に遷移
            if (count($arrErr) > 0) {
                SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
            }
            $arrForm =  $this->objFormParam->getHashArray();
            // クッキー保存判定
            if ($arrForm['mypage_login_memory'] == "1" && $arrForm['mypage_login_email'] != "") {
                $objCookie->setCookie('login_email', $_POST['mypage_login_email']);
            } else {
                $objCookie->setCookie('login_email', '');
            }

            // ログイン判定
            $loginFailFlag = false;
            if(Net_UserAgent_Mobile::isMobile() === true) {
                // モバイルサイト
                if(!$objCustomer->getCustomerDataFromMobilePhoneIdPass($arrForm['mypage_login_pass']) &&
                   !$objCustomer->getCustomerDataFromEmailPass($arrForm['mypage_login_pass'], $arrForm['mypage_login_email'], true)) {
                    $loginFailFlag = true;
                }
            } else {
                // モバイルサイト以外
                if(!$objCustomer->getCustomerDataFromEmailPass($arrForm['mypage_login_pass'], $arrForm['mypage_login_email'])) {
                    $loginFailFlag = true;
                }
            }
            if($loginFailFlag === true) {
                $arrForm['mypage_login_email'] = strtolower($arrForm['mypage_login_email']);
                $objQuery = new SC_Query;
                $where = "(email = ? OR email_mobile = ?) AND status = 1 AND del_flg = 0";
                $ret = $objQuery->count("dtb_customer", $where, array($arrForm['mypage_login_email'], $arrForm['mypage_login_email']));

                if($ret > 0) {
                    SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
                } else {
                    SC_Utils_Ex::sfDispSiteError(SITE_LOGIN_ERROR);
                }
            } else {
                if(Net_UserAgent_Mobile::isMobile() === true) {
                    // ログインが成功した場合は携帯端末IDを保存する。
                    $objCustomer->updateMobilePhoneId();

                    /*
                     * email がモバイルドメインでは無く,
                     * 携帯メールアドレスが登録されていない場合
                     */
                    $objMobile = new SC_Helper_Mobile_Ex();
                    if (!$objMobile->gfIsMobileMailAddress($objCustomer->getValue('email'))) {
                        if (!$objCustomer->hasValue('email_mobile')) {
                            SC_Response_Ex::sendRedirectFromUrlPath('entry/email_mobile.php');
                            exit;
                        }
                    }
                }

                SC_Response_Ex::sendRedirect(DIR_INDEX_PATH);
                exit;
            }
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

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("記憶する", "mypage_login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("メールアドレス", "mypage_login_email", MTEXT_LEN, "a", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("パスワード", "mypage_login_pass", PASSWORD_LEN1, "KVa", array("EXIST_CHECK"));
        $this->objFormParam->addParam("パスワード", "mypage_login_pass1", PASSWORD_LEN1, "KVa", array("EXIST_CHECK", "MIN_LENGTH_CHECK"));
        $this->objFormParam->addParam("パスワード", "mypage_login_pass2", PASSWORD_LEN2, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }

}
?>
