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

/**
 * モジュール設定スクリプトをロードする。
 * GETのクエリにmodule_idを渡す。
 *
 * 管理画面から呼び出すことを想定しているので、
 * 認証は外さないこと
 */

require_once 'require.php';

// 認証可否の判定
SC_Utils::sfIsSuccess(new SC_Session());

$module_id = isset($_GET['module_id']) ? $_GET['module_id'] : null;

if (!empty($module_id) && is_numeric($module_id)) {

    GC_Utils::gfPrintLog('loading module ====> module_id = ' . $module_id);

    $objQuery =& SC_Query_Ex::getSingletonInstance();
    $arrRet = $objQuery->select('module_code', 'dtb_module', 'module_id = ?', array($module_id));

    if (isset($arrRet[0]['module_code'])) {
        $config_path = MODULE_REALDIR . $arrRet[0]['module_code'] . '/config.php';

        if (file_exists($config_path)) {
            require_once $config_path;
            exit;
        } else {
            die("モジュールの取得に失敗しました: $config_path");
        }
    } else {
        die("モジュールが存在しません: module_id => $module_id");
    }
}
