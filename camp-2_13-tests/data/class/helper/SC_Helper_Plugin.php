<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
class SC_Helper_Plugin 
{
    // プラグインのインスタンスの配列.
    var $arrPluginInstances = array();
    // プラグインのアクションの配列.
    var $arrRegistedPluginActions = array();
    // プラグインのIDの配列.
    var $arrPluginIds = array();
    // HeadNaviブロックの配列
    var $arrHeadNaviBlocsByPlugin = array();

    /**
     * 有効なプラグインのロード. プラグインエンジンが有効になっていない場合は
     * プラグインエンジン自身のインストール処理を起動する
     *
     * @return void
     */
    function load($plugin_activate_flg = true)
    {

        if (!defined('CONFIG_REALFILE') || !file_exists(CONFIG_REALFILE)) return; // インストール前
        if (GC_Utils_Ex::isInstallFunction()) return; // インストール中
        if ($plugin_activate_flg === false) return;
        // 有効なプラグインを取得
        $arrPluginDataList = SC_Plugin_Util_Ex::getEnablePlugin();
        // pluginディレクトリを取得
        $arrPluginDirectory = SC_Plugin_Util_Ex::getPluginDirectory();
        foreach ($arrPluginDataList as $arrPluginData) {
            // プラグイン本体ファイル名が取得したプラグインディレクトリ一覧にある事を確認
            if (array_search($arrPluginData['plugin_code'], $arrPluginDirectory) !== false) {
                // プラグイン本体ファイルをrequire.
                require_once PLUGIN_UPLOAD_REALDIR . $arrPluginData['plugin_code'] . '/' . $arrPluginData['class_name'] . '.php';

                // プラグインのインスタンス生成.
                $objPlugin = new $arrPluginData['class_name']($arrPluginData);
                // メンバ変数にプラグインのインスタンスを登録.
                $this->arrPluginInstances[$arrPluginData['plugin_id']] = $objPlugin;
                $this->arrPluginIds[] = $arrPluginData['plugin_id'];
                // ローカルフックポイントの登録.
                $this->registerLocalHookPoint($objPlugin, $arrPluginData['priority']);
                // スーパーフックポイントの登録.
                $this->registerSuperHookPoint($objPlugin, HOOK_POINT_PREPROCESS, 'preProcess', $arrPluginData['priority']);
                $this->registerSuperHookPoint($objPlugin, HOOK_POINT_PROCESS, 'process', $arrPluginData['priority']);
            }
        }
    }

    /**
     * SC_Helper_Plugin オブジェクトを返す（Singletonパターン）
     *
     * @return object SC_Helper_Pluginオブジェクト
     */
    static function getSingletonInstance($plugin_activate_flg = true)
    {
        if (!isset($GLOBALS['_SC_Helper_Plugin_instance'])) {
            // プラグインのローダーがDB接続を必要とするため、
            // SC_Queryインスタンス生成後のみオブジェクトを生成する。
            require_once CLASS_EX_REALDIR . 'SC_Query_Ex.php';
            if (is_null(SC_Query_Ex::getPoolInstance())) {
                return false;
            }

            $GLOBALS['_SC_Helper_Plugin_instance'] = new SC_Helper_Plugin_Ex();
            $GLOBALS['_SC_Helper_Plugin_instance']->load($plugin_activate_flg);
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
    function doAction($hook_point, $arrArgs = array())
    {
        if (is_array($arrArgs) === false) {
            array(&$arrArgs);
        }

        if ($hook_point == 'loadClassFileChange') {
            $arrSaveArgs = $arrArgs;
            $arrClassName = array();
            $arrClassPath = array();
        }

        if (array_key_exists($hook_point, $this->arrRegistedPluginActions)
            && is_array($this->arrRegistedPluginActions[$hook_point])) {

            krsort($this->arrRegistedPluginActions[$hook_point]);
            foreach ($this->arrRegistedPluginActions[$hook_point] as $arrFuncs) {

                foreach ($arrFuncs as $func) {
                    if (!is_null($func['function'])) {
                        if ($hook_point == 'loadClassFileChange') {
                            $classname = $arrSaveArgs[0];
                            $classpath = $arrSaveArgs[1];
                            $arrTempArgs = array(&$classname, &$classpath);

                            call_user_func_array($func['function'], $arrTempArgs);

                            if ($classname !== $arrSaveArgs[0]) {
                                $arrClassName[] = $classname;
                                $arrClassPath[] = $classpath;
                            }
                        } else {
                            call_user_func_array($func['function'], $arrArgs);
                        }
                    }
                }
            }

            if ($hook_point == 'loadClassFileChange') {
                if (count($arrClassName) > 0) {
                    $arrArgs[0] = $arrClassName;
                    $arrArgs[1] = $arrClassPath;
                }
            }
        }
    }

    /**
     * スーパーフックポイントを登録します.
     * 
     * @param Object $objPlugin プラグインのインスタンス
     * @param string $hook_point スーパーフックポイント
     * @param string $function_name 実行する関数名
     * @param string $priority 実行順
     */
    function registerSuperHookPoint($objPlugin, $hook_point, $function_name, $priority)
    {
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
    function registerLocalHookPoint($objPlugin, $priority)
    {
        // ローカルプラグイン関数を定義しているかを検証.
        if (method_exists($objPlugin, 'register') === true) {
            // アクションの登録（プラグイン側に記述）
            $objPluginHelper =& SC_Helper_Plugin::getSingletonInstance();
            $objPlugin->register($objPluginHelper, $priority);
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
    function addAction($hook_point, $function, $priority = 0)
    {
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
    function makeActionUniqueId($hook_point, $function, $priority)
    {
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
     * ブロックの配列から有効でないpluginのブロックを除外して返します.
     *
     * @param array $arrBlocs プラグインのインストールディレクトリ
     * @return array $arrBlocsサイトルートからメディアディレクトリへの相対パス
     */
    function getEnableBlocs($arrBlocs)
    {
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
     * テンプレートのヘッダに追加するPHPのURLをセットする
     *
     * @param string $url PHPファイルのURL
     * @return void
     */
    function setHeadNavi($url)
    {
        $this->arrHeadNaviBlocsByPlugin[$url] = TARGET_ID_HEAD;
    }

    /**
     * PHPのURLをテンプレートのヘッダに追加する
     *
     * @param array|null $arrBlocs  配置情報を含めたブロックの配列
     * @return void
     */
    function setHeadNaviBlocs(&$arrBlocs)
    {
        foreach ($this->arrHeadNaviBlocsByPlugin as $key => $value) {
            $arrBlocs[] = array(
                'target_id' =>$value,
                'php_path' => $key
            );
        }
    }

    /**
     * Utility function to set a hook point.
     *
     * @param string    $hook_point  hook point
     * @param array     $arrArgs     argument passing to callback function
     * @param boolean   $plugin_activate_flg 
     * @return void
     */
    public static function hook($hook_point, $arrArgs = array(), $plugin_activate_flg = PLUGIN_ACTIVATE_FLAG)
    {
        $objPlugin = SC_Helper_Plugin::getSingletonInstance($plugin_activate_flg);
        $objPlugin->doAction($hook_point, $arrArgs);
    }
}
