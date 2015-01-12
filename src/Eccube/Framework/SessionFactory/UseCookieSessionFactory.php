<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework\SessionFactory;

use Eccube\Application;
use Eccube\Framework\SessionFactory;

/**
 * セッション維持の方法にCookieを使用するクラス.
 *
 * このクラスを直接インスタンス化しないこと.
 * 必ず SessionFactory クラスを経由してインスタンス化する.
 * また, SessionFactory クラスの関数を必ずオーバーライドしている必要がある.
 *
 * @package SessionFactory
 * @author LOCKON CO.,LTD.
 */
class UseCookieSessionFactory extends SessionFactory
{
    /**
     * セッションパラメーターの指定
     * ・ブラウザを閉じるまで有効
     * ・EC-CUBE ルート配下で有効
     * ・同じドメイン間で共有
     * FIXME セッションキーのキーが PHP デフォルトのため、上位ディレクトリーで定義があると、その値で動作すると考えられる。
     **/
    public function initSession()
    {
        parent::initSession();

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
    public function useCookie()
    {
        return true;
    }
}
