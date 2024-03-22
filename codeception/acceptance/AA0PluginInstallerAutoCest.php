<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Page\Admin\OwnersPluginListPage;

/**
 * @group plugin
 * @group plugin_installer
 */
class AA00PluginInstallerAutoCest
{

    protected $plugin = [];

    public function _before(AcceptanceTester $I)
    {
        $fixtures = __DIR__.'/../_data/plugin_fixtures_v2.php';
        if (file_exists($fixtures)) {
            $this->plugins = require $fixtures;
        }
        $I->loginAsAdmin();
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function plugin_プラグインインストール(AcceptanceTester $I)
    {
        $plugin = $this->plugins;
        $I->wantTo('プラグインインストール');

            $I->wantTo('Install ' . $plugin['name']);
            $OwnersPluginListPage = OwnersPluginListPage::go($I);
            // Back to plugin list page
            $OwnersPluginListPage->authenByKey($plugin['authen_key']);
            $OwnersPluginListPage->go($I);

            $OwnersPluginListPage->install($plugin['name']);
            $OwnersPluginListPage->enable($plugin['name']);
            $OwnersPluginListPage->disable($plugin['name']);
            $OwnersPluginListPage->uninstall($plugin['name']);

    }
}
