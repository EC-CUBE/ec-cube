<!-- -*- coding: utf-8 -*- -->
<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/error/LC_Page_Error.php");

/**
 * エラー表示のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Error.php 15141 2007-07-27 10:59:11Z nanasess $
 */
class LC_Page_Error_DispError extends LC_Page_Error {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'login_error.tpl';
    }

    /**
     * Page のプロセス。
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();

        switch ($this->type) {
            case LOGIN_ERROR:
                $this->tpl_error="ＩＤまたはパスワードが正しくありません。<br />もう一度ご確認のうえ、再度入力してください。";
                break;
            case ACCESS_ERROR:
                $this->tpl_error="ログイン認証の有効期限切れの可能性があります。<br />もう一度ご確認のうえ、再度ログインしてください。";
                break;
            case AUTH_ERROR:
                $this->tpl_error="このファイルにはアクセス権限がありません。<br />もう一度ご確認のうえ、再度ログインしてください。";
                break;
            case INVALID_MOVE_ERRORR:
                $this->tpl_error="不正なページ移動です。<br />もう一度ご確認のうえ、再度入力してください。";
                break;
            default:
                $this->tpl_error="エラーが発生しました。<br />もう一度ご確認のうえ、再度ログインしてください。";
                break;
        }

        $objView->assignobj($this);
        $objView->display(LOGIN_FRAME);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
