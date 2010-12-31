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

if (!defined("DATA_FILE_PATH")) {
    define("DATA_FILE_PATH", HTML_FILE_PATH . HTML2DATA_DIR);
}

// アプリケーション初期化処理
require_once(DATA_FILE_PATH . "app_initial.php");

// 各種クラス読み込み
require_once(DATA_FILE_PATH . "require_classes.php");

// インストール中で無い場合、
if (!SC_Utils_Ex::sfIsInstallFunction()) {
    // インストールチェック
    SC_Utils_Ex::sfInitInstall();

    // セッションハンドラ開始
    require_once CLASS_EX_PATH . 'helper_extends/SC_Helper_Session_Ex.php';
    $objSession = new SC_Helper_Session_Ex();

    // セッション初期化・開始
    require_once CLASS_PATH . 'session/SC_SessionFactory.php';
    $sessionFactory = SC_SessionFactory::getInstance();
    $sessionFactory->initSession();

    // プラグインを読み込む
    //require_once(DATA_FILE_PATH . 'require_plugin.php');
}
?>
