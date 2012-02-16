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
 * プラグインのヘルパークラス.
 *
 * @package Helper
 * @version $Id$
 */
class SC_Helper_Plugin {
    // プラグインのインスタンスの配列.
    var $arrPluginInstances = array();
    // プラグインのアクションの配列.
    var $arrRegistedPluginActions = array();
    // プラグインのIDの配列.
    var $arrPluginIds = array();

    /**
     * 有効なプラグインのロード. プラグインエンジンが有効になっていない場合は
     * プラグインエンジン自身のインストール処理を起動する
     *
     * @return void
     */
    function load() {

        if (!defined('CONFIG_REALFILE') || !file_exists(CONFIG_REALFILE)) return; // インストール前
        if (SC_Utils_Ex::sfIsInstallFunction()) return; // インストール中

        // 有効なプラグインを取得
        $arrPluginDataList = $this->getEnablePlugin();
        // pluginディレクトリを取得
        $arrPluginDirectory = $this->getPluginDirectory();

        foreach ($arrPluginDataList as $arrPluginData) {
            // プラグイン本体ファイル名が取得したプラグインディレクトリ一覧にある事を確認
            if (array_search($arrPluginData['plugin_code'], $arrPluginDirectory) !== false) {
                // プラグイン本体ファイルをrequire.
                require_once PLUGIN_UPLOAD_REALDIR . $arrPluginData['plugin_code'] . '/' . $arrPluginData['plugin_code'] . '.php';

                // プラグインのインスタンス生成.
                $objPlugin = new $arrPluginData['plugin_code']($arrPluginData);
                // メンバ変数にプラグインのインスタンスを登録.
                $this->arrPluginInstances[$arrPluginData['plugin_id']] = $objPlugin;
                $this->arrPluginIds[] = $arrPluginData['plugin_id'];
                // ローカルフックポイントの登録.
                $this->registLocalHookPoint($objPlugin, $arrPluginData['priority']);
                // スーパーフックポイントの登録.
                $this->registSuperHookPoint($objPlugin, HOOK_POINT_PREPROCESS, 'preProcess', $arrPluginData['priority']);
                $this->registSuperHookPoint($objPlugin, HOOK_POINT_PROCESS, 'process', $arrPluginData['priority']);
            }
        }
    }

    /**
     * SC_Helper_Plugin オブジェクトを返す（Singletonパターン）
     *
     * @return object SC_Helper_Pluginオブジェクト
     */
    function getSingletonInstance() {
        if (!isset($GLOBALS['_SC_Helper_Plugin_instance']) || is_null($GLOBALS['_SC_Helper_Plugin_instance'])) {
            $GLOBALS['_SC_Helper_Plugin_instance'] =& new SC_Helper_Plugin_Ex();
            $GLOBALS['_SC_Helper_Plugin_instance']->load();
        }
        return $GLOBALS['_SC_Helper_Plugin_instance'];
    }

    /**
     * プラグイン実行
     *
     * @param string $hook_point フックポイント
     * @param array  $arrArgs    コールバック関数へ渡す引数
     * @return void
     */
    function doAction($hook_point, $arrArgs = array()) {
        if (is_array($arrArgs) === false) {
            array(&$arrArgs);
        }

        if (array_key_exists($hook_point, $this->arrRegistedPluginActions)
            && is_array($this->arrRegistedPluginActions[$hook_point])) {

            krsort($this->arrRegistedPluginActions[$hook_point]);
            foreach ($this->arrRegistedPluginActions[$hook_point] as $priority => $arrFuncs) {

                foreach ($arrFuncs as $func) {
                    if (!is_null($func['function'])) {
                        call_user_func_array($func['function'], $arrArgs);
                    }
                }
            }
        }
    }

    /**
     * 稼働中のプラグインを取得する。
     */
    function getEnablePlugin() {
        $objQuery = new SC_Query_Ex();
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'enable = 1';
        // XXX 2.11.0 互換のため
        $arrCols = $objQuery->listTableFields($table);
        if (in_array('priority', $arrCols)) {
            $objQuery->setOrder('priority DESC, plugin_id ASC');
        }
        $arrRet = $objQuery->select($col,$table,$where);
        return $arrRet;
    }

