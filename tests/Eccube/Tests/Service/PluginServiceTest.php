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

namespace Eccube\Tests\Service;

use Eccube\Common\Constant;
use Eccube\Exception\PluginException;
use Eccube\Plugin\ConfigManager;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Composer\ComposerApiService;
use Eccube\Service\PluginService;
use Eccube\Service\SchemaService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class PluginServiceTest extends AbstractServiceTestCase
{
    /**
     * @var PluginService
     */
    private $service;

    /**
     * @var PluginRepository
     */
    private $pluginRepository;

    /**
     * {@inheritdoc}
     *
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();

        $this->service = $this->container->get(PluginService::class);
        $rc = new \ReflectionClass($this->service);
        $prop = $rc->getProperty('schemaService');
        $prop->setAccessible(true);
        $prop->setValue($this->service, $this->createMock(SchemaService::class));
        $prop = $rc->getProperty('composerService');
        $prop->setAccessible(true);
        $prop->setValue($this->service, $this->createMock(ComposerApiService::class));
        $this->pluginRepository = $this->container->get(PluginRepository::class);
    }

    public function tearDown()
    {
        $dirs = [];
        $finder = new Finder();
        $iterator = $finder
            ->in($this->container->getParameter('kernel.project_dir').'/app/Plugin')
            ->name('dummy*')
            ->directories();
        foreach ($iterator as $dir) {
            $dirs[] = $dir->getPathName();
        }

        foreach ($dirs as $dir) {
            $this->deleteFile($dir);
        }

        foreach (glob($this->container->getParameter('kernel.project_dir').'/app/proxy/entity/*.php') as $file) {
            unlink($file);
        }

        $this->deleteAllRows(['dtb_plugin_event_handler', 'dtb_plugin']);

        parent::tearDown();
    }

    /*
       正しいプラグインの条件
       * tar/zipアーカイブである
       * 展開した直下のディレクトリにconfig.ymlがあり、正しいymlファイルである
       * config.ymlの必須要素が規定の文字数、文字種で定義されている
       * event.ymlが存在する場合、正しいymlである

     */

    // テスト用のダミープラグインを配置する
    private function createTempDir()
    {
        $t = sys_get_temp_dir().'/plugintest.'.sha1(mt_rand());
        if (!mkdir($t)) {
            throw new \Exception("$t ".$php_errormsg);
        }

        return $t;
    }

    public function deleteFile($path)
    {
        $f = new Filesystem();

        return $f->remove($path);
    }

    // 必要最小限のファイルのプラグインのインストールとアンインストールを検証
    public function testInstallPluginMinimum()
    {
        // インストールするプラグインを作成する
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['name'] = $tmpname.'_name';
        $config['code'] = $tmpname;
        $config['version'] = $tmpname.'_version';

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));

        // インストールできるか
        $this->assertTrue($this->service->install($tmpfile));

        try {
            $this->service->install($tmpfile);
            $this->fail('checkSamePlugin dont throw exception.');
        } catch (\Eccube\Exception\PluginException $e) {
        } catch (\Exception $e) {
            $this->fail('checkSamePlugin throw unexpected exception.'.$e->toString());
        }
        // 同じプラグインの二重インストールが蹴られるか

        // アンインストールできるか
        $this->assertTrue((bool) $plugin = $this->pluginRepository->findOneBy(['code' => $tmpname]));
        $this->assertEquals(Constant::DISABLED, $plugin->isEnabled());
        $this->assertTrue($this->service->uninstall($plugin));
    }

    /**
     * 必須ファイルがないプラグインがインストール出来ないこと
     *
     * @expectedException \Eccube\Exception\PluginException
     * @exceptedExceptionMessage config.yml not found or syntax error
     */
    public function testInstallPluginEmptyError()
    {
        // インストールするプラグインを作成する
        $tmpname = 'dummy'.sha1(mt_rand());
        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('dummy', 'dummy');
        // インストールできるか
        $this->service->install($tmpfile);
    }

    // config.ymlのフォーマット確認
    public function testConfigYmlFormat()
    {
        $tmpname = 'dummy'.mt_rand();
        $tmpfile = sys_get_temp_dir().'/dummy'.mt_rand();

        // 必須項目のチェック
        $config = [];
        //$config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        try {
            file_put_contents($tmpfile, Yaml::dump($config));
            $this->service->checkPluginArchiveContent($tmpfile);
            $this->fail('testConfigYmlFormat dont throw exception.');
        } catch (\Eccube\Exception\PluginException $e) {
        }

        $config = [];
        $config['name'] = $tmpname;
        //$config['code'] = $tmpname;
        $config['version'] = $tmpname;
        try {
            file_put_contents($tmpfile, Yaml::dump($config));
            $this->service->checkPluginArchiveContent($tmpfile);
            $this->fail('testConfigYmlFormat dont throw exception.');
        } catch (\Eccube\Exception\PluginException $e) {
        }

        $config = [];
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        //$config['version'] = $tmpname;
        try {
            file_put_contents($tmpfile, Yaml::dump($config));
            $this->service->checkPluginArchiveContent($tmpfile);
            $this->fail('testConfigYmlFormat dont throw exception.');
        } catch (\Eccube\Exception\PluginException $e) {
        }

        // 禁止文字のチェック

        $config['name'] = $tmpname.'@';
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        try {
            file_put_contents($tmpfile, Yaml::dump($config));
            $this->service->checkPluginArchiveContent($tmpfile);
            $this->fail('testConfigYmlFormat dont throw exception.');
        } catch (\Eccube\Exception\PluginException $e) {
        }

        $config = [];
        $config['name'] = $tmpname;
        $config['code'] = $tmpname.'#';
        $config['version'] = $tmpname;
        try {
            file_put_contents($tmpfile, Yaml::dump($config));
            $this->service->checkPluginArchiveContent($tmpfile);
            $this->fail('testConfigYmlFormat dont throw exception.');
        } catch (\Eccube\Exception\PluginException $e) {
        }

        // 長さのチェック
        $config = [];
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = str_repeat('a', 256);
        try {
            file_put_contents($tmpfile, Yaml::dump($config));
            $this->service->checkPluginArchiveContent($tmpfile);
            $this->fail('testConfigYmlFormat dont throw exception.');
        } catch (\Eccube\Exception\PluginException $e) {
        }

        $this->expectException(PluginException::class);
        $config = [];
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        $config['event'] = '&'.$tmpname;
        file_put_contents($tmpfile, Yaml::dump($config));
        $this->service->checkPluginArchiveContent($tmpfile);
    }

    /**
     * config.ymlに異常な項目がある場合
     *
     * @expectedException \Eccube\Exception\PluginException
     * @exceptedExceptionMessage config.yml name empty
     */
    public function testnstallPluginMalformedConfigError()
    {
        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';
        $tar = new \PharData($tmpfile);

        // インストールするプラグインを作成する
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        $tar->addFromString('config.yml', Yaml::dump($config));

        // インストールできないはず
        $this->assertNull($this->service->install($tmpfile));
    }

    // イベント定義を含むプラグインのインストールとアンインストールを検証
    public function testInstallPluginWithEvent()
    {
        // インストールするプラグインを作成する
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['name'] = $tmpname.'_name';
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        $config['event'] = 'DummyEvent';

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));

        $dummyEvent = <<<'EOD'
