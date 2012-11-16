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

if (!defined('DATA_REALDIR')) {
    define('DATA_REALDIR', HTML_REALDIR . HTML2DATA_DIR);
}
// PHP4互換用関数読み込み(PHP_Compat)
require_once DATA_REALDIR . 'require_compat.php';
// グローバル関数を読み込み
require_once DATA_REALDIR . 'include/common.php';
// アプリケーション初期化処理
require_once DATA_REALDIR . 'app_initial.php';

// 定数 SAFE が設定されている場合、DBアクセスを回避する。主に、エラー画面を意図する。
if (!defined('SAFE') || !SAFE) {
    // インストール中で無い場合、
    if (!GC_Utils_Ex::isInstallFunction()) {
        // インストールチェック
        SC_Utils_Ex::sfInitInstall();

        // セッションハンドラ開始
        $objSession = new SC_Helper_Session_Ex();

        // セッション初期化・開始
        $sessionFactory = SC_SessionFactory_Ex::getInstance();
        $sessionFactory->initSession();

        /*
         * 管理画面の場合は認証行う.
         * 認証処理忘れ防止のため, LC_Page_Admin::init() 等ではなく, ここでチェックする.
         */
        $objSession->adminAuthorization();
    }
}
