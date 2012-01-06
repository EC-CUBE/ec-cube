<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_REALDIR . 'session/SC_SessionFactory.php';

/**
 * セッション維持の方法にCookieを使用するクラス.
 *
 * このクラスを直接インスタンス化しないこと.
 * 必ず SC_SessionFactory クラスを経由してインスタンス化する.
 * また, SC_SessionFactory クラスの関数を必ずオーバーライドしている必要がある.
 *
 * @package SC_SessionFactory
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_SessionFactory_UseCookie extends SC_SessionFactory {

    // }}}
    // {{{ functions

    /**
     * セッションパラメーターの指定
     * ・ブラウザを閉じるまで有効
     * ・すべてのパスで有効
     *   FIXME 多分、同一ホスト名に複数の EC-CUBE をインストールした場合に望ましくない状態である。特段の事由がなければ、アプリケーションルートを指定すべきだし、あればコメントに残すべき。
     * ・同じドメイン間で共有
     **/
    function initSession() {
        ini_set('session.cache_limiter', 'none');
        if (session_id() === "") {
            session_set_cookie_params(0, "/", DOMAIN_NAME);
            if (!ini_get("session.auto_start")) {
                // セッション開始
                session_start();
            }
        }
    }

    /**
     * Cookieを使用するかどうか
     *
     * @return boolean 常に true を返す
     */
    function useCookie() {
        return true;
    }
}
/*
 * Local variables:
 * coding: utf-8
 * End:
 */
?>