<?php
namespace Plugin\@@@@ ;


class DummyEvent
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    public function dummyHandler()
    {
        echo "dummyHandler\n";
    }
    public function dummyHandlerFirst()
    {
        echo "dummyHandlerFirst\n";
    }
    public function dummyHandlerLast()
    {
        echo "dummyHandlerLast\n";
    }

}

EOD;
        $dummyEvent = str_replace('@@@@', $tmpname, $dummyEvent); // イベントクラス名はランダムなのでヒアドキュメントの@@@@部分を置換
        $tar->addFromString('DummyEvent.php', $dummyEvent);

        // イベント定義を作成する
        $event = [];
        $event['eccube.event.app.before'] = [];
        $event['eccube.event.app.before'][] = ['dummyHandler', 'NORMAL'];
        $event['eccube.event.app.before'][] = ['dummyHandlerFirst', 'FIRST'];
        $event['eccube.event.app.after'] = [];
        $event['eccube.event.app.after'][] = ['dummyHandlerLast', 'LAST'];
        $tar->addFromString('event.yml', Yaml::dump($event));

        // インストールできるか
        $this->assertTrue($this->service->install($tmpfile));
        $rep = $this->pluginRepository;

        $plugin = $rep->findOneBy(['code' => $tmpname]); // EntityManagerの内部状態を一旦クリア // associationがうまく取れないため
        $this->entityManager->detach($plugin);

        // インストールした内容は正しいか
        // config.ymlとdtb_pluginの内容を照合
        $this->assertTrue((bool) $plugin = $rep->findOneBy(['code' => $tmpname]));
        $this->assertEquals($plugin->getClassName(), 'DummyEvent');
        $this->assertEquals($plugin->getName(), $tmpname.'_name');
        $this->assertEquals($plugin->getVersion(), $tmpname);

        // event.ymlとdtb_plugin_event_handlerの内容を照合(優先度、ハンドラメソッド名、イベント名)
        $this->assertEquals(3, count($plugin->getPluginEventHandlers()->toArray()));

        foreach ($plugin->getPluginEventHandlers() as $handler) {
            if ($handler->getHandlerType() == \Eccube\Entity\PluginEventHandler::EVENT_HANDLER_TYPE_NORMAL) {
                $this->assertGreaterThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_NORMAL_END, $handler->getPriority());
                $this->assertLessThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_NORMAL_START, $handler->getPriority());
                $this->assertEquals('dummyHandler', $handler->getHandler());
                $this->assertEquals('eccube.event.app.before', $handler->getEvent());
            }
            if ($handler->getHandlerType() == \Eccube\Entity\PluginEventHandler::EVENT_HANDLER_TYPE_FIRST) {
                $this->assertGreaterThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_FIRST_END, $handler->getPriority());
                $this->assertLessThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_FIRST_START, $handler->getPriority());
                $this->assertEquals('dummyHandlerFirst', $handler->getHandler());
                $this->assertEquals('eccube.event.app.before', $handler->getEvent());
            }
            if ($handler->getHandlerType() == \Eccube\Entity\PluginEventHandler::EVENT_HANDLER_TYPE_LAST) {
                $this->assertGreaterThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_LAST_END, $handler->getPriority());
                $this->assertLessThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_LAST_START, $handler->getPriority());
                $this->assertEquals('dummyHandlerLast', $handler->getHandler());
                $this->assertEquals('eccube.event.app.after', $handler->getEvent());
            }
        }

        // ちゃんとファイルが展開されているか
        $this->assertFileExists(__DIR__."/../../../../app/Plugin/$tmpname/config.yml");
        $this->assertFileExists(__DIR__."/../../../../app/Plugin/$tmpname/event.yml");
        $this->assertFileExists(__DIR__."/../../../../app/Plugin/$tmpname/DummyEvent.php");

        // enable/disableできるか
        $this->assertTrue($this->service->disable($plugin));
        $this->assertTrue($this->service->enable($plugin));

        // イベント定義を更新する
        $event = [];
        $event['eccube.event.controller.cart.after'] = [];
        $event['eccube.event.controller.cart.after'][] = ['dummyCartHandlerLast', 'LAST'];
        $event['eccube.event.app.before'] = [];
        $event['eccube.event.app.before'][] = ['dummyHandler', 'NORMAL'];
        $event['eccube.event.app.after'] = [];
        $event['eccube.event.app.after'][] = ['dummyHandlerLast', 'LAST'];
        $tar->addFromString('event.yml', Yaml::dump($event));

        // config.ymlを更新する
        $config = [];
        $config['name'] = $tmpname.'_name';
        $config['code'] = $tmpname;
        $config['version'] = $tmpname.'u';
        $config['event'] = 'DummyEvent';
        $tar->addFromString('config.yml', Yaml::dump($config));

        $tar->addFromString('update_dummy', 'update dummy');

        // updateできるか
        $this->assertTrue($this->service->update($plugin, $tmpfile));
        $this->assertEquals($plugin->getVersion(), $tmpname.'u');

        // イベントハンドラが新しいevent.ymlと整合しているか(追加、削除)
        $this->entityManager->detach($plugin);
        $this->assertTrue((bool) $plugin = $rep->findOneBy(['code' => $tmpname]));
        $this->assertEquals(3, count($plugin->getPluginEventHandlers()->toArray()));

        foreach ($plugin->getPluginEventHandlers() as $handler) {
            if ($handler->getHandlerType() == \Eccube\Entity\PluginEventHandler::EVENT_HANDLER_TYPE_NORMAL) {
                $this->assertGreaterThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_NORMAL_END, $handler->getPriority());
                $this->assertLessThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_NORMAL_START, $handler->getPriority());
                $this->assertEquals('dummyHandler', $handler->getHandler());
                $this->assertEquals('eccube.event.app.before', $handler->getEvent());
            }
            if ($handler->getHandlerType() == \Eccube\Entity\PluginEventHandler::EVENT_HANDLER_TYPE_LAST) {
                $this->assertGreaterThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_LAST_END, $handler->getPriority());
                $this->assertLessThanOrEqual(\Eccube\Entity\PluginEventHandler::EVENT_PRIORITY_LAST_START, $handler->getPriority());
                $this->assertContains($handler->getHandler(), ['dummyHandlerLast', 'dummyCartHandlerLast']);
                $this->assertContains($handler->getEvent(), ['eccube.event.app.after', 'eccube.event.controller.cart.after']);
            }
        }
        // 追加されたファイルが配置されているか
        $this->assertFileExists(__DIR__."/../../../../app/Plugin/$tmpname/update_dummy");

        // アンインストールできるか
        $this->assertTrue($this->service->uninstall($plugin));
        // ちゃんとファイルが消えているか
        $this->assertFalse((bool) $rep->findOneBy(['name' => $tmpname, 'enabled' => 1]));
        $this->assertFileNotExists(__DIR__."/../../../../app/Plugin/$tmpname/config.yml");
        $this->assertFileNotExists(__DIR__."/../../../../app/Plugin/$tmpname/event.yml");
        $this->assertFileNotExists(__DIR__."/../../../../app/Plugin/$tmpname/DummyEvent.php");
    }

    // インストーラが例外を上げた場合ロールバックできるか
    public function testInstallPluginWithBrokenManagerAfterInstall()
    {
        // インストールするプラグインを作成する
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));
        $dummyManager = <<<'EOD'
