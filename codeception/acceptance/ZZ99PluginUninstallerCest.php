<?php

use Page\Admin\OwnersPluginPage;

/**
 * @group plugin
 * @group plugin_uninstaller
 */
class EA09PluginUninstallerCest
{
    const ページタイトル = '#main .page-header';

    protected $plugins = [];

    public function _before(\AcceptanceTester $I)
    {
        $fixtures = __DIR__.'/../_data/plugin_fixtures.php';
        if (file_exists($fixtures)) {
            $this->plugins = require $fixtures;
        }
        $I->loginAsAdmin();
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    public function plugin_プラグインアンインストール(\AcceptanceTester $I)
    {
        $I->wantTo('プラグインアンインストール');

        foreach ($this->plugins as $num => $plugin) {

            // プラグイン無効化
            OwnersPluginPage::go($I)->無効にする($plugin['code']);
            $I->see('プラグインを無効にしました。', OwnersPluginPage::$完了メッセージ);
        }

        foreach ($this->plugins as $num => $plugin) {

            // プラグイン削除
            OwnersPluginPage::go($I)->削除($plugin['code']);
            $I->see(' プラグインを削除しました。', OwnersPluginPage::$完了メッセージ);
        }
    }
}
