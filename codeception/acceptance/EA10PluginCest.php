<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Codeception\Util\FileSystem;
use Codeception\Util\Fixtures;
use Doctrine\ORM\EntityManager;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Page\Admin\PluginLocalInstallPage;
use Page\Admin\PluginManagePage;
use Page\Admin\PluginSearchPage;

class EA10PluginCest
{
    /** @var EntityManager */
    private $em;

    /** @var \Doctrine\DBAL\Connection */
    private $conn;

    /** @var PluginRepository */
    private $pluginRepository;

    /** @var EccubeConfig */
    private $config;

    public function _before(\AcceptanceTester $I)
    {
        $I->loginAsAdmin();

        $this->em = Fixtures::get('entityManager');
        $this->conn = $this->em->getConnection();
        $this->pluginRepository = $this->em->getRepository(Plugin::class);
        $this->config = Fixtures::get('config');
        FileSystem::doEmptyDir('repos');
    }

    public function installFromStore(\AcceptanceTester $I)
    {
        $this->publishPlugin('Horizon-1.0.0.tgz');
        /*
         * インストール
         */
        $ManagePage = PluginSearchPage::go($I)
            ->入手する('Horizon')
            ->インストール();

        $I->assertFalse($this->tableExists('dtb_dash'));
        $I->assertFalse($this->columnExists('dtb_cart', 'is_horizon'));

        $Plugin = $this->pluginRepository->findByCode('Horizon');
        $I->assertFalse($Plugin->isInitialized(), '初期化されていない');
        $I->assertFalse($Plugin->isEnabled(), '有効化されていない');

        /*
         * 有効化
         */
        $ManagePage->ストアプラグイン_有効化('Horizon');

        $I->assertTrue($this->tableExists('dtb_dash'));
        $I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertTrue($Plugin->isEnabled(), '有効化されている');

        /*
         * 無効化
         */
        $ManagePage->ストアプラグイン_無効化('Horizon');

        $I->assertTrue($this->tableExists('dtb_dash'));
        $I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertFalse($Plugin->isEnabled(), '無効化されている');

        /*
         * 再度有効化
         */
        $ManagePage->ストアプラグイン_有効化('Horizon');

        $I->assertTrue($this->tableExists('dtb_dash'));
        $I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertTrue($Plugin->isEnabled(), '有効化されている');

        /*
         * 再度無効化
         */
        $ManagePage->ストアプラグイン_無効化('Horizon');

        $I->assertTrue($this->tableExists('dtb_dash'));
        $I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertFalse($Plugin->isEnabled(), '無効化されている');

        /*
         * 削除
         */
        $ManagePage->ストアプラグイン_削除('Horizon');

        $I->assertFalse($this->tableExists('dtb_dash'));
        $I->assertFalse($this->columnExists('dtb_cart', 'is_horizon'));

        $this->em->refresh($Plugin);
        $Plugin = $this->pluginRepository->findByCode('Horizon');
        $I->assertNull($Plugin);
    }

    public function installFromLocal(\AcceptanceTester $I)
    {
        /*
         * インストール
         */
        $ManagePage = PluginLocalInstallPage::go($I)
            ->アップロード('plugins/Horizon-1.0.0.tgz');

        $I->see('プラグインをインストールしました。', PluginManagePage::完了メーッセージ);

        $I->assertTrue($this->tableExists('dtb_dash'));
        $I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'));

        $Plugin = $this->pluginRepository->findByCode('Horizon');
        $I->assertTrue($Plugin->isInitialized(), '初期化されていない');
        $I->assertFalse($Plugin->isEnabled(), '有効化されていない');

        /*
         * 有効化
         */
        $ManagePage->独自プラグイン_有効化('Horizon');

        $I->assertTrue($this->tableExists('dtb_dash'));
        $I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertTrue($Plugin->isEnabled(), '有効化されている');

        /*
         * 無効化
         */
        $ManagePage->独自プラグイン_無効化('Horizon');

        $I->assertTrue($this->tableExists('dtb_dash'));
        $I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertFalse($Plugin->isEnabled(), '無効化されている');

        /*
         * 再度有効化
        */
        $ManagePage->独自プラグイン_有効化('Horizon');

        $I->assertTrue($this->tableExists('dtb_dash'));
        $I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertTrue($Plugin->isEnabled(), '有効化されている');

        /*
         * 再度無効化
         */
        $ManagePage->独自プラグイン_無効化('Horizon');

        $I->assertTrue($this->tableExists('dtb_dash'));
        $I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertFalse($Plugin->isEnabled(), '無効化されている');

        /*
         * 削除
         */
        $ManagePage->独自プラグイン_削除('Horizon');

        $I->see('プラグインを削除しました。', PluginManagePage::完了メーッセージ);

        $I->assertFalse($this->tableExists('dtb_dash'));
        $I->assertFalse($this->columnExists('dtb_cart', 'is_horizon'));

        $this->em->refresh($Plugin);
        $Plugin = $this->pluginRepository->findByCode('Horizon');
        $I->assertNull($Plugin);
    }

