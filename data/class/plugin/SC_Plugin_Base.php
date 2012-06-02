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
 *
 *
 */

/**
 * プラグインの基底クラス
 *
 * @package Plugin
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
abstract class SC_Plugin_Base {

    protected $arrSelfInfo;

    /**
     * コンストラクタ
     *
     * @param array $arrSelfInfo 自身のプラグイン情報
     * @return void
     */
    function __construct(array $arrSelfInfo) {
        $this->arrSelfInfo = $arrSelfInfo;
    }
    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    abstract function install($arrPlugin);

    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    abstract function uninstall($arrPlugin);

    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    abstract function enable($arrPlugin);

    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    abstract function disable($arrPlugin);

    /**
     * プラグインヘルパーへ, コールバックメソッドを登録します.
     *
     * @param object $objPluginHelper
     * @param integer $priority
     */
    function register(SC_Helper_Plugin $objHelperPlugin, $priority) {
        if (isset($this->arrSelfInfo['plugin_hook_point'])) {
            $arrHookPoints = $this->arrSelfInfo['plugin_hook_point'];
            foreach ($arrHookPoints as $hook_point) {
                if (isset($hook_point['callback'])) {
                    $hook_point_name = $hook_point['hook_point'];
                    $callback_name   = $hook_point['callback'];
                    $objHelperPlugin->addAction($hook_point_name, array($this, $callback_name), $priority);
                }
            }
        }
    }

    /**
     * このプラグインのプラグイン情報を返す。
     *
     * @return array $arrSelfInfo 自身のプラグイン情報
     */
    function getPluginInfo() {
        return $this->arrSelfInfo;
    }

}
