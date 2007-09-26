<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * ログインチェック のページクラス.
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
        $objCustomer = new SC_Customer();
        // 不正なURLがPOSTされた場合はエラー表示
        if (!$this->isValidToken()) {
            GC_Utils_Ex::gfPrintLog('invalid access :login_check.php $POST["url"]=' . $_POST['url']);
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR);
        }
        // クッキー管理クラス
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);
        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'login':
            $this->objFormParam->toLower('login_email');
            $arrErr = $this->objFormParam->checkError();
            $arrForm =  $this->objFormParam->getHashArray();
            // クッキー保存判定
            if ($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
                $objCookie->setCookie('login_email', $_POST['login_email']);
            } else {
                $objCookie->setCookie('login_email', '');
            }

            if(count($arrErr) == 0) {
                if($objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
                    $this->sendRedirect($this->getLocation($_POST['url']));
                    exit;
                } else {
                    $objQuery = new SC_Query;
                    $where = "email ILIKE ? AND status = 1 AND del_flg = 0";
                    $ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));

                    if($ret > 0) {
                        SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
                    } else {
                        SC_Utils_Ex::sfDispSiteError(SITE_LOGIN_ERROR);
                    }
                }
            } else {
                // 入力エラーの場合、元のアドレスに戻す。
                $this->sendRedirect($this->getLocation($_POST['url']));
                exit;
            }
            break;
        case 'logout':
            // ログイン情報の解放
            $objCustomer->EndSession();
            $mypage_url_search = strpos('.'.$_POST['url'], "mypage");
            //マイページログイン中はログイン画面へ移行
            if ($mypage_url_search == 2){
                $this->sendRedirect($this->getLocation(URL_DIR . "mypage/login.php"));
            }else{
                $this->sendRedirect($this->getLocation($_POST['url']));
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
        $this->objFormParam->addParam("メールアドレス", "login_email", STEXT_LEN, "a", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("パスワード", "login_pass", STEXT_LEN, "", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }
}
?>