    public function installLocalPluginWithAssets(\AcceptanceTester $I)
    {
        $this->publishPlugin('Assets-1.0.0.tgz');

        $assetsPath = $this->config['plugin_html_realdir'].'/Assets/assets/assets.js';
        $updatedPath = $this->config['plugin_html_realdir'].'/Assets/assets/updated.js';

        $I->assertFileNotExists($assetsPath);
        $I->assertFileNotExists($updatedPath);

        $ManagePage = PluginLocalInstallPage::go($I)->アップロード('plugins/Assets-1.0.0.tgz');
        $I->assertFileExists($assetsPath);
        $I->assertFileNotExists($updatedPath);

        $ManagePage->独自プラグイン_有効化('Assets');
        $I->assertFileExists($assetsPath);
        $I->assertFileNotExists($updatedPath);

        $ManagePage->独自プラグイン_無効化('Assets');
        $I->assertFileExists($assetsPath);
        $I->assertFileNotExists($updatedPath);

        $ManagePage->独自プラグイン_アップデート('Assets', 'plugins/Assets-1.0.1.tgz');
        $I->assertFileExists($assetsPath);
        $I->assertFileExists($updatedPath);

        $ManagePage->独自プラグイン_削除('Assets');
        $I->assertFileNotExists($assetsPath);
        $I->assertFileNotExists($updatedPath);
    }

    public function installStorePluginWithAssets(\AcceptanceTester $I)
    {
        // 最初のバージョンを作成
        $this->publishPlugin('Assets-1.0.0.tgz');

        $assetsPath = $this->config['plugin_html_realdir'].'/Assets/assets/assets.js';
        $updatedPath = $this->config['plugin_html_realdir'].'/Assets/assets/updated.js';
        $I->assertFileNotExists($assetsPath);
        $I->assertFileNotExists($updatedPath);

        $ManagePage = PluginSearchPage::go($I)
            ->入手する('Assets')
            ->インストール();
        $I->assertFileNotExists($assetsPath);
        $I->assertFileNotExists($updatedPath);

        $ManagePage->ストアプラグイン_有効化('Assets');
        $I->assertFileExists($assetsPath);
        $I->assertFileNotExists($updatedPath);

        $ManagePage->ストアプラグイン_無効化('Assets');
        $I->assertFileExists($assetsPath);
        $I->assertFileNotExists($updatedPath);

        // 新しいバージョンを作成
        $this->publishPlugin('Assets-1.0.1.tgz');

        $I->reloadPage();
        $ManagePage->ストアプラグイン_アップデート('Assets')->アップデート();
        $I->assertFileExists($assetsPath);
        $I->assertFileExists($updatedPath);

        $ManagePage->ストアプラグイン_無効化('Assets');
        $I->assertFileExists($assetsPath);
        $I->assertFileExists($updatedPath);

        $ManagePage->ストアプラグイン_削除('Assets');
        $I->assertFileNotExists($assetsPath);
        $I->assertFileNotExists($updatedPath);
    }

    private function publishPlugin($fileName)
    {
        copy(codecept_data_dir().'/'.'plugins/'.$fileName, codecept_root_dir().'/repos/'.$fileName);
    }

    private function tableExists($tableName)
    {
        return $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}';")->fetch()['count'] > 0;
    }

    private function columnExists($tableName, $columnName)
    {
        return $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}' AND column_name = '${columnName}';")->fetch()['count'] == 1;
    }
}
