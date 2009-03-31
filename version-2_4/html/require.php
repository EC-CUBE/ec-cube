<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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

$require_php_dir = realpath(dirname( __FILE__));
require_once($require_php_dir . "/define.php");
require_once($require_php_dir . HTML2DATA_DIR . "require_base.php");

// 携帯端末の場合は mobile 以下へリダイレクトする。
if (SC_MobileUserAgent::isMobile()) {
    $url = "";
    if (SC_Utils_Ex::sfIsHTTPS()) {
        $url = MOBILE_SSL_URL;
    } else {
        $url = MOBILE_SITE_URL;
    }

    if (preg_match('|^' . URL_DIR . '(.*)$|', $_SERVER['REQUEST_URI'], $matches)) {
        $path = $matches[1];
    } else {
        $path = '';
    }

    header("Location: ". SC_Utils_Ex::sfRmDupSlash($url . $path));
    exit;
}

?>