<?php
namespace Plugin\@@@@ ;

use Eccube\Plugin\AbstractPluginManager;
class PluginManager extends AbstractPluginManager
{
    public function install($plugin,$app)
    {
        echo "";
    }
    public function uninstall($config,$app)
    {
        throw new \Exception('hoge',1);
    }
    public function enable($config,$app)
    {
        throw new \Exception('hoge',1);
    }
    public function disable($config,$app)
    {
        throw new \Exception('hoge',1);
    }
    public function update($config,$app)
    {
        throw new \Exception('hoge',1);
    }

}

EOD;
        $dummyManager = str_replace('@@@@', $tmpname, $dummyManager); // イベントクラス名はランダムなのでヒアドキュメントの@@@@部分を置換
        $tar->addFromString('PluginManager.php', $dummyManager);

        // 正しくインストールでき、enableのハンドラが呼ばれないことを確認
        $this->assertTrue($this->service->install($tmpfile));
        $this->assertTrue((bool) $plugin = $this->pluginRepository->findOneBy(['name' => $tmpname]));
        $this->assertEquals(Constant::DISABLED, $plugin->isEnabled()); // インストール直後にプラグインがdisableになっているか
        try {
            $this->assertTrue($this->service->enable($plugin)); // enableにしようとするが、例外発生
        } catch (\Exception $e) {
        }
        $this->entityManager->detach($plugin);
        $this->assertTrue((bool) $plugin = $this->pluginRepository->findOneBy(['name' => $tmpname]));
        $this->assertEquals(Constant::DISABLED, $plugin->isEnabled()); // プラグインがdisableのままになっていることを確認
    }

    // インストーラを含むプラグインが正しくインストールできるか
    public function testInstallPluginWithManager()
    {
        // インストールするプラグインを作成する
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['name'] = $tmpname;
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));
        $dummyManager = <<<'EOD'
