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

// プラグインのユーティリティクラス.
class SC_Plugin_Util {

    /**
     * 稼働中のプラグインを取得する。
     */
    function getEnablePlugin() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'enable = 1';
        // XXX 2.11.0 互換のため
        $arrCols = $objQuery->listTableFields($table);
        if (in_array('priority', $arrCols)) {
            $objQuery->setOrder('priority DESC, plugin_id ASC');
        }
        $arrRet = $objQuery->select($col,$table,$where);

        // プラグインフックポイントを取得.
        $max = count($arrRet);
        for ($i = 0; $i < $max; $i++) {
            $plugin_id = $arrRet[$i]['plugin_id'];
            $arrHookPoint = SC_Plugin_Util::getPluginHookPoint($plugin_id);
            $arrRet[$i]['plugin_hook_point'] = $arrHookPoint;
        }
        return $arrRet;
    }

    /**
     * インストールされているプラグインを取得する。
     * 
     * @return array $arrRet インストールされているプラグイン.
     */
    function getAllPlugin() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = '*';
        $table = 'dtb_plugin';
        // XXX 2.11.0 互換のため
        $arrCols = $objQuery->listTableFields($table);
        if (in_array('priority', $arrCols)) {
            $objQuery->setOrder('plugin_id ASC');
        }
        $arrRet = $objQuery->select($col,$table);
        return $arrRet;
    }

    /**
     * プラグインIDをキーにプラグインを取得する。
     * 
     * @param int $plugin_id プラグインID.
     * @return array プラグインの基本情報.
     */
    function getPluginByPluginId($plugin_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'plugin_id = ?';
        $plugin = $objQuery->getRow($col, $table, $where, array($plugin_id));
        return $plugin;
    }


    /**
     * プラグインコードをキーにプラグインを取得する。
     * 
     * @param string $plugin_code プラグインコード.
     * @return array プラグインの基本情報.
     */
    function getPluginByPluginCode($plugin_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'plugin_code = ?';
        $plugin = $objQuery->getRow($col, $table, $where, array($plugin_code));
        return $plugin;
    }

    /**
     * プラグインIDをキーにプラグインを削除する。
     * 
     * @param string $plugin_id プラグインID.
     * @return array プラグインの基本情報.
     */
    function deletePluginByPluginId($plugin_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = 'plugin_id = ?';
        $objQuery->delete('dtb_plugin', $where, array($plugin_id));
        $objQuery->delete('dtb_plugin_hookpoint', $where, array($plugin_id));
    }

    /**
     * プラグインディレクトリの取得
     *
     * @return array $arrPluginDirectory
     */
    function getPluginDirectory() {
        $arrPluginDirectory = array();
        if (is_dir(PLUGIN_UPLOAD_REALDIR)) {
            if ($dh = opendir(PLUGIN_UPLOAD_REALDIR)) {
                while (($pluginDirectory = readdir($dh)) !== false) {
                    $arrPluginDirectory[] = $pluginDirectory;
                }
                closedir($dh);
            }
        }
        return $arrPluginDirectory;
    }

    /**
     * プラグインIDをキーに, プラグインフックポイントを取得する.
     *
     * @param integer $plugin_id
     * @return array フックポイントの一覧
     */
    function getPluginHookPoint($plugin_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $cols = '*';
        $from = 'dtb_plugin_hookpoint';
        $where = 'plugin_id = ?';
        return $objQuery->select($cols, $from, $where, array($plugin_id));
    }

    /**
     * プラグイン利用に必須のモジュールチェック
     *
     * @param string $key  エラー情報を格納するキー
     * @return array $arrErr エラー情報を格納した連想配列.
     */
    function checkExtension($key) {
        // プラグイン利用に必須のモジュール
        // 'EC-CUBEバージョン' => array('モジュール名')
        $arrRequireExtension = array(
                                     '2.12.0' => array('dom'),
                                     '2.12.1' => array('dom'),
                                     '2.12.2' => array('dom')
                                    );
        // 必須拡張モジュールのチェック
        $arrErr = array();
        if (is_array($arrRequireExtension[ECCUBE_VERSION])) {
            foreach ($arrRequireExtension[ECCUBE_VERSION] AS $val) {
                if (!extension_loaded($val)) {
                    $arrErr[$key] .= "※ プラグインを利用するには、拡張モジュール「${val}」が必要です。<br />";
                }
            }
        }
        return $arrErr;
    }
}
