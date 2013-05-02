<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
class SC_SessionFactory_UseCookie extends SC_SessionFactory_Ex {

    // }}}
    // {{{ functions

    /**
     * セッションパラメーターの指定
     * ・ブラウザを閉じるまで有効
     * ・EC-CUBE ルート配下で有効
     * ・同じドメイン間で共有
     * FIXME セッションキーのキーが PHP デフォルトのため、上位ディレクトリーで定義があると、その値で動作すると考えられる。
     **/
    function initSession() {
        ini_set('session.cache_limiter', 'none');
        // (session.auto_start などで)セッションが開始されていた場合に備えて閉じる。(FIXME: 保存する必要はない。破棄で良い。)
        session_write_close();
        session_set_cookie_params(0, ROOT_URLPATH, DOMAIN_NAME);
        // セッション開始
        // FIXME EC-CUBE をネストしてインストールした場合を考慮して、一意とすべき
        session_name('ECSESSID');
        session_start();
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
