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

use Page\Admin\OwnersPluginPage;

/**
 * @group plugin
 * @group plugin_installer
 */
class AA00PluginInstallerCest
{
    const ページタイトル = '#main .page-header';

    protected $plugins = [];

    public function _before(AcceptanceTester $I)
    {
        $fixtures = __DIR__.'/../_data/plugin_fixtures.php';
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
        $I->wantTo('プラグインインストール');

        foreach ($this->plugins as $num => $plugin) {
            $OwnersPluginPage = OwnersPluginPage::go($I)
                ->goInstall($I);
            $datadir = __DIR__.'/../_data';

            if (file_exists($datadir.'/'.$plugin['file'])) {
                unlink($datadir.'/'.$plugin['file']);
            }
            $I->amGoingTo($plugin['file'].' を '.$plugin['url'].' からダウンロードします.');
            $archive = file_get_contents($plugin['url']);
            $save_path = $datadir.'/'.$plugin['file'];
            file_put_contents($save_path, $archive);
            $I->amGoingTo($plugin['file'].' を '.$save_path.' に保存しました.');

            $OwnersPluginPage->インストール($plugin['file']);
            $I->see('プラグインをインストールしました。', OwnersPluginPage::$完了メッセージ);

            // プラグイン有効化
            $OwnersPluginPage->有効にする($plugin['code']);
            $I->see('プラグインを有効にしました。', OwnersPluginPage::$完了メッセージ);
        }
    }
}
