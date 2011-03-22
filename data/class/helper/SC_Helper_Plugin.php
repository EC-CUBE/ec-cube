<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
class SC_Helper_Plugin{

    /**
     * enableかどうかを判別する
     * インスタンス化
     */
    function load(&$lcpage){
        //データベースからクラス名を読み込む
        $objQuery = new SC_Query_Ex();
        $col = "*";
        $table = "dtb_plugin";
        $where = "enable = 1 AND del_flg = 0";
        $arrRet = $objQuery->select($col, $table, $where);
        $class_name = get_class($lcpage);

        // 実行されたぺーじ
        // 現在のページで使用するプラグインが存在するかどうかを検証する
        foreach ($arrRet as $plugins){
            // プラグインを稼働させるクラス名のリストを取得する
            // プラグインのディレクトリ内の設定ファイルを参照する
            $plugin_name = $plugins['name'];
            $plugin_class_name = $plugins['class_name'];
            require_once DATA_REALDIR."plugin/{$plugin_name}/{$plugin_class_name}.php";

            $code_str = "\$is_enable = {$plugin_class_name}::isEnable(\$class_name);";
            eval($code_str);
            if ($is_enable) {
                $arrPluginList[] = $plugin_class_name;
            }
        }
        return $arrPluginList;
    }

    function preProcess(&$lcpage){
        //プラグインの名前を判別してページ内で有効なプラグインがあれば実行する
        $arrPluginList = SC_Helper_Plugin_Ex::load($lcpage);
       if(count($arrPluginList) > 0){
            foreach ($arrPluginList as $key => $value){
                $instance = new $value;
                $instance->preProcess($lcpage);
            }
        }
        return $lcpage;
    }

    /* 読み込んだプラグインの実行用メソッド
     *
     */
    function process(&$lcpage){
        //プラグインの名前を判別してページ内で有効なプラグインがあれば実行する
        $arrPluginList = SC_Helper_Plugin_Ex::load($lcpage);
        if(count($arrPluginList) > 0){
            foreach ($arrPluginList as $key => $value){
                $instance = new $value;
                $instance->process($lcpage);
            }
        }
        return $lcpage;
    }

    /**
     * 稼働中のプラグインを取得する。
     */
    function getEnablePlugin(){
        $objQuery = new SC_Query_Ex();
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'enable = 1 AND del_flg = 0';
        $arrRet = $objQuery->select($col,$table,$where);
        return $arrRet;
    }

    /**
     * インストールされているプラグインを取得する。
     */
    function getAllPlugin(){
        $objQuery = new SC_Query_Ex();
        $col = '*';
        $table = 'dtb_plugin';
        $where = 'del_flg = 0';
        $arrRet = $objQuery->select($col,$table,$where);
        return $arrRet;
    }

    function getFilesystemPlugins(){
        $plugin_dir = DATA_REALDIR."/plugin/";
        if($dh = opendir($plugin_dir)){
            while(($file = readdir($dh) !== false)){
                if(is_dir($plugin_dir."/".$file)){
                }
            }
        }
    }
}
