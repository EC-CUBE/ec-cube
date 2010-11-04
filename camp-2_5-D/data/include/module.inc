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

if (!defined('ECCUBE_INSTALL')
 || preg_match('|/install/index.php$|', $_SERVER['PHP_SELF'])) {

    return;
}

// {{{ requires
require_once CLASS_PATH . 'SC_Query.php';
require_once CLASS_EX_PATH . 'db_extends/SC_DB_DBFactory_Ex.php';

/**
 * ダウンロード済みモジュールのinclude.phpを読み込む.
 * テンプレート関数用.
 *
 * クラス化したかったが、グローバル変数をかなり使ってたので断念.
 * そのうち変数名とか衝突しそう...
 *
 * sfPrintEbisTag(), sfPrintAffTag()は今は使用しない関数だが、
 * 互換性維持のため残しておく.
 */

/**
 * 常にinclude.phpを呼び出すモジュールの一覧を$_arrModuleに定義している
 * FIXME dtb_moduleとかに保持する
 * TODO  dtb_module内にカラムを追加して判定するか、include.phpで定義するかに変更する必要有り。
 */
$_arrModule = array(
    'mdl_a8',
    'mdl_moba8',
    'mdl_janet',
    'mdl_opebuilder',
    'mdl_souraku',
    'mdl_ts',
);

$_objQuery = new SC_Query;
$_arrModuleCode = $_objQuery->getCol('dtb_module', 'module_code');

if (is_array($_arrModuleCode)) {
    foreach ($_arrModuleCode as $_moduleCode) {
        $_file = MODULE_PATH . "$_moduleCode/include.php";
        if (in_array($_moduleCode, $_arrModule) && file_exists($_file)) {
            include_once($_file);
        }
    }
}

// グローバル変数は残しておきたくないのでunset
unset(
    $_arrModule, $_objQuery, $_arrModuleCode,
    $_moduleCode, $_file
);

// 互換性保持のため空の関数を残しておく
function sfPrintEbisTag() {}
function sfPrintAffTag() {}
