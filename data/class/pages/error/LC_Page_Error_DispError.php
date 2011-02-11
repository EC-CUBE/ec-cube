<!-- -*- coding: utf-8 -*- -->
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
require_once(CLASS_REALDIR . "pages/error/LC_Page_Error.php");

/**
 * エラー表示のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
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
        $this->tpl_mainpage = 'error.tpl';
        $this->tpl_title = 'ログインエラー';
    }

    /**
     * Page のプロセス。
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }
	
    /**
     * Page のプロセス。
     *
     * @return void
     */
    function action(){
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
