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
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'ownersstore/plugin_hookpoint_list.tpl';
        $this->tpl_subno    = 'index';
        $this->tpl_mainno   = 'ownersstore';
        $this->tpl_maintitle = 'オーナーズストア';
        $this->tpl_subtitle = 'プラグインフックポイント管理';

        $this->arrUse = array();
        $this->arrUse[1] = 'ON';
        $this->arrUse[0] = 'OFF';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        $this->initParam($objFormParam);
        $objFormParam->setParam($_POST);

        $mode = $this->getMode();
        switch ($mode) {
            // ON/OFF
            case 'update_use':
                // エラーチェック
                $this->arrErr = $objFormParam->checkError();
                if (!(count($this->arrErr) > 0)) {
                    $arrPluginHookpointUse = $objFormParam->getValue('plugin_hookpoint_use');
                    $plugin_hookpoint_id = $objFormParam->getValue('plugin_hookpoint_id');
                    $use_flg = ($arrPluginHookpointUse[$plugin_hookpoint_id] == 1) ? 1 : 0;
                    SC_Plugin_Util_Ex::setPluginHookPointChangeUse($plugin_hookpoint_id, $use_flg);
                    // Smartyコンパイルファイルをクリア
                    SC_Utils_Ex::clearCompliedTemplate();
                }
                break;
            default:
                break;
        }
        // DBからプラグイン情報を取得
        $arrRet = SC_Plugin_Util_Ex::getPluginHookPointList();
        // 競合チェック
        $this->arrConflict = SC_Plugin_Util_Ex::checkConflictPlugin();
        $arrHookPoint = array();
        foreach ($arrRet AS $key => $val) {
            $arrHookPoint[$val['hook_point']][$val['plugin_id']] = $val;
        }
        $this->arrHookPoint = $arrHookPoint;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    public function destroy()
    {
        parent::destroy();
    }

    /**
     * パラメーター初期化.
     *
     * @param  SC_FormParam_Ex $objFormParam
     * @param  string          $mode         モード
     * @return void
     */
    public function initParam(&$objFormParam)
    {
        $objFormParam->addParam('モード', 'mode', STEXT_LEN, '', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ON/OFFフラグ', 'plugin_hookpoint_use', INT_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('プラグインフックポイントID', 'plugin_hookpoint_id', INT_LEN, '', array('NUM_CHECK', 'EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    }

}
