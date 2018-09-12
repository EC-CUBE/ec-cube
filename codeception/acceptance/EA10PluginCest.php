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

    public function install_enable_disable_enable_disable_remove_store(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->無効化()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function install_enable_disable_enable_disable_remove_local(\AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->有効化()
            ->無効化()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function install_remove_local(\AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->削除();
    }

    public function install_remove_store(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->削除();
    }

    public function install_update_remove_store(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->アップデート()
            ->削除();
    }



    public function install_update_remove_local(\AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->アップデート()
            ->削除();
    }

    public function install_enable_disable_update_enable_disable_remove_local(\AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->有効化()
            ->無効化()
            ->アップデート()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function install_enable_disable_update_enable_disable_remove_store(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->無効化()
            ->アップデート()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function install_enable_update_disable_remove_store(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->アップデート()
            ->無効化()
            ->削除();
    }

    public function install_enable_update_disable_remove_local(\AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->有効化()
            ->アップデート()
            ->無効化()
            ->削除();
    }

    public function install_update_enable_disable_remove_local(\AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->アップデート()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function install_update_enable_disable_remove_store(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->アップデート()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function install_enable_enable(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->新しいタブで開く()
            ->有効化()
            ->前のタブに戻る()
            ->既に有効なものを有効化();
    }

    public function install_disable_disable(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->新しいタブで開く()
            ->無効化()
            ->前のタブに戻る()
            ->既に無効なものを無効化();
    }

    public function install_assets_local(\AcceptanceTester $I)
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

    public function install_assets_store(\AcceptanceTester $I)
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

abstract class Abstract_Plugin
{
    /** @var AcceptanceTester */
    protected $I;

    /** @var EntityManager */
    protected $em;

    /** @var \Doctrine\DBAL\Connection */
    protected $conn;

    /** @var PluginRepository */
    protected $pluginRepository;

    /** @var EccubeConfig */
    protected $config;

    /**
     * Abstract_Plugin constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        $this->I = $I;
        $this->em = Fixtures::get('entityManager');
        $this->conn = $this->em->getConnection();
        $this->pluginRepository = $this->em->getRepository(Plugin::class);
        $this->config = Fixtures::get('config');
    }

    protected function tableExists($tableName)
    {
        return $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}';")->fetch()['count'] > 0;
    }

    protected function columnExists($tableName, $columnName)
    {
        return $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}' AND column_name = '${columnName}';")->fetch()['count'] == 1;
    }

    public function 新しいタブで開く()
    {
        $this->I->executeJS("window.open(location.href, 'other')");
        $this->I->switchToWindow('other');
        return $this;
    }

    public function 前のタブに戻る()
    {
        $this->I->switchToPreviousTab();
        return $this;
    }
}

class Horizon_Store extends Abstract_Plugin
{
    /** @var PluginManagePage */
    private $ManagePage;

    /** @var Plugin */
    private $Plugin;

    private $initialized = false;

    public static function start(AcceptanceTester $I)
    {
        return new Horizon_Store($I);
    }

    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public function インストール()
    {
        $this->publishPlugin('Horizon-1.0.0.tgz');
        /*
         * インストール
         */
        $this->ManagePage = PluginSearchPage::go($this->I)
            ->入手する('Horizon')
            ->インストール();

        $this->I->assertFalse($this->tableExists('dtb_dash'), 'テーブルがない');
        $this->I->assertFalse($this->columnExists('dtb_cart', 'is_horizon'), 'カラムがない');

        $this->Plugin = $this->pluginRepository->findByCode('Horizon');
        $this->I->assertFalse($this->Plugin->isInitialized(), '初期化されていない');
        $this->I->assertFalse($this->Plugin->isEnabled(), '有効化されていない');

        return $this;
    }

    public function 有効化()
    {
        $this->ManagePage->ストアプラグイン_有効化('Horizon');

        $this->I->assertTrue($this->tableExists('dtb_dash'), 'テーブルがある');
        $this->I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'), 'カラムがある');

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');

        $this->initialized = true;
        return $this;
    }

    public function 既に有効なものを有効化()
    {
        $this->ManagePage->ストアプラグイン_有効化('Horizon', '「ホライゾン」は既に有効です。');

        $this->I->assertTrue($this->tableExists('dtb_dash'), 'テーブルがある');
        $this->I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'), 'カラムがある');

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');

        $this->initialized = true;


        return $this;
    }

    public function 無効化()
    {
        $this->ManagePage->ストアプラグイン_無効化('Horizon');

        $this->I->assertTrue($this->tableExists('dtb_dash'), 'テーブルがある');
        $this->I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'), 'カラムがある');

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');

        return $this;
    }

    public function 既に無効なものを無効化()
    {
        $this->ManagePage->ストアプラグイン_無効化('Horizon', '「ホライゾン」は既に無効です。');

        $this->I->assertTrue($this->tableExists('dtb_dash'), 'テーブルがある');
        $this->I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'), 'カラムがある');

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');

        return $this;
    }

    public function 削除()
    {
        $this->ManagePage->ストアプラグイン_削除('Horizon');

        $this->I->assertFalse($this->tableExists('dtb_dash'), 'テーブルが消えている');
        $this->I->assertFalse($this->columnExists('dtb_cart', 'is_horizon'), 'カラムが消えている');

        $this->em->refresh($this->Plugin);
        $this->Plugin = $this->pluginRepository->findByCode('Horizon');
        $this->I->assertNull($this->Plugin, '削除されている');

        return $this;
    }

    public function アップデート()
    {
        $this->publishPlugin('Horizon-1.0.1.tgz');

        $this->I->reloadPage();
        $this->ManagePage->ストアプラグイン_アップデート('Horizon')->アップデート();

        $this->em->refresh($this->Plugin);

        if ($this->initialized) {
            $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
            $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');
        } else {
            $this->I->assertFalse($this->Plugin->isInitialized(), '初期化されていない');
            $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');
        }

        return $this;
    }

    private function publishPlugin($fileName)
    {
        $published = copy(codecept_data_dir().'/'.'plugins/'.$fileName, codecept_root_dir().'/repos/'.$fileName);
        $this->I->assertTrue($published, "公開できた ${fileName}");
    }
}

class Horizon_Local extends Abstract_Plugin
{
    /** @var PluginManagePage */
    private $ManagePage;

    /** @var Plugin */
    private $Plugin;

    private $enabled = false;

    public static function start(AcceptanceTester $I)
    {
        return new Horizon_Local($I);
    }

    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public function インストール()
    {
        $this->ManagePage = PluginLocalInstallPage::go($this->I)
            ->アップロード('plugins/Horizon-1.0.0.tgz');

        $this->I->see('プラグインをインストールしました。', PluginManagePage::完了メーッセージ);

        $this->I->assertTrue($this->tableExists('dtb_dash'), 'テーブルがある');
        $this->I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'), 'カラムがある');

        $this->Plugin = $this->pluginRepository->findByCode('Horizon');
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されていない');
        $this->I->assertFalse($this->Plugin->isEnabled(), '有効化されていない');

        return $this;
    }

    public function 有効化()
    {
        $this->ManagePage->独自プラグイン_有効化('Horizon');

        $this->I->assertTrue($this->tableExists('dtb_dash'), 'テーブルがある');
        $this->I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'), 'カラムがある');

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');

        $this->enabled = true;
        return $this;
    }

    public function 無効化()
    {
        $this->ManagePage->独自プラグイン_無効化('Horizon');

        $this->I->assertTrue($this->tableExists('dtb_dash'), 'テーブルがある');
        $this->I->assertTrue($this->columnExists('dtb_cart', 'is_horizon'), 'カラムがある');

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');

        return $this;
    }

    public function 削除()
    {
        $this->ManagePage->独自プラグイン_削除('Horizon');

        $this->I->see('プラグインを削除しました。', PluginManagePage::完了メーッセージ);

        $this->I->assertFalse($this->tableExists('dtb_dash'), 'テーブルが削除されている');
        $this->I->assertFalse($this->columnExists('dtb_cart', 'is_horizon'), 'カラムが削除されている');

        $this->em->refresh($this->Plugin);
        $this->Plugin = $this->pluginRepository->findByCode('Horizon');
        $this->I->assertNull($this->Plugin, '削除されている');

        return $this;
    }

    public function アップデート()
    {
        $this->ManagePage->独自プラグイン_アップデート('Horizon', 'plugins/Horizon-1.0.1.tgz');

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');

        if ($this->enabled) {
            $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');
        } else {
            $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');
        }

        return $this;
    }
}