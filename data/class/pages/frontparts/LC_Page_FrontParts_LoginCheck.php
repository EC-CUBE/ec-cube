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
 * @version $Id$
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
        // 不正なURLがPOSTされた場合はエラー表示
        if (isset($_POST['url']) && $this->lfIsValidURL() !== true) {
            GC_Utils_Ex::gfPrintLog('invalid access :login_check.php $POST["url"]=' . $_POST['url']);
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR);
        }

        $objCustomer = new SC_Customer();
        // クッキー管理クラス
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);
        // パラメータ管理クラス
        $objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $objFormParam->setParam($_POST);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'login':
            $objFormParam->toLower('login_email');
            $arrErr = $objFormParam->checkError();
            $arrForm =  $objFormParam->getHashArray();
            // クッキー保存判定
            if ($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
                $objCookie->setCookie('login_email', $_POST['login_email']);
            } else {
                $objCookie->setCookie('login_email', '');
            }

            if(count($arrErr) == 0) {
                if($objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
                    header("Location: " . $_POST['url']); // FIXME
                    exit;
                } else {
                    $objQuery = new SC_Query;
                    $where = "email ILIKE ? AND status = 1 AND del_flg = 0";
                    $ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));

                    if($ret > 0) {
                        sfDispSiteError(TEMP_LOGIN_ERROR);
                    } else {
                        sfDispSiteError(SITE_LOGIN_ERROR);
                    }
                }
            } else {
                // 入力エラーの場合、元のアドレスに戻す。
                header("Location: " . $_POST['url']);// FIXME
                exit;
            }
            break;
        case 'logout':
            // ログイン情報の解放
            $objCustomer->EndSession();
            $mypage_url_search = strpos('.'.$_POST['url'], "mypage");
            //マイページログイン中はログイン画面へ移行
            if ($mypage_url_search == 2){
                header("Location: /mypage/login.php");// FIXME
            }else{
                header("Location: " . $_POST['url']);// FIXME
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
        global $objFormParam;
        $objFormParam->addParam("記憶する", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("メールアドレス", "login_email", STEXT_LEN, "a", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("パスワード", "login_pass", STEXT_LEN, "", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }

    /* POSTされるURLのチェック*/
    function lfIsValidURL() {
        $site_url  = sfIsHTTPS() ? SSL_URL : SITE_URL;
        $check_url = trim($_POST['url']);

        // ローカルドメインチェック
        if (!preg_match("|^$site_url|", $check_url) && !preg_match("|^/|", $check_url)) {
            return false;
        }

        // 改行コード(CR・LF)・NULLバイトチェック
        $pattern = '/\r|\n|\0|%0D|%0A|%00/';
        if (preg_match_all($pattern, $check_url, $matches) > 0) {
            return false;
        }

        return true;
    }
}
?>
