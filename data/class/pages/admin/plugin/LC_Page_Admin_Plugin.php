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

require_once(CLASS_FILE_PATH . "pages/admin/LC_Page_Admin.php");

/**
 * プラグイン管理のページクラス
 *
 * @package Page
 * @author Seasoft 塚田将久
 * @version $Id:$
 */
class LC_Page_Admin_Plugin extends LC_Page_Admin {

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        if (DEBUG_LOAD_PLUGIN !== true) SC_Utils_Ex::sfDispException('プラグインは有効化されていない'); // XXX 開発途上対応
        parent::init();

        $this->tpl_mainpage = 'plugin/index.tpl';
        $this->tpl_mainno   = 'plugin';
        $this->tpl_subno    = 'index';
        $this->tpl_subtitle = 'プラグイン管理';
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

        $this->loadPluginsList();
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
     * プラグインの一覧を読み込む
     *
     * @return void
     */
    function loadPluginsList() {
        $plugins = array();
        $this->arrInstalledPlugin = array();
        $this->arrInstallablePlugin = array();

        $d = dir(PLUGIN_PATH);
        while (false !== ($entry = $d->read())) {
            if ($entry == '.') continue;
            if ($entry == '..') continue;
            if (!is_dir($d->path . $entry)) continue;

            $plugins[$entry]['dir_exists'] = true;
        }
        $d->close();

        $pluginsXml = SC_Utils_Ex::sfGetPluginsXml();
        foreach ($pluginsXml->plugin as $plugin) {
            $plugins[(string)$plugin->path]['installed'] = true;
        }

        foreach ($plugins as $path=>$plugin) {
            $plugin['info'] = SC_Utils_Ex::sfGetPluginInfoArray($path);
            $plugin['path'] = $path;
            if ($plugin['installed']) {
                $this->arrInstalledPlugin[] = $plugin;
            } else {
                $this->arrInstallablePlugin[] = $plugin;
            }
        }

    }
}
?>
