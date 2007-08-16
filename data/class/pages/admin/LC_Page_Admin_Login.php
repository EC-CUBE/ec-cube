<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 管理者ログイン のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Login extends LC_Page {

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
        $conn = new SC_DBConn();
        $this->objSess = new SC_Session();
        $ret = false;

        // 入力判定
        if(strlen($_POST{'login_id'}) > 0 && strlen($_POST{'password'}) > 0) {
            // 認証パスワードの判定
            $ret = $this->fnCheckPassword($conn);
        }

        if($ret) {
            // 成功
            $this->sendRedirect($this->getLocation(URL_HOME));
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
    function fnCheckPassword($conn) {
        $sql = "SELECT member_id, password, authority, login_date, name FROM dtb_member WHERE login_id = ? AND del_flg <> 1 AND work = 1";
        $arrcol = array ($_POST['login_id']);
        // DBから暗号化パスワードを取得する。
        $data_list = $conn->getAll($sql ,$arrcol);
        // パスワードの取得
        $password = $data_list[0]['password'];
        // ユーザ入力パスワードの判定
        $ret = sha1($_POST['password'] . ":" . AUTH_MAGIC);

        if ($ret == $password) {
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
        $oquery = new SC_Query();
        $sqlval['login_date'] = date("Y-m-d H:i:s");
        $member_id = $this->objSess->GetSession('member_id');
        $where = "member_id = " . $member_id;
        $ret = $oquery->update("dtb_member", $sqlval, $where);
    }
}
?>
