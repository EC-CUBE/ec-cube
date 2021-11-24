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

namespace Eccube\Tests\Service;

use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Eccube\Service\SchemaService;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PluginServiceWithEntityExtensionTest extends AbstractServiceTestCase
{
    /**
     * @var PluginService
     */
    private $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockSchemaService;

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
        // Fixme: because the proxy entity still not working, it's can not help to run this test case
        $this->markTestIncomplete('Fatal error: Cannot declare class Eccube\Entity\BaseInfo, because the name is already in use in app\proxy\entity\BaseInfo.php on line 28');

        parent::setUp();

        $this->mockSchemaService = $this->createMock(SchemaService::class);
        $this->service = self::$container->get(PluginService::class);
        $rc = new \ReflectionClass($this->service);
        $prop = $rc->getProperty('schemaService');
        $prop->setAccessible(true);
        $prop->setValue($this->service, $this->mockSchemaService);

        $this->pluginRepository = $this->entityManager->getRepository(\Eccube\Entity\Plugin::class);
    }

    public function tearDown()
    {
        $finder = new Finder();
        $iterator = $finder
            ->in(self::$container->getParameter('kernel.project_dir').'/app/Plugin')
            ->name('dummy*')
            ->directories();

        $dirs = [];
        foreach ($iterator as $dir) {
            $dirs[] = $dir->getPathName();
        }

        foreach ($dirs as $dir) {
            $this->deleteFile($dir);
        }

        $files = Finder::create()
            ->in(self::$container->getParameter('kernel.project_dir').'/app/proxy/entity')
            ->files();
        $f = new Filesystem();
        $f->remove($files);

        parent::tearDown();
    }

    public function deleteFile($path)
    {
        $f = new Filesystem();

        return $f->remove($path);
    }

    /**
     * プラグインをインストールしたときにスキーマが更新される
     */
    public function testInstallPlugin()
    {
        list($configA, $fileA) = $this->createDummyPluginWithEntityExtension();

        // スキーマ更新されるはず
        $this->mockSchemaService->expects($this->once())->method('updateSchema');

        // インストール
        $this->service->install($fileA);

        // Proxyは生成されない
        self::assertFalse(file_exists(self::$container->getParameter('kernel.project_dir').'/app/proxy/entity/Customer.php'));
    }

    /**
     * プラグインを有効化するとプロキシが生成される
     */
    public function testEnablePlugin()
    {
        list($configA, $fileA) = $this->createDummyPluginWithEntityExtension();

        // インストール
        $this->service->install($fileA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->entityManager->detach($pluginA);

        // 有効化
        $this->service->enable($pluginA);

        // Traitは有効
        self::assertContainsTrait(
            self::$container->getParameter('kernel.project_dir').'/app/proxy/entity/Customer.php',
            "Plugin\\${configA['code']}\\Entity\\HogeTrait");
    }

    /**
     * プラグインを無効化するとプロキシからTraitが使われなくなる
     */
    public function testDisablePlugin()
    {
        list($configA, $fileA) = $this->createDummyPluginWithEntityExtension();

        // インストール
        $this->service->install($fileA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->entityManager->detach($pluginA);

        // 有効化
        $this->service->enable($pluginA);

        // 無効化
        $this->service->disable($pluginA);

        // Traitは無効
        self::assertNotContainsTrait(
            self::$container->getParameter('kernel.project_dir').'/app/proxy/entity/Customer.php',
            "Plugin\\${configA['code']}\\Entity\\HogeTrait");
    }

    /**
     * プラグインを削除するとスキーマ更新が行われる
     */
    public function testUninstallPlugin()
    {
        list($configA, $fileA) = $this->createDummyPluginWithEntityExtension();

        // インストール
        $this->service->install($fileA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->entityManager->detach($pluginA);

        // 有効化
        $this->service->enable($pluginA);

        // 無効化
        $this->service->disable($pluginA);

        // スキーマ更新されるはず
        $this->mockSchemaService->expects($this->once())->method('updateSchema');

        // 削除
        $this->service->uninstall($pluginA);

        // Traitは無効
        self::assertNotContainsTrait(
            self::$container->getParameter('kernel.project_dir').'/app/proxy/entity/Customer.php',
            "Plugin\\${configA['code']}\\Entity\\HogeTrait");
    }

    /**
     * プラグインを無効化せずに削除してもプロキシの再生成とスキーマ更新が行われる
     */
    public function testImmediatelyUninstallPlugin()
    {
        list($configA, $fileA) = $this->createDummyPluginWithEntityExtension();

        // インストール
        $this->service->install($fileA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->entityManager->detach($pluginA);

        // 有効化
        $this->service->enable($pluginA);

        // スキーマ更新されるはず
        $this->mockSchemaService->expects($this->once())->method('updateSchema');

        // 削除
        $this->service->uninstall($pluginA);

        // Traitは無効
        self::assertNotContainsTrait(
            self::$container->getParameter('kernel.project_dir').'/app/proxy/entity/Customer.php',
            "Plugin\\${configA['code']}\\Entity\\HogeTrait");
    }

    /**
     * インストール済み(無効)のプラグインがある状態で別のプラグインをインストールして有効化する
     */
    public function testInstallWithEntityExtensionWithDisabledPlugin()
    {
        list($configDisabled, $fileDisabled) = $this->createDummyPluginWithEntityExtension();
        list($configEnabled, $fileEnabled) = $this->createDummyPluginWithEntityExtension();

        // スキーマ更新は2回行われるはず
        $this->mockSchemaService->expects($this->exactly(2))->method('updateSchema');

        // プラグイン1はインストールのみ

        $this->service->install($fileDisabled);

        // プラグイン2をインストール&有効化

        $this->service->install($fileEnabled);

        $pluginEnabled = $this->pluginRepository->findOneBy(['code' => $configEnabled['code']]);
        $this->entityManager->detach($pluginEnabled);

        // 有効化
        $this->service->enable($pluginEnabled);

        self::assertNotContainsTrait(self::$container->getParameter('kernel.project_dir').'/app/proxy/entity/Customer.php',
            "Plugin\\${configDisabled['code']}\\Entity\\HogeTrait",
            '無効状態プラグインのTraitは利用されないはず');

        // 無効化状態のTraitは利用されないはず
        self::assertContainsTrait(self::$container->getParameter('kernel.project_dir').'/app/proxy/entity/Customer.php',
            "Plugin\\${configEnabled['code']}\\Entity\\HogeTrait",
            '有効状態のプラグインは利用されるはず');
    }

    private static function assertContainsTrait($file, $trait, $message = 'Traitが有効になっているはず')
    {
        $tokens = Tokens::fromCode(file_get_contents($file));
        $useTraitStart = $tokens->getNextTokenOfKind(0, [[CT::T_USE_TRAIT]]);
        $useTraitEnd = $tokens->getNextTokenOfKind($useTraitStart, [';']);
        $useStatement = $tokens->generatePartialCode($useTraitStart, $useTraitEnd);

        self::assertContains($trait, $useStatement, $message);
    }

    private static function assertNotContainsTrait($file, $trait, $message = 'Traitが有効になっているはず')
    {
        $tokens = Tokens::fromCode(file_get_contents($file));
        $useTraitStart = $tokens->getNextTokenOfKind(0, [[CT::T_USE_TRAIT]]);
        $useTraitEnd = $tokens->getNextTokenOfKind($useTraitStart, [';']);
        $useStatement = $tokens->generatePartialCode($useTraitStart, $useTraitEnd);

        self::assertNotContains($trait, $useStatement, $message);
    }

    // テスト用のダミープラグインを配置する
    private function createTempDir()
    {
        $t = sys_get_temp_dir().'/plugintest.'.sha1(mt_rand());
        if (!mkdir($t)) {
            throw new \Exception("$t ".$php_errormsg);
        }

        return $t;
    }

    private function createDummyPluginConfig()
    {
        $tmpname = 'dummy'.sha1(mt_rand());
        $config = [];
        $config['name'] = $tmpname.'_name';
        $config['code'] = $tmpname;
        $config['version'] = $tmpname;
        $config['event'] = 'DummyEvent';

        return $config;
    }

    private function createDummyPluginWithEntityExtension()
    {
        // インストールするプラグインを作成する
        $config = $this->createDummyPluginConfig();
        $tmpname = $config['code'];

        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';

        $tar = new \PharData($tmpfile);
        $tar->addFromString('composer.json', json_encode($config));
        $tar->addFromString('Entity/HogeTrait.php', <<< EOT
<?php

namespace Plugin\\${tmpname}\\Entity;

use Eccube\Annotation\EntityExtension;

/**
 * @EntityExtension("Eccube\Entity\Customer")
 */
trait HogeTrait
{
}
EOT
        );

        return [$config, $tmpfile];
    }
}
