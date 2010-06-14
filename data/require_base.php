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

$require_base_php_dir = realpath(dirname( __FILE__));

// アプリケーション初期化処理
require_once($require_base_php_dir . "/app_initial.php");

// モジュールの読み込み
require_once($require_base_php_dir . "/include/module.inc");

// 各種クラス読み込み
require_once($require_base_php_dir . "/require_classes.php");

// TODO プラグイン読み込み
include_once($require_base_php_dir . "/require_plugin.php");

// セッションハンドラ開始
$objSession = new SC_Helper_Session_Ex();

// インストールチェック
SC_Utils_Ex::sfInitInstall();

// セッション初期化・開始
require_once CLASS_PATH . 'session/SC_SessionFactory.php';
$sessionFactory = SC_SessionFactory::getInstance();
$sessionFactory->initSession();

// 絵文字変換 (除去) フィルターを組み込む。
ob_start(array('SC_MobileEmoji', 'handler'));

?>
