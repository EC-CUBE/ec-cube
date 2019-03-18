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
use Eccube\Repository\PluginRepository;
use Eccube\Service\Composer\ComposerServiceInterface;
use Eccube\Service\EntityProxyService;
use Eccube\Service\PluginService;
use Eccube\Service\SchemaService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Class PluginServiceTest
 *
 * @group cache-clear
 */
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
        $prop->setValue($this->service, $this->createMock(ComposerServiceInterface::class));

        $prop = $rc->getProperty('entityProxyService');
        $prop->setAccessible(true);
        $prop->setValue($this->service, $this->createMock(EntityProxyService::class));

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

        $this->deleteAllRows(['dtb_plugin']);

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
        $config = [
            'version' => $tmpname.'_version',
            'description' => $tmpname.'_name',
            'extra' => [
                'code' => $tmpname,
            ],
        ];

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('composer.json', json_encode($config));

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

    // インストーラが例外を上げた場合ロールバックできるか
    public function testInstallPluginWithBrokenManagerAfterInstall()
    {
        // インストールするプラグインを作成する
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [
            'version' => $tmpname,
            'description' => $tmpname,
            'extra' => [
                'code' => $tmpname,
            ],
        ];

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('composer.json', json_encode($config));
        $dummyManager = <<<'EOD'
<?php
namespace Plugin\@@@@ ;

use Eccube\Plugin\AbstractPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function install(array $meta, ContainerInterface $container)
    {
        echo "";
    }
    public function uninstall(array $meta, ContainerInterface $container)
    {
        throw new \Exception('hoge',1);
    }
    public function enable(array $meta, ContainerInterface $container)
    {
        throw new \Exception('hoge',1);
    }
    public function disable(array $meta, ContainerInterface $container)
    {
        throw new \Exception('hoge',1);
    }
    public function update(array $meta, ContainerInterface $container)
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
        $config = [
            'version' => $tmpname,
            'description' => $tmpname,
            'extra' => [
                'code' => $tmpname,
            ],
        ];

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('composer.json', json_encode($config));
        $dummyManager = <<<'EOD'
<?php
namespace Plugin\@@@@ ;

use Eccube\Plugin\AbstractPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function install(array $meta, ContainerInterface $container)
    {
        echo "Installed";
    }

    public function uninstall(array $meta, ContainerInterface $container)
    {
        echo "Uninstalled";
    }

    public function enable(array $meta, ContainerInterface $container)
    {
        echo "Enabled";
    }

    public function disable(array $meta, ContainerInterface $container)
    {
        echo "Disabled";
    }

    public function update(array $meta, ContainerInterface $container)
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

    public function testRemoveAssets()
    {
        $code = 'remove_assets_dir';
        $dir = $this->eccubeConfig['plugin_html_realdir'].$code;
        mkdir($dir, 0777, true);

        $this->assertFileExists($dir);

        $this->service->removeAssets($code);

        $this->assertFileNotExists($dir);
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
                'ec-cube/plugin-installer' => '*'
                 ],
            'extra' => [
                'code' => $config['code'],
            ],
        ];

        return $jsonPHP;
    }
}
