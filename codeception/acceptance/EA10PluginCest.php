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

use Codeception\Util\FileSystem;
use Codeception\Util\Fixtures;
use Doctrine\ORM\EntityManager;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Page\Admin\CacheManagePage;
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

    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();

        $this->em = Fixtures::get('entityManager');
        $this->conn = $this->em->getConnection();
        $this->pluginRepository = $this->em->getRepository(Plugin::class);
        $this->config = Fixtures::get('config');
        FileSystem::doEmptyDir('repos');
    }

    public function test_install_enable_disable_remove_store(AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function test_install_enable_disable_remove_local(AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function test_install_enable_disable_enable_disable_remove_store(AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->無効化()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function test_install_enable_disable_enable_disable_remove_local(AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->有効化()
            ->無効化()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function test_install_remove_local(AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->削除();
    }

    public function test_install_remove_store(AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->削除();
    }

    public function test_install_update_remove_store(AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->アップデート()
            ->削除();
    }

    public function test_install_update_remove_local(AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->アップデート()
            ->削除();
    }

    public function test_install_enable_disable_update_enable_disable_remove_local(AcceptanceTester $I)
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

    public function test_install_enable_disable_update_enable_disable_remove_store(AcceptanceTester $I)
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

    public function test_install_enable_update_disable_remove_store(AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->アップデート()
            ->削除();
    }

    public function test_install_enable_update_disable_remove_local(AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->有効化()
            ->アップデート()
            ->無効化()
            ->削除();
    }

    public function test_install_update_enable_disable_remove_local(AcceptanceTester $I)
    {
        Horizon_Local::start($I)
            ->インストール()
            ->アップデート()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function test_install_update_enable_disable_remove_store(AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->アップデート()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function test_install_enable_enable(AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->新しいタブで開く()
            ->有効化()
            ->前のタブに戻る()
            ->既に有効なものを有効化();
    }

    public function test_install_disable_disable(AcceptanceTester $I)
    {
        Horizon_Store::start($I)
            ->インストール()
            ->有効化()
            ->新しいタブで開く()
            ->無効化()
            ->前のタブに戻る()
            ->既に無効なものを無効化();
    }

    public function test_install_assets_local(AcceptanceTester $I)
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

    public function test_install_assets_store(AcceptanceTester $I)
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

    public function test_extend_same_table_store(AcceptanceTester $I)
    {
        $Horizon = Horizon_Store::start($I);
        $Boomerang = Boomerang_Store::start($I);

        $Horizon->インストール()->有効化();
        $Boomerang->インストール()->有効化();

        $Horizon->検証()->無効化()->削除();
        $Boomerang->検証()->無効化()->削除();
    }

    public function test_extend_same_table_disabled_remove_store(AcceptanceTester $I)
    {
        $Horizon = Horizon_Store::start($I);
        $Boomerang = Boomerang_Store::start($I);

        $Horizon->インストール()->有効化()->無効化();
        $Boomerang->インストール()->有効化()->無効化();

        $Horizon->検証()->削除();
        $Boomerang->検証()->削除();
    }

    public function test_extend_same_table_local(AcceptanceTester $I)
    {
        $Horizon = Horizon_Local::start($I);
        $Boomerang = Boomerang_Local::start($I);

        $Horizon->インストール()->有効化();
        $Boomerang->インストール()->有効化();

        $Horizon->検証()->無効化()->削除();
        $Boomerang->検証()->無効化()->削除();
    }

    public function test_extend_same_table_disabled_remove_local(AcceptanceTester $I)
    {
        $Horizon = Horizon_Local::start($I);
        $Boomerang = Boomerang_Local::start($I);

        $Horizon->インストール()->有効化()->無効化();
        $Boomerang->インストール()->有効化()->無効化();

        $Horizon->検証()->削除();
        $Boomerang->検証()->削除();
    }

    public function test_extend_same_table_crossed_store(AcceptanceTester $I)
    {
        $Horizon = Horizon_Store::start($I);
        $Boomerang = Boomerang_Store::start($I);

        $Horizon->インストール()->有効化()->無効化();
        $Boomerang->インストール()->有効化();

        $Horizon->検証()->削除();
        $Boomerang->検証()->無効化()->削除();
    }

    public function test_extend_same_table_crossed_local(AcceptanceTester $I)
    {
        $Horizon = Horizon_Local::start($I);
        $Boomerang = Boomerang_Local::start($I);

        $Horizon->インストール()->有効化()->無効化();
        $Boomerang->インストール()->有効化();

        $Horizon->検証()->削除();
        $Boomerang->検証()->無効化()->削除();
    }

    public function test_dependency_each_install_plugin(AcceptanceTester $I)
    {
        $Horizon = Horizon_Store::start($I);
        $Emperor = Emperor_Store::start($I);

        $Horizon->インストール()->有効化();
        $Emperor->インストール()->有効化();
    }

    public function test_dependency_plugin_install(AcceptanceTester $I)
    {
        $Horizon = Horizon_Store::start($I);
        $Emperor = Emperor_Store::start($I, $Horizon);

        $Emperor->インストール()
            ->依存より先に有効化();

        $Horizon->有効化();

        $Emperor->有効化();

        $Horizon->依存されているのが有効なのに無効化();
        $Emperor->無効化();
        $Horizon->無効化();

        $Horizon->依存されているのが削除されていないのに削除();
        $Emperor->削除();
        $Horizon->削除();
    }

    public function test_dependency_plugin_update(AcceptanceTester $I)
    {
        $Horizon = Horizon_Store::start($I);
        $Emperor = Emperor_Store::start($I, $Horizon);

        $Emperor->インストール();

        $Horizon->検証()
            ->有効化();

        $Emperor
            ->有効化()
            ->無効化()
            ->アップデート();

        $Horizon->検証();
    }

    public function test_install_error(AcceptanceTester $I)
    {
        $this->publishPlugin('InstallError.tgz');
        $Horizon = Horizon_Store::start($I);

        PluginSearchPage::go($I)
            ->入手する('InstallError')
            ->インストール('システムエラーが発生しました。');

        // エラー後に他のプラグインがインストールできる
        $Horizon->インストール();
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/pull/4527
     */
    public function test_template_overwrite(AcceptanceTester $I)
    {
        $plugin = new Local_Plugin($I, 'Template');
        $plugin->インストール();
        $plugin->有効化();

        // テンプレートの確認
        $I->amOnPage('/template');
        $I->see('hello');

        // テンプレートをapp/template/plugin/[Plugin Code]に設置
        $dir = $this->config->get('eccube_theme_app_dir').'/plugin/Template';
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->mkdir($dir);
        $fs->dumpFile($dir.'/index.twig', 'bye');

        // キャッシュ削除すると反映される
        $page = CacheManagePage::go($I);
        $page->キャッシュ削除();

        // 上書きされていることを確認
        $I->amOnPage('/template');
        $I->see('bye');

        $I->amOnPage('/'.$this->config->get('eccube_admin_route').'/store/plugin');
        $plugin->無効化();
        $plugin->削除();

        $fs->remove($dir);
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/pull/4638
     */
    public function test_enhance_plugin_entity(AcceptanceTester $I)
    {
        $Boomerang = Boomerang_Store::start($I)
            ->インストール()
            ->有効化()
            ->カート作成();

        $I->see('[1]');

        Boomerang10_Store::start($I, $Boomerang)
            ->インストール()
            ->有効化();

        $Boomerang->カート一覧();
        $I->see('[1]');
    }

    public function test_bundle_install_enable_disable_remove_store(AcceptanceTester $I)
    {
        $Bundle = Bundle_Store::start($I);
        $Bundle->インストール()
            ->有効化()
            ->無効化()
            ->削除();
    }

    public function test_bundle_install_update_enable_disable_remove_store(AcceptanceTester $I)
    {
        $Bundle = Bundle_Store::start($I);
        $Bundle->インストール()
            ->有効化()
            ->アップデート()
            ->有効化()
            ->無効化()
            ->削除();
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

    protected $initialized = false;

    protected $enabled = false;

    protected $removed = false;

    protected $tables = [];

    protected $columns = [];

    protected $traits = [];

    public function __construct(AcceptanceTester $I)
    {
        $this->I = $I;
        $this->em = Fixtures::get('entityManager');
        $this->conn = $this->em->getConnection();
        $this->pluginRepository = $this->em->getRepository(Plugin::class);
        $this->config = Fixtures::get('config');
    }

    public function tableExists()
    {
        foreach ($this->tables as $table) {
            $exists = $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '".$table."';")->fetch()['count'] > 0;
            $this->I->assertTrue($exists, 'テーブルがあるはず '.$table);
        }
    }

    public function tableNotExists()
    {
        foreach ($this->tables as $table) {
            $exists = $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '".$table."';")->fetch()['count'] > 0;
            $this->I->assertFalse($exists, 'テーブルがないはず '.$table);
        }
    }

    public function columnExists()
    {
        foreach ($this->columns as $column) {
            list($tableName, $columnName) = explode('.', $column);
            $exists = $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}' AND column_name = '${columnName}';")->fetch()['count'] == 1;
            $this->I->assertTrue($exists, 'カラムがあるはず '.$column);
        }
    }

    public function columnNotExists()
    {
        foreach ($this->columns as $column) {
            list($tableName, $columnName) = explode('.', $column);
            $exists = $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}' AND column_name = '${columnName}';")->fetch()['count'] == 1;
            $this->I->assertFalse($exists, 'カラムがないはず '.$column);
        }
    }

    public function traitExists()
    {
        foreach ($this->traits as $trait => $target) {
            $this->I->assertContains($trait, file_get_contents($this->config['kernel.project_dir'].'/app/proxy/entity/'.$target.'.php'), 'Traitがあるはず '.$trait);
        }
    }

    public function traitNotExists()
    {
        foreach ($this->traits as $trait => $target) {
            $file = $this->config['kernel.project_dir'].'/app/proxy/entity/'.$target.'.php';
            if (file_exists($file)) {
                $this->I->assertNotContains($trait, file_get_contents($file), 'Traitがないはず '.$trait);
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

    public function 検証()
    {
        $this->I->wait(1);
        if ($this->initialized) {
            $this->tableExists();
            $this->columnExists();
        } else {
            $this->tableNotExists();
            $this->columnNotExists();
        }

        if ($this->enabled) {
            $this->traitExists();
        } else {
            $this->traitNotExists();
        }

        return $this;
    }
}

class Store_Plugin extends Abstract_Plugin
{
    /** @var PluginManagePage */
    protected $ManagePage;

    /** @var Plugin */
    protected $Plugin;

    protected $code;

    /** @var Store_Plugin */
    protected $dependency;

    public function __construct(AcceptanceTester $I, $code, Store_Plugin $dependency = null)
    {
        parent::__construct($I);
        $this->code = $code;
        $this->publishPlugin($this->code.'-1.0.0.tgz');
        if ($dependency) {
            $this->dependency = $dependency;
            $this->ManagePage = $dependency->ManagePage;
            $this->Plugin = $this->pluginRepository->findByCode($code);
        }
    }

    public function インストール()
    {
        /*
         * インストール
         */
        $this->ManagePage = PluginSearchPage::go($this->I)
            ->入手する($this->code)
            ->インストール();

        $this->検証();

        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertFalse($this->Plugin->isInitialized(), '初期化されていない');
        $this->I->assertFalse($this->Plugin->isEnabled(), '有効化されていない');

        if ($this->dependency) {
            $this->dependency->ManagePage = $this->ManagePage;
            $this->dependency->Plugin = $this->pluginRepository->findByCode($this->dependency->code);
        }

        return $this;
    }

    public function 有効化()
    {
        $this->ManagePage->ストアプラグイン_有効化($this->code);

        $this->initialized = true;
        $this->enabled = true;

        $this->検証();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');

        return $this;
    }

    public function 既に有効なものを有効化()
    {
        $this->ManagePage->ストアプラグイン_有効化($this->code, '既に有効です。');

        $this->initialized = true;
        $this->enabled = true;

        $this->検証();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');

        return $this;
    }

    public function 無効化()
    {
        $this->ManagePage->ストアプラグイン_無効化($this->code);

        $this->enabled = false;

        $this->検証();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');

        return $this;
    }

    public function 既に無効なものを無効化()
    {
        $this->ManagePage->ストアプラグイン_無効化($this->code, '既に無効です。');

        $this->enabled = false;

        $this->検証();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');

        return $this;
    }

    public function 削除()
    {
        $this->ManagePage->ストアプラグイン_削除($this->code);

        $this->initialized = false;
        $this->enabled = false;

        $this->検証();

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

        $this->initialized = true;
        $this->enabled = false;

        $this->検証();

        $this->em->refresh($this->Plugin);
        $this->I->assertEquals($this->initialized, $this->Plugin->isInitialized(), '初期化');
        $this->I->assertEquals($this->enabled, $this->Plugin->isEnabled(), '有効/無効');

        return $this;
    }

    protected function publishPlugin($fileName)
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

        $this->initialized = true;

        $this->I->see('プラグインをインストールしました。', PluginManagePage::完了メーッセージ);

        $this->検証();

        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されていない');
        $this->I->assertFalse($this->Plugin->isEnabled(), '有効化されていない');

        return $this;
    }

    public function 有効化()
    {
        $this->ManagePage->独自プラグイン_有効化($this->code);

        $this->enabled = true;

        $this->検証();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されている');

        return $this;
    }

    public function 無効化()
    {
        $this->ManagePage->独自プラグイン_無効化($this->code);

        $this->enabled = false;

        $this->検証();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されている');
        $this->I->assertFalse($this->Plugin->isEnabled(), '無効化されている');

        return $this;
    }

    public function 削除()
    {
        $this->ManagePage->独自プラグイン_削除($this->code);

        $this->initialized = false;
        $this->enabled = false;

        $this->I->see('プラグインを削除しました。', PluginManagePage::完了メーッセージ);

        $this->検証();

        $this->em->refresh($this->Plugin);
        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertNull($this->Plugin, '削除されている');

        return $this;
    }

    public function アップデート()
    {
        $this->ManagePage->独自プラグイン_アップデート($this->code, 'plugins/'.$this->code.'-1.0.1.tgz');

        $this->検証();

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
        $this->tables[] = 'dtb_dash';
        $this->columns[] = 'dtb_cart.is_horizon';
        $this->columns[] = 'dtb_cart.dash_id';
        $this->traits['\Plugin\Horizon\Entity\CartTrait'] = 'src/Eccube/Entity/Cart';
    }

    public function アップデート()
    {
        // アップデートで新たしいカラムが追加される
        $this->columns[] = 'dtb_dash.new_column';

        return parent::アップデート();
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
        $this->tables[] = 'dtb_dash';
        $this->columns[] = 'dtb_cart.is_horizon';
        $this->columns[] = 'dtb_cart.dash_id';
        $this->traits['\Plugin\Horizon\Entity\CartTrait'] = 'src/Eccube/Entity/Cart';
    }

    public function アップデート()
    {
        // アップデートで新たしいカラムが追加される
        $this->columns[] = 'dtb_dash.new_column';

        return parent::アップデート();
    }

    public static function start(AcceptanceTester $I)
    {
        $result = new self($I);

        return $result;
    }

    public function 依存されているのが有効なのに無効化()
    {
        $this->ManagePage->ストアプラグイン_無効化($this->code, '「ホライゾン」を無効にする前に、「エンペラー」を無効にしてください。');

        $this->検証();

        $this->em->refresh($this->Plugin);
        $this->I->assertTrue($this->Plugin->isInitialized(), '初期化されているはず');
        $this->I->assertTrue($this->Plugin->isEnabled(), '有効化されているはず');

        return $this;
    }

    public function 依存されているのが削除されていないのに削除()
    {
        $this->ManagePage->ストアプラグイン_削除($this->code, '「エンペラー」が「ホライゾン」に依存しているため削除できません。');

        $this->検証();

        $this->em->refresh($this->Plugin);
        $this->Plugin = $this->pluginRepository->findByCode($this->code);
        $this->I->assertNotNull($this->Plugin, '削除されていない');

        return $this;
    }
}

class Emperor_Store extends Store_Plugin
{
    public function __construct(AcceptanceTester $I, Store_Plugin $dependency = null)
    {
        parent::__construct($I, 'Emperor', $dependency);
        $this->publishPlugin('Horizon-1.0.0.tgz');
        $this->tables[] = 'dtb_foo';
        $this->columns[] = 'dtb_cart.foo_id';
        $this->traits['\Plugin\Emperor\Entity\CartTrait'] = 'src/Eccube/Entity/Cart';
    }

    public static function start(AcceptanceTester $I, Store_Plugin $dependency = null)
    {
        return new self($I, $dependency);
    }

    public function アップデート()
    {
        $this->tables = ['dtb_bar'];
        $this->columns = ['dtb_cart.bar_id'];
        $this->traits['\Plugin\Emperor\Entity\Cart2Trait'] = 'src/Eccube/Entity/Cart';

        return parent::アップデート();
    }

    public function 依存より先に有効化()
    {
        $this->ManagePage->ストアプラグイン_有効化($this->code, '「ホライゾン」を先に有効化してください。');

        $this->検証();

        $this->em->refresh($this->Plugin);
        $this->I->assertFalse($this->Plugin->isInitialized(), '初期化されていないはず');
        $this->I->assertFalse($this->Plugin->isEnabled(), '有効化されていないはず');

        return $this;
    }
}

class Boomerang_Store extends Store_Plugin
{
    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I, 'Boomerang');
        $this->tables[] = 'dtb_bar';
        $this->columns[] = 'dtb_cart.is_boomerang';
        $this->columns[] = 'dtb_cart.bar_id';
        $this->traits['\Plugin\Boomerang\Entity\CartTrait'] = 'src/Eccube/Entity/Cart';
    }

    public static function start(AcceptanceTester $I)
    {
        return new self($I);
    }

    public function カート一覧()
    {
        $this->I->amOnPage('/boomerang');
    }

    public function カート作成()
    {
        $this->I->amOnPage('/boomerang/new');
        $this->I->seeCurrentUrlMatches('/^\/boomerang$/');

        return $this;
    }
}

class Boomerang10_Store extends Store_Plugin
{
    public function __construct(AcceptanceTester $I, Store_Plugin $dependency = null)
    {
        parent::__construct($I, 'Boomerang10', $dependency);
        $this->columns[] = 'dtb_bar.mail';
    }

    public static function start(AcceptanceTester $I, Store_Plugin $dependency = null)
    {
        return new self($I, $dependency = null);
    }
}

class Boomerang_Local extends Local_Plugin
{
    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I, 'Boomerang');
        $this->tables[] = 'dtb_bar';
        $this->columns[] = 'dtb_cart.is_boomerang';
        $this->traits['\Plugin\Boomerang\Entity\CartTrait'] = 'src/Eccube/Entity/Cart';
    }

    public static function start(AcceptanceTester $I)
    {
        return new self($I);
    }
}

class Bundle_Store extends Store_Plugin
{
    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I, 'Bundle');
        $this->tables[] = 'oauth2_client';
        $this->tables[] = 'oauth2_refresh_token';
        $this->tables[] = 'oauth2_access_token';
        $this->tables[] = 'oauth2_authorization_code';
    }

    public function 有効化()
    {
        parent::有効化();

        return $this;
    }

    public function 無効化()
    {
        parent::無効化();

        return $this;
    }

    public static function start(AcceptanceTester $I)
    {
        return new self($I);
    }
}
