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

require_once(CLASS_PATH . "pages/admin/LC_Page_Admin.php");

/**
 * プラグインのアンインストールのページクラス
 *
 * FIXME インストール直後のレンダリング時点では、上部ナビに反映されない
 * TODO Transaction Token を使用する
 *
 * @package Page
 * @author Seasoft 塚田将久
 * @version $Id:$
 */
class LC_Page_Admin_Plugin_Uninstall extends LC_Page_Admin {

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        if (DEBUG_LOAD_PLUGIN !== true) SC_Utils_Ex::sfDispException('プラグインは有効化されていない'); // XXX 開発途上対応
        parent::init();

        $this->tpl_mainpage = 'plugin/uninstall.tpl';
        $this->tpl_mainno   = 'plugin';
        $this->tpl_subno    = 'uninstall';
        $this->tpl_subtitle = 'プラグインのアンインストール';
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
            // アンインストール
            $this->lfUninstall($this->arrForm['path']);
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
     * アンインストール
     *
     * @return void
     */
    function lfUninstall($path) {
        $objQuery = new SC_Query();

        // アンインストール SQL を実行
        SC_Helper_DB_Ex::sfExecSqlByFile(PLUGIN_PATH . "$path/sql/uninstall.sql");

        // プラグイン XML から削除
        $this->lfRemoveFromPluginsXml($path);
    }

    /**
     * プラグイン XML から削除
     *
     * @return void
     */
    function lfRemoveFromPluginsXml($path) {
        $pluginsXml = SC_Utils_Ex::sfGetPluginsXml();
        for ($i = 0; $i <= count($pluginsXml->plugin) - 1; $i++) {
            if ((string)$pluginsXml->plugin[$i]->path == $path) {
                unset($pluginsXml->plugin[$i]);
            }
        }
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
