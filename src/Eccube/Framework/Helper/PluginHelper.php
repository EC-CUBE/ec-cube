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

namespace Eccube\Framework\Helper;

use Eccube\Application;
use Eccube\Framework\Query;
use Eccube\Framework\Util\GcUtils;
use Eccube\Framework\Plugin\PluginUtil;
use Eccube\Framework\Helper\HandleErrorHelper;

/**
 * プラグインのヘルパークラス.
 *
 * @package Helper
 */
class PluginHelper
{
    // プラグインのインスタンスの配列.
    public $arrPluginInstances = array();
    // プラグインのアクションの配列.
    public $arrRegistedPluginActions = array();
    // プラグインのIDの配列.
    public $arrPluginIds = array();
    // HeadNaviブロックの配列
    public $arrHeadNaviBlocsByPlugin = array();

    /**
     * 有効なプラグインのロード. プラグインエンジンが有効になっていない場合は
     * プラグインエンジン自身のインストール処理を起動する
     *
     * @return void
     */
    public function load($plugin_activate_flg = true)
    {
        if (!defined('CONFIG_REALFILE') || !file_exists(CONFIG_REALFILE)) return; // インストール前
        if (GcUtils::isInstallFunction()) return; // インストール中
        if ($plugin_activate_flg === false) return;
        // 有効なプラグインを取得
        $arrPluginDataList = PluginUtil::getEnablePlugin();
        // pluginディレクトリを取得
        $arrPluginDirectory = PluginUtil::getPluginDirectory();
        foreach ($arrPluginDataList as $arrPluginData) {
            // プラグイン本体ファイル名が取得したプラグインディレクトリ一覧にある事を確認
            if (array_search($arrPluginData['plugin_code'], $arrPluginDirectory) !== false) {
                $plugin_file_path = PLUGIN_UPLOAD_REALDIR . $arrPluginData['plugin_code'] . '/' . $arrPluginData['class_name'] . '.php';
                // プラグイン本体ファイルが存在しない場合
                if (!file_exists($plugin_file_path)) {
                    // エラー出力
                    $msg = 'プラグイン本体ファイルが存在しない。当該プラグインを無視して続行する。';
                    $msg .= 'ファイル=' . var_export($plugin_file_path, true) . '; ';
                    trigger_error($msg, E_USER_WARNING);
                    // 次のプラグインへ続行
                    continue 1;
                }
                // プラグイン本体ファイルをrequire.
                require_once $plugin_file_path;

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
     * PluginHelper オブジェクトを返す（Singletonパターン）
     *
     * @return PluginHelper PluginHelperオブジェクト
     */
    public static function getSingletonInstance($plugin_activate_flg = true)
    {
        if (!isset($GLOBALS['_PluginHelper_instance'])) {
            // プラグインのローダーがDB接続を必要とするため、
            // Queryインスタンス生成後のみオブジェクトを生成する。
            if (is_null(Query::getPoolInstance())) {
                return null;
            }

            $GLOBALS['_PluginHelper_instance'] = new static();
            $GLOBALS['_PluginHelper_instance']->load($plugin_activate_flg);
        }

        return $GLOBALS['_PluginHelper_instance'];
    }

    /**
     * プラグイン実行
     *
     * @param  string $hook_point フックポイント
     * @param  array  $arrArgs    コールバック関数へ渡す引数
     * @return void
     */
    public function doAction($hook_point, $arrArgs = array())
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
     * @param Object $objPlugin     プラグインのインスタンス
     * @param string $hook_point    スーパーフックポイント
     * @param string $function_name 実行する関数名
     * @param string $priority      実行順
     */
    public function registerSuperHookPoint($objPlugin, $hook_point, $function_name, $priority)
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
     * @param string $priority  実行順
     */
    public function registerLocalHookPoint($objPlugin, $priority)
    {
        // ローカルプラグイン関数を定義しているかを検証.
        if (method_exists($objPlugin, 'register') === true) {
            // アクションの登録（プラグイン側に記述）
            $objPluginHelper = static::getSingletonInstance();
            $objPlugin->register($objPluginHelper, $priority);
        }
    }

    /**
     * プラグイン コールバック関数を追加する
     *
     * @param  string   $hook_point フックポイント名
     * @param  callback $function   コールバック関数名
     * @param  integer   $priority   同一フックポイント内での実行優先度
     * @return boolean  成功すればtrue
     */
    public function addAction($hook_point, $function, $priority = 0)
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
     * @param  string   $hook_point フックポイント名
     * @param  callback $function   コールバック関数名
     * @param  integer  $priority   同一フックポイント内での実行優先度
     * @return string   コールバック関数を一意に識別するID
     */
    public function makeActionUniqueId($hook_point, $function, $priority)
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
                         ? count((array) $this->arrRegistedPluginActions[$hook_point][$priority])
                         : $filter_id_count;
                $function[0]->wp_filter_id = $filter_id_count;
                ++$filter_id_count;

                return $obj_idx;
            }
        } elseif (is_string($function[0])) {
            return $function[0].$function[1];
        }
    }

    /**
     * ブロックの配列から有効でないpluginのブロックを除外して返します.
     *
     * @param  array $arrBlocs プラグインのインストールディレクトリ
     * @return array $arrBlocsサイトルートからメディアディレクトリへの相対パス
     */
    public function getEnableBlocs($arrBlocs)
    {
        foreach ($arrBlocs as $key => $value) {
            // 有効なpluginのブロック以外.
            if (!in_array($value['plugin_id'], $this->arrPluginIds)) {
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
     * @param  string $url PHPファイルのURL
     * @return void
     */
    public function setHeadNavi($url)
    {
        $this->arrHeadNaviBlocsByPlugin[$url] = TARGET_ID_HEAD;
    }

    /**
     * PHPのURLをテンプレートのヘッダに追加する
     *
     * @param  array|null $arrBlocs 配置情報を含めたブロックの配列
     * @return void
     */
    public function setHeadNaviBlocs(&$arrBlocs)
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
     * @param  string  $hook_point          hook point
     * @param  SiteView[]   $arrArgs             argument passing to callback function
     * @param  boolean $plugin_activate_flg
     * @return void
     */
    public static function hook($hook_point, $arrArgs = array(), $plugin_activate_flg = PLUGIN_ACTIVATE_FLAG)
    {
        // エラー処理中は実行しない
        if (HandleErrorHelper::$under_error_handling) {
            return;
        }

        $objPlugin = static::getSingletonInstance($plugin_activate_flg);

        // 以前、エラー処理中に (オブジェクトではない) false に対し、doAction をコールする不具合があった。(#1971, #2551)
        // 現在、そういった状況は回避している認識だが、念のため同様の状況が発生した場合、ログを残し、実行しない。
        if (!is_object($objPlugin)) {
            // XXX 致命的エラーの処理中だと、この方法ではログが残らない模様。実質的に問題無いと考えている。
            trigger_error('プラグインの処理で意図しない状況が発生しました。', E_USER_WARNING);
            return;
        }

        $objPlugin->doAction($hook_point, $arrArgs);
    }
}
