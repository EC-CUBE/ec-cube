<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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

require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * プラグインのインストールのページクラス
 *
 * FIXME インストール直後のレンダリング時点では、上部ナビに反映されない
 * TODO Transaction Token を使用する
 *
 * @package Page
 * @author Seasoft 塚田将久
 * @version $Id$
 */
class LC_Page_Admin_Plugin_Install extends LC_Page_Admin {

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        if (DEBUG_LOAD_PLUGIN !== true) SC_Utils_Ex::sfDispException('プラグインは有効化されていない'); // XXX 開発途上対応
        parent::init();

        $this->tpl_mainpage = 'plugin/install.tpl';
        $this->tpl_mainno   = 'plugin';
        $this->tpl_subno    = 'install';
        $this->tpl_subtitle = 'プラグインのインストール';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objSess = new SC_Session();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_REQUEST);
        // 入力情報を渡す
        $this->arrForm = $this->objFormParam->getHashArray();
        $this->arrErr = $this->objFormParam->checkError();
        if (count($this->arrErr) == 0) {
        // インストール
            $this->lfInstall($this->arrForm['path']);
            $this->tpl_result = '完了しました。';
        } else {
            SC_Utils_Ex::sfDispException();
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * インストール
     *
     * @return void
     */
    function lfInstall($path) {

        // アンインストール SQL を実行 (クリーンアップ)
        SC_Helper_DB_Ex::sfExecSqlByFile(PLUGIN_REALDIR . "$path/sql/uninstall.sql");

        // インストール SQL を実行
        SC_Helper_DB_Ex::sfExecSqlByFile(PLUGIN_REALDIR . "$path/sql/install.sql");

        // プラグイン XML に追加
        $this->lfAddToPluginsXml($path);
    }

    /**
     * プラグイン XML に追加
     *
     * @return void
     */
    function lfAddToPluginsXml($path) {
        $pluginsXml = SC_Utils_Ex::sfGetPluginsXml();
        $addPluginXml = $pluginsXml->addChild('plugin');
        $addPluginXml->addChild('path', $path);
        $arrPluginInfo = SC_Utils_Ex::sfGetPluginInfoArray($path);
        $addPluginXml->addChild('name', $arrPluginInfo['name']);
        SC_Utils_Ex::sfPutPluginsXml($pluginsXml);
    }

    /**
     * パラメータ情報の初期化
     *
     * @return void
     */
    function lfInitParam() {
        $this->objFormParam->addParam('プラグインのパス', 'path', STEXT_LEN, '', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    }
}
?>
