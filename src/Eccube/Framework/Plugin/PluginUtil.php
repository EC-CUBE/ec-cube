<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework\Plugin;

use Eccube\Application;
use Eccube\Framework\Query;

// プラグインのユーティリティクラス.
class PluginUtil
{
    /**
     * 稼働中のプラグインを取得する。
     */
    public function getEnablePlugin()
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'enable = 1';
        // XXX 2.11.0 互換のため
        $arrCols = $objQuery->listTableFields($table);
        if (in_array('priority', $arrCols)) {
            $objQuery->setOrder('priority DESC, plugin_id ASC');
        }
        $arrRet = $objQuery->select($col, $table, $where);

        // プラグインフックポイントを取得.
        $max = count($arrRet);
        for ($i = 0; $i < $max; $i++) {
            $plugin_id = $arrRet[$i]['plugin_id'];
            $arrHookPoint = static::getPluginHookPoint($plugin_id);
            $arrRet[$i]['plugin_hook_point'] = $arrHookPoint;
        }

        return $arrRet;
    }

    /**
     * インストールされているプラグインを取得する。
     *
     * @return array $arrRet インストールされているプラグイン.
     */
    public function getAllPlugin()
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $table = 'dtb_plugin';
        // XXX 2.11.0 互換のため
        $arrCols = $objQuery->listTableFields($table);
        if (in_array('priority', $arrCols)) {
            $objQuery->setOrder('plugin_id ASC');
        }
        $arrRet = $objQuery->select($col, $table);

        return $arrRet;
    }

    /**
     * プラグインIDをキーにプラグインを取得する。
     *
     * @param  int   $plugin_id プラグインID.
     * @return array プラグインの基本情報.
     */
    public function getPluginByPluginId($plugin_id)
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'plugin_id = ?';
        $plugin = $objQuery->getRow($col, $table, $where, array($plugin_id));

        return $plugin;
    }

    /**
     * プラグインコードをキーにプラグインを取得する。
     *
     * @param  string $plugin_code プラグインコード.
     * @return array  プラグインの基本情報.
     */
    public function getPluginByPluginCode($plugin_code)
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'plugin_code = ?';
        $plugin = $objQuery->getRow($col, $table, $where, array($plugin_code));

        return $plugin;
    }

    /**
     * プラグインIDをキーにプラグインを削除する。
     *
     * @param  string $plugin_id プラグインID.
     * @return array  プラグインの基本情報.
     */
    public function deletePluginByPluginId($plugin_id)
    {
        $objQuery = Application::alias('eccube.query');
        $where = 'plugin_id = ?';
        $objQuery->delete('dtb_plugin', $where, array($plugin_id));
        $objQuery->delete('dtb_plugin_hookpoint', $where, array($plugin_id));
    }

    /**
     * プラグインディレクトリの取得
     *
     * @return array $arrPluginDirectory
     */
    public function getPluginDirectory()
    {
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
     * @param  integer $plugin_id
     * @param  integer $use_type  1=有効のみ 2=無効のみ 3=全て
     * @return array   フックポイントの一覧
     */
    public function getPluginHookPoint($plugin_id, $use_type = 1)
    {
        $objQuery = Application::alias('eccube.query');
        $cols = '*';
        $from = 'dtb_plugin_hookpoint';
        $where = 'plugin_id = ?';
        switch ($use_type) {
            case 1:
                $where .= ' AND use_flg = 1';
                break;

            case 2:
                $where .= ' AND use_flg = 0';
                break;

            case 3:
            default:
                break;
        }

        return $objQuery->select($cols, $from, $where, array($plugin_id));
    }

    /**
     *  プラグインフックポイントを取得する.
     *
     * @param  integer $use_type 1=有効のみ 2=無効のみ 3=全て
     * @return array   フックポイントの一覧
     */
    public function getPluginHookPointList($use_type = 3)
    {
        $objQuery = Application::alias('eccube.query');
        $objQuery->setOrder('hook_point ASC, priority DESC');
        $cols = 'dtb_plugin_hookpoint.*, dtb_plugin.priority, dtb_plugin.plugin_name';
        $from = 'dtb_plugin_hookpoint LEFT JOIN dtb_plugin USING(plugin_id)';
        switch ($use_type) {
            case 1:
                $where = 'enable = 1 AND use_flg = 1';
                break;

            case 2:
                $where = 'enable = 1 AND use_flg = 0';
                break;

            case 3:
            default:
                $where = '';
                break;
        }

        return $objQuery->select($cols, $from, $where);
        //$arrList = array();
        //foreach ($arrRet AS $key=>$val) {
        //    $arrList[$val['hook_point']][$val['plugin_id']] = $val;
        //}
        //return $arrList;
    }

    /**
     * プラグイン利用に必須のモジュールチェック
     *
     * @param  string $key エラー情報を格納するキー
     * @return array  $arrErr エラー情報を格納した連想配列.
     */
    public function checkExtension($key)
    {
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

    /**
     * フックポイントのON/OFF変更
     *
     * @param  intger $plugin_hookpoint_id フックポイントID
     * @return bolean $use_flg：1=ON、0=OFF
     */
    public function setPluginHookPointChangeUse($plugin_hookpoint_id, $use_flg = 0)
    {
        $objQuery = Application::alias('eccube.query');
        $sqlval['use_flg'] = $use_flg;
        $objQuery->update('dtb_plugin_hookpoint', $sqlval, 'plugin_hookpoint_id = ?', array($plugin_hookpoint_id));
    }

    /**
     * フックポイントで衝突する可能性のあるプラグインを判定.メッセージを返します.
     *
     * @param  int    $plugin_id プラグインID
     * @return string $conflict_alert_message メッセージ
     */
    public function checkConflictPlugin($plugin_id = '')
    {
        // フックポイントを取得します.
        $where = 'T1.hook_point = ? AND NOT T1.plugin_id = ? AND T2.enable = ?';
        if ($plugin_id > 0) {
            $hookPoints = static::getPluginHookPoint($plugin_id, '');
        } else {
            $hookPoints = static::getPluginHookPointList(1);
            $where .= ' AND T1.use_flg = 1';
        }

        $conflict_alert_message = '';
        $arrConflictPluginName = array();
        $arrConflictHookPoint = array();
        $objQuery = Application::alias('eccube.query');
        $objQuery->setGroupBy('T1.hook_point, T1.plugin_id, T2.plugin_name');
        $table = 'dtb_plugin_hookpoint AS T1 LEFT JOIN dtb_plugin AS T2 ON T1.plugin_id = T2.plugin_id';
        foreach ($hookPoints as $hookPoint) {
            // 競合するプラグインを取得する,
            $conflictPlugins = $objQuery->select('T1.hook_point, T1.plugin_id, T2.plugin_name', $table, $where, array($hookPoint['hook_point'], $hookPoint['plugin_id'], PLUGIN_ENABLE_TRUE));

            // プラグイン名重複を削除する為、専用の配列に格納し直す.
            foreach ($conflictPlugins as $conflictPlugin) {
                // プラグイン名が見つからなければ配列に格納
                if (!in_array($conflictPlugin['plugin_name'], $arrConflictPluginName)) {
                    $arrConflictPluginName[] = $conflictPlugin['plugin_name'];
                }
                // プラグイン名が見つからなければ配列に格納
                if (!in_array($conflictPlugin['hook_point'], $arrConflictHookPoint)) {
                    $arrConflictHookPoint[] = $conflictPlugin['hook_point'];
                }
            }
        }

        if ($plugin_id > 0) {
            // メッセージをセットします.
            foreach ($arrConflictPluginName as $conflictPluginName) {
                $conflict_alert_message .= '* ' .  $conflictPluginName . 'と競合する可能性があります。<br/>';
            }

            return $conflict_alert_message;
        } else {
            return $arrConflictHookPoint;
        }
    }

}
