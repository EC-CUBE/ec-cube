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
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * 管理者ログイン のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Login extends LC_Page_Admin {

    // {{{ properties

    /** SC_Session インスタンス */
    var $objSess;

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
        $objQuery = new SC_Query();
        $this->objSess = new SC_Session();
        $ret = false;

        if (!isset($_POST['login_id'])) $_POST['login_id'] = "";
        if (!isset($_POST['password'])) $_POST['password'] = "";

        // 入力判定
        if(strlen($_POST{'login_id'}) > 0 && strlen($_POST{'password'}) >= ID_MIN_LEN && strlen($_POST{'password'}) <= ID_MAX_LEN) {
            // 認証パスワードの判定
            $ret = $this->fnCheckPassword($objQuery);
        }

        if($ret) {
            // 成功
            SC_Response_Ex::sendRedirect(ADMIN_HOME_URLPATH);
            exit;
        } else {
            // エラーページの表示
            SC_Utils_Ex::sfDispError(LOGIN_ERROR);
            exit;
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

    /* 認証パスワードの判定 */
    function fnCheckPassword(&$objQuery) {
        $sql = "SELECT member_id, password, salt, authority, login_date, name FROM dtb_member WHERE login_id = ? AND del_flg <> 1 AND work = 1";
        $arrcol = array ($_POST['login_id']);
        // DBから暗号化パスワードを取得する。
        $data_list = $objQuery->getAll($sql ,$arrcol);
        // パスワードの取得
        $password = $data_list[0]['password'];
        // saltの取得
        $salt = $data_list[0]['salt'];
        // ユーザ入力パスワードの判定
        if (SC_Utils_Ex::sfIsMatchHashPassword($_POST['password'], $password, $salt)) {
               // セッション登録
            $this->fnSetLoginSession($data_list[0]['member_id'], $data_list[0]['authority'], $data_list[0]['login_date'], $data_list[0]['name']);
            // ログイン日時の登録
            $this->fnSetLoginDate();
            return true;
        }

        // パスワード
        GC_Utils_Ex::gfPrintLog($_POST['login_id'] . " password incorrect.");
        return false;
    }

    /* 認証セッションの登録 */
    function fnSetLoginSession($member_id,$authority,$login_date, $login_name = '') {

        // 認証済みの設定
        $this->objSess->SetSession('cert', CERT_STRING);
        $this->objSess->SetSession('login_id', $_POST{'login_id'});
        $this->objSess->SetSession('authority', $authority);
        $this->objSess->SetSession('member_id', $member_id);
        $this->objSess->SetSession('login_name', $login_name);
        $this->objSess->SetSession('uniqid', $this->objSess->getUniqId());

        if(strlen($login_date) > 0) {
            $this->objSess->SetSession('last_login', $login_date);
        } else {
            $this->objSess->SetSession('last_login', date("Y-m-d H:i:s"));
        }
        $sid = $this->objSess->GetSID();
        // ログに記録する
        GC_Utils_Ex::gfPrintLog("login : user=".$_SESSION{'login_id'}." auth=".$_SESSION{'authority'}." lastlogin=". $_SESSION{'last_login'} ." sid=".$sid);
    }

    /* ログイン日時の更新 */
    function fnSetLoginDate() {
        $objQuery = new SC_Query();
        $sqlval['login_date'] = date("Y-m-d H:i:s");
        $member_id = $this->objSess->GetSession('member_id');
        $where = "member_id = " . $member_id;
        $ret = $objQuery->update("dtb_member", $sqlval, $where);
    }
}
?>
