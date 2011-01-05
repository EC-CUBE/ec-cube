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
 * TODO mypage/LC_Page_Mypage_LoginCheck と統合
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_FrontParts_LoginCheck.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_FrontParts_LoginCheck extends LC_Page {

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
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objCustomer = new SC_Customer();
        // 不正なURLがPOSTされた場合はエラー表示
        if (!SC_Helper_Session_Ex::isValidToken()) {
            GC_Utils_Ex::gfPrintLog('invalid access :login_check.php $POST["url"]=' . $_POST['url']);
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR);
        }
        // クッキー管理クラス
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);
        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        //パスワード・Eメールにある空白をトリム
        $_POST["login_email"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["login_email"]);
        $_POST["login_pass"] = trim($_POST["login_pass"]); //認証用
        $_POST["login_pass1"] = $_POST["login_pass"];      //最小桁数比較用
        $_POST["login_pass2"] = $_POST["login_pass"];      //最大桁数比較用
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'login':
            $this->objFormParam->toLower('login_email');
            $arrErr = $this->objFormParam->checkError();

            // エラーの場合はエラー画面に遷移
            if (count($arrErr) > 0) {
                SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
            }
            $arrForm =  $this->objFormParam->getHashArray();
            // クッキー保存判定
            if ($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
                $objCookie->setCookie('login_email', $_POST['login_email']);
            } else {
                $objCookie->setCookie('login_email', '');
            }

            if(count($arrErr) == 0) {
                if($objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'], true)) {
                    $this->objDisplay->redirect('/', array(), false, true);
                    exit;
                } else {
                    $arrForm['login_email'] = strtolower($arrForm['login_email']);
                    $objQuery = new SC_Query;
                    $where = "(email = ? OR email_mobile = ?) AND status = 1 AND del_flg = 0";
                    $ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email'], $arrForm['login_email']));

                    if($ret > 0) {
                        SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
                    } else {
                        SC_Utils_Ex::sfDispSiteError(SITE_LOGIN_ERROR);
                    }
                }
            } else {
                // 入力エラーの場合、元のアドレスに戻す。
                $this->objDisplay->redirect($this->getLocation($_POST['url'], array(), false));
                exit;
            }
            break;
        case 'logout':
            // ログイン情報の解放
            $objCustomer->EndSession();
            $mypage_url_search = strpos('.'.$_POST['url'], "mypage");
            //マイページログイン中はログイン画面へ移行
            if ($mypage_url_search == 2){
                $this->objDisplay->redirect('/mypage/login.php');
            }else{
                $this->objDisplay->redirect('/', array(), false, true);
            }
            exit;
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
        $this->objFormParam->addParam("記憶する", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("メールアドレス", "login_email", MTEXT_LEN, "a", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK"));
        $this->objFormParam->addParam("パスワード", "login_pass", PASSWORD_LEN1, "", array("EXIST_CHECK"));
        $this->objFormParam->addParam("パスワード", "login_pass1", PASSWORD_LEN1, "", array("EXIST_CHECK", "MIN_LENGTH_CHECK"));
        $this->objFormParam->addParam("パスワード", "login_pass2", PASSWORD_LEN2, "", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }
}
?>