<?php
namespace Plugin\@@@@ ;

use Eccube\Plugin\AbstractPluginManager;
class PluginManager extends AbstractPluginManager
{
    public function install($plugin,$app)
    {
        echo "Installed";
    }

    public function uninstall($config,$app)
    {
        echo "Uninstalled";
    }

    public function enable($config,$app)
    {
        echo "Enabled";
    }

    public function disable($config,$app)
    {
        echo "Disabled";
    }

    public function update($config,$app)
    {
        echo "Updated";
    }
}

EOD;
        $dummyManager = str_replace('@@@@', $tmpname, $dummyManager); // イベントクラス名はランダムなのでヒアドキュメントの@@@@部分を置換
        $tar->addFromString('PluginManager.php', $dummyManager);

        // インストールできるか、インストーラが呼ばれるか
        ob_start();
        $this->assertTrue($this->service->install($tmpfile));
        $this->assertRegexp('/Installed/', ob_get_contents());
        ob_end_clean();
        $this->assertFileExists(__DIR__."/../../../../app/Plugin/$tmpname/PluginManager.php");

        $this->assertTrue((bool) $plugin = $this->pluginRepository->findOneBy(['name' => $tmpname]));

        ob_start();
        $this->service->enable($plugin);
        $this->assertRegexp('/Enabled/', ob_get_contents());
        ob_end_clean();
        ob_start();
        $this->service->disable($plugin);
        $this->assertRegexp('/Disabled/', ob_get_contents());
        ob_end_clean();

        // アンインストールできるか、アンインストーラが呼ばれるか
        ob_start();
        $this->service->disable($plugin);
        $this->assertTrue($this->service->uninstall($plugin));
        $this->assertRegexp('/DisabledUninstalled/', ob_get_contents());
        ob_end_clean();
    }

    // const定義を含むpluginのインストール
    public function testInstallPluginWithConst()
    {
        // インストールするプラグインを作成する
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['name'] = $tmpname.'_name';
        $config['code'] = $tmpname;
        $config['version'] = $tmpname.'_version';
        $config['const']['A'] = 'A';
        $config['const']['C'] = 1;

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));

        // インストールできるか
        $this->assertTrue($this->service->install($tmpfile));

        $this->assertTrue((bool) $plugin = $this->pluginRepository->findOneBy(['code' => $tmpname]));

        // インストール後disable状態でもconstがロードされているか
        // FIXME プラグインのローディングはEccubePluginServiceProviderに移植。再ロードは別途検討。
