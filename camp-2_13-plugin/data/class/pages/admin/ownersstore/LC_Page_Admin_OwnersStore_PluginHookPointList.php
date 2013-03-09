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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * オーナーズストア：プラグイン管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_OwnersStore.php 22567 2013-02-18 10:09:54Z shutta $
 */
class LC_Page_Admin_OwnersStore_PluginHookPointList extends LC_Page_Admin_Ex 
{

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
        $this->tpl_mainpage = 'ownersstore/plugin_hookpoint_list.tpl';
        $this->tpl_subno    = 'index';
        $this->tpl_mainno   = 'ownersstore';
        $this->tpl_maintitle = 'オーナーズストア';
        $this->tpl_subtitle = 'プラグインフックポイント管理';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action()
    {
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        $this->initParam($objFormParam, $mode);
        $objFormParam->setParam($_POST);

        $mode = $this->getMode();
        switch ($mode) {
            // 削除
            case 'uninstall':
                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if ($this->isError($this->arrErr) === false) {
                    $plugin_id = $objFormParam->getValue('plugin_id');
                    $plugin = SC_Plugin_Util_Ex::getPluginByPluginId($plugin_id);
                    $this->arrErr = $this->uninstallPlugin($plugin);
                    if ($this->isError($this->arrErr) === false) {
                        // TODO 全プラグインのインスタンスを保持したまま後続処理が実行されるので、全てのインスタンスを解放する。
                        unset($GLOBALS['_SC_Helper_Plugin_instance']);
                        // コンパイルファイルのクリア処理
                        SC_Utils_Ex::clearCompliedTemplate();
                        $this->tpl_onload = "alert('" . $plugin['plugin_name'] ."を削除しました。');";
                    }
                }
                break;
            // 有効化
            case 'enable':
                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if ($this->isError($this->arrErr) === false) {
                    $plugin_id = $objFormParam->getValue('plugin_id');
                    // プラグイン取得.
                    $plugin = SC_Plugin_Util_Ex::getPluginByPluginId($plugin_id);
                    $this->arrErr = $this->enablePlugin($plugin);
                    if ($this->isError($this->arrErr) === false) {
                        // TODO 全プラグインのインスタンスを保持したまま後続処理が実行されるので、全てのインスタンスを解放する。
                        unset($GLOBALS['_SC_Helper_Plugin_instance']);
                        // コンパイルファイルのクリア処理
                        SC_Utils_Ex::clearCompliedTemplate();
                        $this->tpl_onload = "alert('" . $plugin['plugin_name'] . "を有効にしました。');";
                    }
                }
                break;
            // 無効化
            case 'disable':
                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if ($this->isError($this->arrErr) === false) {
                    $plugin_id = $objFormParam->getValue('plugin_id');
                    // プラグイン取得.
                    $plugin = SC_Plugin_Util_Ex::getPluginByPluginId($plugin_id);
                    $this->arrErr = $this->disablePlugin($plugin);
                    if ($this->isError($this->arrErr) === false) {
                        // TODO 全プラグインのインスタンスを保持したまま後続処理が実行されるので、全てのインスタンスを解放する。
                        unset($GLOBALS['_SC_Helper_Plugin_instance']);
                        // コンパイルファイルのクリア処理
                        SC_Utils_Ex::clearCompliedTemplate();
                        $this->tpl_onload = "alert('" . $plugin['plugin_name'] . "を無効にしました。');";
                    }
                }
                break;
            default:
                break;
        }
        // DBからプラグイン情報を取得
        $arrHookPoint = SC_Plugin_Util_Ex::getPluginHookPointList();

        $this->arrHookPoint = $arrHookPoint;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy()
    {
        parent::destroy();
    }

    /**
     * パラメーター初期化.
     *
     * @param SC_FormParam_Ex $objFormParam
     * @param string $mode モード
     * @return void
     */
    function initParam(&$objFormParam, $mode)
    {
        $objFormParam->addParam('mode', 'mode', INT_LEN, '', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('plugin_id', 'plugin_id', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        if ($mode === 'priority') {
            $objFormParam->addParam('優先度', 'priority', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        }
    }

}
