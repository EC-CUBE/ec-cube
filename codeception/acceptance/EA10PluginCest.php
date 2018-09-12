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

    public function test_install_enable_disable_enable_disable_remove_store(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->無効化()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function test_install_enable_disable_enable_disable_remove_local(\AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->有効化()
            ->無効化()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function test_install_remove_local(\AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->削除();
    }

    public function test_install_remove_store(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール();
    }

    public function test_install_update_remove_store(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->アップデート()
            ->削除();
    }



    public function test_install_update_remove_local(\AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->アップデート()
            ->削除();
    }

    public function test_install_enable_disable_update_enable_disable_remove_local(\AcceptanceTester $I)
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

    public function test_install_enable_disable_update_enable_disable_remove_store(\AcceptanceTester $I)
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

    public function test_install_enable_update_disable_remove_store(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->アップデート()
            ->無効化()
            ->削除();
    }

    public function test_install_enable_update_disable_remove_local(\AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->有効化()
            ->アップデート()
            ->無効化()
            ->削除();
    }

    public function test_install_update_enable_disable_remove_local(\AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->アップデート()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function test_install_update_enable_disable_remove_store(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->アップデート()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function test_install_enable_enable(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->新しいタブで開く()
            ->有効化()
            ->前のタブに戻る()
            ->既に有効なものを有効化();
    }

    public function test_install_disable_disable(\AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->新しいタブで開く()
            ->無効化()
            ->前のタブに戻る()
            ->既に無効なものを無効化();
    }

    public function test_install_assets_local(\AcceptanceTester $I)
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

    public function test_install_assets_store(\AcceptanceTester $I)
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

        $ManagePage->ストアプラグイン_削除('Assets');
        $I->assertFileNotExists($assetsPath);
        $I->assertFileNotExists($updatedPath);
    }

    public function test_extend_same_table_store(\AcceptanceTester $I)
    {
        $Horizon = Horizon_Store::start($I);
        $Boomerang = Boomerang_Store::start($I);

        $Horizon->インストール()->有効化();
        $Boomerang->インストール()->有効化();

        $Horizon->tableExists();
        $Horizon->columnExists();
    }

    public function test_extend_same_table_local(\AcceptanceTester $I)
    {
        $Horizon = Horizon_Local::start($I);
        $Boomerang = Boomerang_Local::start($I);

        $Horizon->インストール()->有効化();
        $Boomerang->インストール()->有効化();

        $Horizon->tableExists();
        $Horizon->columnExists();
    }

    public function test_extend_same_table_crossed_local(\AcceptanceTester $I)
    {
        $Horizon = Horizon_Local::start($I);
        $Boomerang = Boomerang_Local::start($I);

        $Horizon->インストール()->有効化()->無効化();
        $Boomerang->インストール()->有効化();

        $Horizon->tableExists();
        $Horizon->columnExists();
    }

    private function publishPlugin($fileName)
    {
        copy(codecept_data_dir().'/'.'plugins/'.$fileName, codecept_root_dir().'/repos/'.$fileName);
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


    protected $table;

    protected $column;

    protected $traitTarget;

    protected $trait;

    public function __construct(\AcceptanceTester $I)
    {
        $this->I = $I;
        $this->em = Fixtures::get('entityManager');
        $this->conn = $this->em->getConnection();
        $this->pluginRepository = $this->em->getRepository(Plugin::class);
        $this->config = Fixtures::get('config');
    }

    public function tableExists()
    {
        if ($this->table) {
            $exists = $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '".$this->table."';")->fetch()['count'] > 0;
            $this->I->assertTrue($exists, 'テーブルがあるはず '.$this->table);
        }
    }

    public function tableNotExists()
    {
        if ($this->table) {
            $exists = $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '".$this->table."';")->fetch()['count'] > 0;
            $this->I->assertFalse($exists, 'テーブルがないはず '.$this->table);
        }
    }

    public function columnExists()
    {
        if ($this->column) {
            list($tableName, $columnName) = explode('.', $this->column);
            $exists = $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}' AND column_name = '${columnName}';")->fetch()['count'] == 1;
            $this->I->assertTrue($exists, 'カラムがあるはず '.$this->column);
        }
    }

    public function columnNotExists()
    {
        if ($this->column) {
            list($tableName, $columnName) = explode('.', $this->column);
            $exists = $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}' AND column_name = '${columnName}';")->fetch()['count'] == 1;
            $this->I->assertFalse($exists, 'カラムがないはず '.$this->column);
        }
    }

    public function traitExists()
    {
        if ($this->trait) {
            $this->I->assertContains($this->trait, file_get_contents($this->config['kernel.project_dir'].'/app/proxy/entity/'.$this->traitTarget.'.php'), 'Traitがあるはず');
        }
    }

    public function traitNotExists()
    {
        if ($this->trait) {
            $file = $this->config['kernel.project_dir'].'/app/proxy/entity/'.$this->traitTarget.'.php';
            if (file_exists($file)) {
                $this->I->assertNotContains($this->trait, file_get_contents($file), 'Traitがないはず');
            } else {
                $this->I->assertTrue(true, 'Traitがないはず');
            }
        }
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

class Store_Plugin extends Abstract_Plugin
{
    /** @var PluginManagePage */
    private $ManagePage;

    /** @var Plugin */
    private $Plugin;

    private $initialized = false;

    private $enabled = false;

    private $code;

    public function __construct(AcceptanceTester $I, $code)
    {
        parent::__construct($I);
        $this->code = $code;
        $this->publishPlugin($this->code.'-1.0.0.tgz');
    }

    public function インストール()
    {
        /*
         * インストール
         */
        $this->ManagePage = PluginSearchPage::go($this->I)
            ->入手する($this->code)
            ->インストール();

        $this->tableNotExists();
        $this->columnNotExists();

        $this->traitNotExists();

        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertFalse($this->Plugin->isInitialized(), '初期化されていない');
        $this->I->assertFalse($this->Plugin->isEnabled(), '有効化されていない');

        return $this;
    }

    public function 有効化()
    {
        $this->ManagePage->ストアプラグイン_有効化($this->code);

        $this->tableExists();
        $this->columnExists();

        $this->traitExists();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');

        $this->initialized = true;
        $this->enabled = true;
        return $this;
    }

    public function 既に有効なものを有効化()
    {
        $this->ManagePage->ストアプラグイン_有効化($this->code, '既に有効です。');

        $this->tableExists();
        $this->columnExists();

        $this->traitExists();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');

        $this->initialized = true;
        $this->enabled = true;


        return $this;
    }

    public function 無効化()
    {
        $this->ManagePage->ストアプラグイン_無効化($this->code);

        $this->tableExists();
        $this->columnExists();

        $this->traitNotExists();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');

        $this->enabled = false;

        return $this;
    }

    public function 既に無効なものを無効化()
    {
        $this->ManagePage->ストアプラグイン_無効化($this->code, '既に無効です。');

        $this->tableExists();
        $this->columnExists();

        $this->traitNotExists();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');

        $this->enabled = false;

        return $this;
    }

    public function 削除()
    {
        $this->ManagePage->ストアプラグイン_削除($this->code);

        $this->tableNotExists();
        $this->columnNotExists();

        $this->traitNotExists();

        $this->em->refresh($this->Plugin);
        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertNull($this->Plugin, '削除されている');

        return $this;
    }

    public function アップデート()
    {
        $this->publishPlugin($this->code.'-1.0.1.tgz');

        $this->I->reloadPage();
        $this->ManagePage->ストアプラグイン_アップデート($this->code)->アップデート();

        if ($this->initialized) {
            $this->tableExists();
            $this->columnExists();
            $this->traitExists();
        } else {
            $this->tableNotExists();
            $this->columnNotExists();
            $this->traitNotExists();
        }

        $this->em->refresh($this->Plugin);
        $this->I->assertEquals($this->initialized, $this->Plugin->isInitialized(), '初期化');
        $this->I->assertEquals($this->enabled, $this->Plugin->isEnabled(), '有効/無効');

        return $this;
    }

    private function publishPlugin($fileName)
    {
        $published = copy(codecept_data_dir().'/'.'plugins/'.$fileName, codecept_root_dir().'/repos/'.$fileName);
        $this->I->assertTrue($published, "公開できた ${fileName}");
    }
}

class Local_Plugin extends Abstract_Plugin
{
    /** @var PluginManagePage */
    private $ManagePage;

    /** @var Plugin */
    private $Plugin;

    private $enabled = false;

    /** @var string */
    private $code;

    public function __construct(AcceptanceTester $I, $code)
    {
        parent::__construct($I);
        $this->code = $code;
    }

    public function インストール()
    {
        $this->ManagePage = PluginLocalInstallPage::go($this->I)
            ->アップロード('plugins/'.$this->code.'-1.0.0.tgz');

        $this->I->see('プラグインをインストールしました。', PluginManagePage::完了メーッセージ);

        $this->tableExists();
        $this->columnExists();

        $this->traitNotExists();

        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されていない');
        $this->I->assertFalse($this->Plugin->isEnabled(), '有効化されていない');

        return $this;
    }

    public function 有効化()
    {
        $this->ManagePage->独自プラグイン_有効化($this->code);

        $this->tableExists();
        $this->columnExists();

        $this->traitExists();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');

        $this->enabled = true;
        return $this;
    }

    public function 無効化()
    {
        $this->ManagePage->独自プラグイン_無効化($this->code);

        $this->tableExists();
        $this->columnExists();

        $this->traitNotExists();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');

        $this->enabled = false;

        return $this;
    }

    public function 削除()
    {
        $this->ManagePage->独自プラグイン_削除($this->code);

        $this->I->see('プラグインを削除しました。', PluginManagePage::完了メーッセージ);

        $this->tableNotExists();
        $this->columnNotExists();

        $this->traitNotExists();

        $this->em->refresh($this->Plugin);
        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertNull($this->Plugin, '削除されている');

        return $this;
    }

    public function アップデート()
    {
        $this->ManagePage->独自プラグイン_アップデート($this->code, 'plugins/'.$this->code.'-1.0.1.tgz');

        $this->tableExists();
        $this->columnExists();

        $this->traitExists();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertEquals($this->enabled, $this->Plugin->isEnabled(), '有効/無効');

        return $this;
    }
}

class Horizon_Local extends Local_Plugin
{
    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I, 'Horizon');
        $this->table = 'dtb_dash';
        $this->column = 'dtb_cart.is_horizon';
        $this->trait = '\Plugin\Horizon\Entity\CartTrait';
        $this->traitTarget = 'Cart';
    }

    public static function start(AcceptanceTester $I)
    {
        return new self($I);
    }
}

class Horizon_Store extends Store_Plugin
{
    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I, 'Horizon');
        $this->table = 'dtb_dash';
        $this->column = 'dtb_cart.is_horizon';
        $this->trait = '\Plugin\Horizon\Entity\CartTrait';
        $this->traitTarget = 'Cart';
    }

    public static function start(AcceptanceTester $I)
    {
        return new self($I);
    }
}

class Boomerang_Store extends Store_Plugin
{
    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I, 'Boomerang');
        $this->column = 'dtb_cart.is_boomerang';
        $this->trait = '\Plugin\Boomerang\Entity\CartTrait';
        $this->traitTarget = 'Cart';
    }

    public static function start(AcceptanceTester $I)
    {
        return new self($I);
    }
}

class Boomerang_Local extends Local_Plugin
{
    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I, 'Boomerang');
        $this->column = 'dtb_cart.is_boomerang';
        $this->trait = '\Plugin\Boomerang\Entity\CartTrait';
        $this->traitTarget = 'Cart';
    }

    public static function start(AcceptanceTester $I)
    {
        return new self($I);
    }
}