//        $config = $this->app['config'];
//        $config[$tmpname]['const']['A'] = null;
//        $config[$tmpname]['const']['C'] = null;
//        // const が存在しないのを確認後, 再ロード
//        $this->assertFalse(isset($this->app['config'][$tmpname]['const']['A']));
//        $this->assertFalse(isset($this->app['config'][$tmpname]['const']['C']));
//
//        $this->app->initPluginEventDispatcher();
//        $this->app->loadPlugin();
//        $this->app->boot();
//
//        $this->assertEquals('A',$this->app['config'][$tmpname]['const']['A']);
//        $this->assertEquals('1',$this->app['config'][$tmpname]['const']['C']);

        // アンインストールできるか
        $this->assertTrue($this->service->uninstall($plugin));
    }

    /**
     * プラグイン設定ファイルキャッシュの検証
     */
    public function testPluginConfigCache()
    {
        $this->app['debug'] = false;
        $pluginConfigCache = $this->container->getParameter('kernel.project_dir').'/app/cache/plugin/config_cache.php';

        // 事前にキャッシュを削除しておく
        if (file_exists($pluginConfigCache)) {
            unlink($pluginConfigCache);
        }

        // インストールするプラグインを作成する
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['name'] = $tmpname.'_name';
        $config['code'] = $tmpname;
        $config['version'] = $tmpname.'_version';
        $config['const']['A'] = 'A';
        $config['const']['C'] = 1;

        $event = [
            'eccube.event.app.request' => [
                0 => [
                    0 => 'onAppRequest',
                    1 => 'NORMAL',
                ],
            ],
        ];

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));
        $tar->addFromString('event.yml', Yaml::dump($event));

        // インストールできるか
        $this->assertTrue($this->service->install($tmpfile));

        $this->assertTrue((bool) $plugin = $this->pluginRepository->findOneBy(['code' => $tmpname]));
        $this->entityManager->refresh($plugin);

        $this->expected = realpath($pluginConfigCache);
        $this->actual = realpath(ConfigManager::getPluginConfigCacheFile());
        $this->verify('キャッシュファイル名が一致するか');

        $pluginConfigs = ConfigManager::parsePluginConfigs();
        $this->assertTrue(array_key_exists($tmpname, $pluginConfigs));
        $this->expected = $config;
        $this->actual = $pluginConfigs[$tmpname]['config'];
        $this->verify('parsePluginConfigs の結果が一致するか');

        $this->assertTrue(false !== ConfigManager::writePluginConfigCache(), 'キャッシュファイルが書き込まれるか');

        $this->assertTrue(file_exists($pluginConfigCache), 'キャッシュファイルが存在するか');

        $this->assertTrue(ConfigManager::removePluginConfigCache(), 'キャッシュファイルを削除できるか');

        $this->assertFalse(file_exists($pluginConfigCache), 'キャッシュファイルが削除されているか');

        $pluginConfigs = ConfigManager::getPluginConfigAll();

        $this->assertTrue(file_exists($pluginConfigCache), 'キャッシュファイルが再生成されているか');

        $this->expected = $config;
        $this->actual = $pluginConfigs[$tmpname]['config'];
        $this->verify('getPluginConfigAll の結果が一致するか');

        // インストール後disable状態でもconstがロードされているか
        // FIXME プラグインのローディングはEccubePluginServiceProviderに移植。再ロードは別途検討。
