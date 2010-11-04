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

if (!defined("CLASS_PATH")) {
    /** クラスパス */
    define("CLASS_PATH", DATA_PATH . "class/");
}

if (!defined("CLASS_EX_PATH")) {
    /** クラスパス */
    define("CLASS_EX_PATH", DATA_PATH . "class_extends/");
}

if (!defined("CACHE_PATH")) {
    /** キャッシュ生成ディレクトリ */
    define("CACHE_PATH", DATA_PATH . "cache/");
}

require_once(CLASS_EX_PATH . "SC_Initial_Ex.php");
// アプリケーション初期化処理
$objInit = new SC_Initial_Ex();
$objInit->init();
?>