    /**
     * インストールされているプラグインを取得する。
     * 
     * @return array $arrRet インストールされているプラグイン.
     */
    function getAllPlugin() {
        $objQuery = new SC_Query_Ex();
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
        $objQuery = new SC_Query_Ex();
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
        $objQuery = new SC_Query_Ex();
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'plugin_code = ?';
        $plugin = $objQuery->getRow($col, $table, $where, array($plugin_code));
        return $plugin;
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
     * スーパーフックポイントを登録します.
     * 
     * @param Object $objPlugin プラグインのインスタンス
     * @param string $hook_point スーパーフックポイント
     * @param string $function_name 実行する関数名
     * @param string $priority 実行順
     */
    function registSuperHookPoint($objPlugin, $hook_point, $function_name, $priority) {
        // スーパープラグイン関数を定義しているかを検証.
        if (method_exists($objPlugin, $function_name) === true) {
            // アクションの登録
            $this->addAction($hook_point, array($objPlugin, $function_name), $priority);
        }
    }

    /**
     * ローカルフックポイントを登録します.
     *
     * @param Object $objPlugin プラグインのインスタンス
     * @param string $priority 実行順
     */
    function registLocalHookPoint($objPlugin, $priority) {
        // ローカルプラグイン関数を定義しているかを検証.
        if (method_exists($objPlugin, 'regist') === true) {
            // アクションの登録（プラグイン側に記述）
            $objPluginHelper =& SC_Helper_Plugin::getSingletonInstance();
            $objPlugin->regist($objPluginHelper, $priority);
        }
    }

    /**
     * プラグイン コールバック関数を追加する
     *
     * @param string   $hook_point フックポイント名
     * @param callback $function   コールバック関数名
     * @param string   $priority   同一フックポイント内での実行優先度
     * @return boolean 成功すればtrue
     */
    function addAction($hook_point, $function, $priority) {
        if (!is_callable($function)) {
            // TODO エラー処理;　コール可能な形式ではありません
        }
        $idx = $this->makeActionUniqueId($hook_point, $function, $priority);
        $this->arrRegistedPluginActions[$hook_point][$priority][$idx] = array('function' => $function);
        return true;
    }

    /**
     * コールバック関数を一意に識別するIDの生成
     *
     * @param string   $hook_point フックポイント名
     * @param callback $function   コールバック関数名
     * @param integer  $priority   同一フックポイント内での実行優先度
     * @return string コールバック関数を一意に識別するID
     */
    function makeActionUniqueId($hook_point, $function, $priority) {
        static $filter_id_count = 0;

        if (is_string($function)) {
            return $function;
        }

        if (is_object($function)) {
            $function = array($function, '');
        } else {
            $function = (array) $function;
        }

        if (is_object($function[0])) {
            if (function_exists('spl_object_hash')) {
                return spl_object_hash($function[0]) . $function[1];
            } else {
                $obj_idx = get_class($function[0]).$function[1];
                if ( false === $priority)
                    return false;
                $obj_idx .= isset($this->arrRegistedPluginActions[$hook_point][$priority])
                         ? count((array)$this->arrRegistedPluginActions[$hook_point][$priority])
                         : $filter_id_count;
                $function[0]->wp_filter_id = $filter_id_count;
                ++$filter_id_count;

                return $obj_idx;
            }
        } else if (is_string($function[0])) {
            return $function[0].$function[1];
        }
    }

    /**
     * 全てのテンプレートを再生成する
     *
     * @param boolean $test_mode true の場合、validate のみを行い、実際にテンプレートの再生成は行わない
     * @return void
     */
    function remakeAllTemplates($test_mode = false) {
        $this->load(); // 最新のデータを読み込みなおす
        if (!is_writable(PLUGIN_TMPL_CACHE_REALDIR)) {
            // TODO エラー処理;
            exit;
        }
        // キャッシュテンプレートを削除
        if ($test_mode === false) {
            SC_Utils_Ex::deleteFile(PLUGIN_TMPL_CACHE_REALDIR, false);
        }
        $objTemplateTransformList = SC_Plugin_TemplateTransformList::getSingletonInstance();
        $objTemplateTransformList->init();
        // プラグインのsetTemplateTransformerを実行します.
        foreach ($this->arrPluginInstances as $objPlugin) {
            if (method_exists($objPlugin, 'setTemplateTransformer') === true) {
                // SC_Plugin_TemplateTransformList::arrConfsByTemplatesにトランスフォーム情報をセットします
                $objPlugin->setTemplateTransformer();
            }
        }
        // SC_Plugin_TemplateTransformList::arrConfsByTemplatesにセットされた情報を元にトランスフォームの実行
        $objTemplateTransformList->transformAll($test_mode);
    }

    /**
     * テンプレートキャッシュファイルのフルパスを返す.
     *
     * @param string $tpl_mainpage  返すキャッシュファイルのパスの対象となるテンプレート.
     * @param object $objPage  ページオブジェクト.
     */
    function getPluginTemplateCachePath($objPage) {
        // main_template の差し替え
        if (strpos($objPage->tpl_mainpage, SMARTY_TEMPLATES_REALDIR) === 0) {
            // フルパスで指定された
            $dir = '';
            $default_tpl_mainpage = str_replace(SMARTY_TEMPLATES_REALDIR, '', $objPage->tpl_mainpage);
        } else {
            // フロントページ or 管理画面を判定
            $dir = ($objPage instanceof LC_Page_Admin) ? 'admin/' : TEMPLATE_NAME . '/';
            $default_tpl_mainpage = $objPage->tpl_mainpage;
        }
        return PLUGIN_TMPL_CACHE_REALDIR . $dir . $default_tpl_mainpage;
    }

    /**
     * ブロックの配列から有効でないpluginのブロックを除外して返します.
     *
     * @param array $arrBlocs プラグインのインストールディレクトリ
     * @return array $arrBlocsサイトルートからメディアディレクトリへの相対パス
     */
    function getEnableBlocs($arrBlocs) {
        foreach ($arrBlocs as $key => $value) {
            // 有効なpluginのブロック以外.
            if (!in_array($value['plugin_id'] , $this->arrPluginIds)) {
                // 通常ブロック以外.
                if ($value['plugin_id'] != '') {
                    //　ブロック配列から削除する
                    unset ($arrBlocs[$key]);
                }
            }
        }
        return $arrBlocs;
    }

    /**
     * テンプレートの再生成
     *
     * @return void
     */
    function remakeTemplate() {
        $this->remakeAllTemplates(true);
        $this->remakeAllTemplates();
    }
}