//        $config = $this->app['config'];
//        $config[$tmpname]['const']['A'] = null;
//        $config[$tmpname]['const']['C'] = null;
//        // const が存在しないのを確認後, 再ロード
//        $this->assertFalse(isset($this->app['config'][$tmpname]['const']['A']));
//        $this->assertFalse(isset($this->app['config'][$tmpname]['const']['C']));
//
//        $this->app->initPluginEventDispatcher();
//        $this->app->loadPlugin();
//        $this->app->boot();
//        $this->assertEquals('A',$this->app['config'][$tmpname]['const']['A']);
//        $this->assertEquals('1',$this->app['config'][$tmpname]['const']['C']);

        // アンインストールできるか
        $this->assertTrue($this->service->uninstall($plugin));

        $pluginConfigs = ConfigManager::getPluginConfigAll();
        $this->assertFalse(array_key_exists($tmpname, $pluginConfigs), 'キャッシュからプラグインが削除されているか');
    }

    /**
     * Test getDependentByCode with eccube plugin
     */
    public function testGetDependentByCodeEccubePlugin()
    {
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['name'] = $tmpname.'_name';
        $config['code'] = $tmpname;
        $config['version'] = $tmpname.'_version';

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));
        $jsonPHP = $this->createComposerJsonFile($config);
        $text = json_encode($jsonPHP);
        $tar->addFromString('composer.json', $text);

        // install
        $this->service->install($tmpfile);

        // check require
        $expected = $jsonPHP['require'];
        unset($expected['composer/installers']);
        unset($expected['composer/semver']);
        $actual = $this->service->getDependentByCode($config['code'], PluginService::ECCUBE_LIBRARY);
        $this->assertEquals($expected, $actual);

        // check parser
        $actual2 = $this->service->parseToComposerCommand($actual, false);
        $expected2 = implode(' ', array_keys($expected));
        $this->assertEquals($expected2, $actual2);
    }

    /**
     * Test getDependentByCode with other plugin
     */
    public function testGetDependentByCodeOtherPlugin()
    {
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['name'] = $tmpname.'_name';
        $config['code'] = $tmpname;
        $config['version'] = $tmpname.'_version';

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));
        $jsonPHP = $this->createComposerJsonFile($config);
        $text = json_encode($jsonPHP);
        $tar->addFromString('composer.json', $text);

        // install
        $this->service->install($tmpfile);

        // check get require
        $expected = $jsonPHP['require'];
        unset($expected['ec-cube/plugin-installer']);
        $actual = $this->service->getDependentByCode($config['code'], PluginService::OTHER_LIBRARY);
        $this->assertEquals($expected, $actual);

        // check parser
        $actual2 = $this->service->parseToComposerCommand($actual, false);
        $expected2 = implode(' ', array_keys($expected));
        $this->assertEquals($expected2, $actual2);
    }

    /**
     * Test getDependentByCode with all plugin
     */
    public function testGetDependentByCodeAllPlugin()
    {
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['name'] = $tmpname.'_name';
        $config['code'] = $tmpname;
        $config['version'] = $tmpname.'_version';

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('config.yml', Yaml::dump($config));
        $jsonPHP = $this->createComposerJsonFile($config);
        $text = json_encode($jsonPHP);
        $tar->addFromString('composer.json', $text);

        // install
        $this->service->install($tmpfile);

        // check require
        $expected = $jsonPHP['require'];
        $actual = $this->service->getDependentByCode($config['code']);
        $this->assertEquals($expected, $actual);

        // check parser
        $actual2 = $this->service->parseToComposerCommand($actual);
        $expected2 = '';
        foreach ($expected as $packages => $version) {
            $expected2 .= $packages.':'.$version.' ';
        }
        $this->assertEquals(trim($expected2), $actual2);
    }

    /**
     * @param $config
     *
     * @return array
     */
    private function createComposerJsonFile($config)
    {
        /** @var \Faker\Generator $faker */
        $faker = $this->getFaker();
        $jsonPHP = [
            'name' => $config['name'],
            'description' => $faker->word,
            'version' => $config['version'],
            'type' => 'eccube-plugin',
            'require' => [
                'ec-cube/plugin-installer' => '*',
                'composer/installers' => '*',
                'composer/semver' => '*',
            ],
        ];

        return $jsonPHP;
    }
}
