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
        $objCustomer = new SC_Customer();
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
            $this->objFormParam->toLower('mypage_login_email');
            $arrErr = $this->objFormParam->checkError();
            $arrForm =  $this->objFormParam->getHashArray();
            // クッキー保存判定
            if ($arrForm['mypage_login_memory'] == "1" && $arrForm['mypage_login_email'] != "") {
                $objCookie->setCookie('login_email', $_POST['mypage_login_email']);
            } else {
                $objCookie->setCookie('login_email', '');
            }

              if($objCustomer->getCustomerDataFromEmailPass($arrForm['mypage_login_pass'], $arrForm['mypage_login_email'])) {
                  $this->sendRedirect($this->getLocation("./index.php"));
                  exit;
              } else {
                  $objQuery = new SC_Query;
                  $where = "email = ? AND status = 1 AND del_flg = 0";
                  $ret = $objQuery->count("dtb_customer", $where, array($arrForm['mypage_login_email']));

                  if($ret > 0) {
                      SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
                  } else {
                      SC_Utils_Ex::sfDispSiteError(SITE_LOGIN_ERROR);
                  }
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
        $this->objFormParam->addParam("メールアドレス", "mypage_login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("パスワード", "mypage_login_pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }
}
?>
