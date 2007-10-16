<?php
/**
 * モジュール設定スクリプトをロードする。
 * GETのクエリにmodule_idを渡す。
 *
 * 管理画面から呼び出すことを想定しているので、
 * 認証は外さないこと
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once 'require.php';

// 認証可否の判定
SC_Utils::sfIsSuccess(new SC_Session());

$module_id = isset($_GET['module_id']) ? $_GET['module_id'] : null;

if(!empty($module_id) && is_numeric($module_id)) {

    GC_Utils::gfPrintLog("loading module ====> module_id = " . $module_id);

    $objQuery = new SC_Query();
    $arrRet = $objQuery->select("module_name", "dtb_module2", "module_id = ?", array($module_id));

    if (isset($arrRet[0]['module_name'])) {
        $config_path = MODULE_PATH . $arrRet[0]['module_name'] . '/config.php';

        if (file_exists($config_path)) {
            require_once $config_path;
            exit;
        } else {
            die("モジュールの取得に失敗しました: $path");
        }
    } else {
        die("モジュールが存在しません: module_id => $module_id");
    }
}
?>
