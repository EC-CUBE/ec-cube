<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * Myページログイン のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_Login extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = TEMPLATE_DIR . 'mypage/login.tpl';
        $this->tpl_title = 'MYページ(ログイン)';
        $this->tpl_column_num = 1;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();

        // クッキー管理クラス
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);

        //SSLURL判定
        if (SSLURL_CHECK == 1){
            $ssl_url= sfRmDupSlash(SSL_URL.$_SERVER['REQUEST_URI']);
            if (!ereg("^https://", $non_ssl_url)){
                SC_Utils_Ex::sfDispSiteError(URL_ERROR);
            }
        }

        // ログイン判定
        if($objCustomer->isLoginSuccess()) {
            $this->sendRedirect($this->getLocation("./index.php"));
        } else {
            // クッキー判定
            $this->tpl_login_email = $objCookie->getCookie('login_email');
            if($this->tpl_login_email != "") {
                $this->tpl_login_memory = "1";
            }

            // POSTされてきたIDがある場合は優先する。
            if(isset($_POST['mypage_login_email'])
               && $_POST['mypage_login_email'] != "") {
                $this->tpl_login_email = $_POST['mypage_login_email'];
            }
        }

        //$objpage内の全てのテンプレート変数をsmartyに格納
        $objView->assignobj($this);
        //パスとテンプレート変数の呼び出し、実行
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

    //エラーチェック
    function lfErrorCheck() {
        $objErr = new SC_CheckError();
        $objErr->doFunc(array("メールアドレス", "login_email", STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","EMAIL_CHECK","MAX_LENGTH_CHECK"));
        $objErr->dofunc(array("パスワード", "login_password", PASSWORD_LEN2), array("EXIST_CHECK","ALNUM_CHECK"));
        return $objErr->arrErr;
    }
}
?>
