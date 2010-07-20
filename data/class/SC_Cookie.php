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

/* 日時表示用クラス */
class SC_Cookie {

    var $expire;

    // コンストラクタ
    function SC_Cookie($day = 365) {
        // 有効期限
        $this->expire = time() + ($day * 24 * 3600);
    }

    // クッキー書き込み
    function setCookie($key, $val) {
        setcookie($key, $val, $this->expire, "/", DOMAIN_NAME);
    }

    // クッキー取得
    function getCookie($key) {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }
}
?>
