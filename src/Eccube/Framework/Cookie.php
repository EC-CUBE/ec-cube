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

namespace Eccube\Framework;

/**
 * クッキー用クラス
 *
 */
class Cookie
{
    public $expire;

    // コンストラクタ
    public function __construct($day = COOKIE_EXPIRE)
    {
        // 有効期限
        $this->expire = time() + ($day * 24 * 3600);
    }

    // クッキー書き込み

    /**
     * @param string $key
     */
    public function setCookie($key, $val)
    {
        setcookie($key, $val, $this->expire, ROOT_URLPATH, DOMAIN_NAME);
    }

    /**
     * クッキー取得
     *
     * EC-CUBE をURLパスルート以外にインストールしている場合、上位ディレクトリの値も(劣後ではあるが)取得する点に留意。
     * @param string $key
     */
    public function getCookie($key)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }
}
