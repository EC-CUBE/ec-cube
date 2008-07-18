<?php
/**
 *
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
$mobile_require_php_dir = realpath(dirname( __FILE__));
require_once($mobile_require_php_dir . "/../define.php");

define('MOBILE_SITE', true);

require_once($mobile_require_php_dir . "/../" . HTML2DATA_DIR . "require_base.php");

// モバイルサイト用の初期処理を実行する。
if (!defined('SKIP_MOBILE_INIT')) {
    $objMobile = new SC_Helper_Mobile_Ex();
    $objMobile->sfMobileInit();
}

// Moba8対応（Moba8パラメータ引き継ぎ）
if (function_exists("sfGetMoba8Param") == TRUE) {
	sfGetMoba8Param($_GET['a8']);
}
?>
