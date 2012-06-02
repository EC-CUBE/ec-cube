<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

require_once DATA_REALDIR . 'module/Compat/Compat.php';

//TODO: このライブラリを使うのが良いのか、PEAR/Crypt_HMAC2を使うべきか検討したが
//      Crypt_HMAC2は5.0.0以上であったため、4.0.0からの動作が可能な下記を引用。

// hash_algos (PHP 5 >= 5.1.2, PECL hash >= 1.1)
// パスワード・リマインダーのハッシュ暗号化に利用
// XXX PHP_Compat::loadFunction('hash_algos'); // include_once のパス相違
if (!function_exists('hash_algos')) {
    require_once DATA_REALDIR . 'module/Compat/Compat/Function/hash_algos.php';
}
// hash_hmac (PHP 5 >= 5.1.2, PECL hash >= 1.1)
// パスワード・リマインダーのハッシュ暗号化に利用
// http://pear.php.net/bugs/bug.php?id=16521 よりPHP_Compat互換仕様のhash関連関数追加
// XXX PHP_Compat::loadFunction('hash_hmac'); // include_once のパス相違
if (!function_exists('hash_hmac')) {
    require_once DATA_REALDIR . 'module/Compat/Compat/Function/hash_hmac.php';
}
