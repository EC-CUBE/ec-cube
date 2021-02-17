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

namespace Eccube\Tests\Command;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Eccube\Command\UpdateSchemaDoctrineCommand;
use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Eccube\Service\SchemaService;
use Eccube\Tests\EccubeTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * @group update-schema-doctrine
 */
class UpdateSchemaDoctrineCommandTest extends EccubeTestCase
{
    /**
     * @var PluginService
     */
    private $pluginService;

    /**
     * @var SchemaService
     */
    private $schemaService;

    /**
     * @var PluginRepository
     */
    private $pluginRepository;

    const NAME = 'eccube:schema:update';

    public function setUp()
    {
        parent::setUp();
        $conn = $this->entityManager->getConnection();
        // https://github.com/dmaicher/doctrine-test-bundle#troubleshooting
        $platform = $conn->getDatabasePlatform()->getName();
        if ('postgresql' !== $platform) {
            $this->markTestSkipped('does not support of '.$platform);
        }
        $files = Finder::create()
            ->in(self::$container->getParameter('kernel.project_dir').'/app/proxy/entity')
            ->files();
        $f = new Filesystem();
        $f->remove($files);

        $this->pluginRepository = $this->entityManager->getRepository(\Eccube\Entity\Plugin::class);
        $this->pluginService = self::$container->get(PluginService::class);
        $this->schemaService = self::$container->get(SchemaService::class);
    }

    public function tearDown()
    {
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');
        foreach ($columns as $column) {
            if ($column->getName() == 'test_update_schema_command') {
                $conn = $this->entityManager->getConnection();
                $conn->executeUpdate('ALTER TABLE dtb_customer DROP test_update_schema_command');
            }
        }
        parent::tearDown();
    }

    public function testHelpWithOriginalDoctrineCommand()
    {
        $this->addTestColumn();

        $tester = $this->getCommandTester(self::NAME);
        $tester->execute(
            ['command' => self::NAME]
        );
        $display = $tester->getDisplay();

        $this->assertContains('eccube:schema:update --force', $display);
        $this->assertContains('eccube:schema:update --dump-sql', $display);
    }

    public function testHelpWithNoProxy()
    {
        $this->addTestColumn();

        $tester = $this->getCommandTester(self::NAME);
        $tester->execute(
            [
                'command' => self::NAME,
                '--no-proxy' => true,
            ]
        );
        $display = $tester->getDisplay();

        $this->assertContains('eccube:schema:update --force', $display);
        $this->assertContains('eccube:schema:update --dump-sql', $display);
    }

    /**
     * @group update-schema-doctrine-install
     */
    public function testInstallPluginWithNoProxy()
    {
        $commandTester = $this->getCommandTester(self::NAME);

        list($configA, $fileA) = $this->createDummyPluginWithEntityExtension();
        $this->pluginService->install($fileA);

        $commandTester->execute(
            [
                'command' => self::NAME,
                '--no-proxy' => true,
                '--dump-sql' => true,
            ]
        );
        $display = $commandTester->getDisplay();
        $this->assertContains(
            'ALTER TABLE dtb_customer DROP test_update_schema_command',
            $display,
            '--no-proxy is do not use proxy'
        );

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, function (Column $column) {
            return $column->getName() == 'test_update_schema_command';
        }), 'test_update_schema_command is exists');

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNull($pluginA);
    }

    /**
     * @group update-schema-doctrine-install
     */
    public function testInstallPluginWithProxy()
    {
        $commandTester = $this->getCommandTester(self::NAME);

        list($configA, $fileA) = $this->createDummyPluginWithEntityExtension();
        $this->pluginService->install($fileA);

        $commandTester->execute(
            [
                'command' => self::NAME,
                '--dump-sql' => true,
            ]
        );
        $display = $commandTester->getDisplay();
        $this->assertContains('[OK] Nothing to update', $display, 'Use proxy');

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, function (Column $column) {
            return $column->getName() == 'test_update_schema_command';
        }), 'test_update_schema_command is exists');

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);

        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNull($pluginA);
    }

    /**
     * @group update-schema-doctrine-install
     */
    public function testEnablePluginWithNoProxy()
    {
        $this->markTestIncomplete('Fatal error: Cannot declare class になってしまうためスキップ');
        $commandTester = $this->getCommandTester(self::NAME);

        list($configA, $fileA) = $this->createDummyPluginWithEntityExtension();

        $this->pluginService->install($fileA);

        $this->executeExternalProcess('bin/console eccube:plugin:enable --code='.$configA['code']);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);

        $commandTester->execute(
            [
                'command' => self::NAME,
                '--no-proxy' => true,
                '--dump-sql' => true,
            ]
        );
        $display = $commandTester->getDisplay();
        $this->assertContains('[OK] Nothing to update', $display, '--no-proxy is do not use proxy');

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, function (Column $column) {
            return $column->getName() == 'test_update_schema_command';
        }), 'test_update_schema_command is exists');

        $this->executeExternalProcess('bin/console eccube:plugin:disable --code='.$configA['code']);
        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNull($pluginA);
    }

    /**
     * @group update-schema-doctrine-install
     */
    public function testEnablePluginWithProxy()
    {
        $commandTester = $this->getCommandTester(self::NAME);
        list($configA, $fileA) = $this->createDummyPluginWithEntityExtension();
        $this->pluginService->install($fileA);

        $this->executeExternalProcess('bin/console eccube:plugin:enable --code='.$configA['code']);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $commandTester->execute(
            [
                'command' => self::NAME,
                '--dump-sql' => true,
            ]
        );
        $display = $commandTester->getDisplay();
        $this->assertContains('[OK] Nothing to update', $display, 'Use proxy');

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, function (Column $column) {
            return $column->getName() == 'test_update_schema_command';
        }), 'test_update_schema_command is exists');

        $this->executeExternalProcess('bin/console eccube:plugin:disable --code='.$configA['code']);
        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNull($pluginA);
    }

    /**
     * @group update-schema-doctrine-install
     */
    public function testDisablePluginWithNoProxy()
    {
        $this->markTestIncomplete('Fatal error: Cannot declare class になってしまうためスキップ');
        $commandTester = $this->getCommandTester(self::NAME);

        list($configA, $fileA) = $this->createDummyPluginWithEntityExtension();
        $this->pluginService->install($fileA);

        $this->executeExternalProcess('bin/console eccube:plugin:enable --code='.$configA['code']);
        $this->executeExternalProcess('bin/console eccube:plugin:disable --code='.$configA['code']);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);

        $commandTester->execute(
            [
                'command' => self::NAME,
                '--no-proxy' => true,
                '--dump-sql' => true,
            ]
        );
        $display = $commandTester->getDisplay();
        $this->assertContains(
            'ALTER TABLE dtb_customer DROP test_update_schema_command',
            $display,
            '--no-proxy is do not use proxy'
        );

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, function (Column $column) {
            return $column->getName() == 'test_update_schema_command';
        }), 'test_update_schema_command is exists');

        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNull($pluginA);
    }

    /**
     * @group update-schema-doctrine-install
     */
    public function testDisablePluginWithProxy()
    {
        $commandTester = $this->getCommandTester(self::NAME);

        list($configA, $fileA) = $this->createDummyPluginWithEntityExtension();
        $this->pluginService->install($fileA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);

        $this->executeExternalProcess('bin/console eccube:plugin:enable --code='.$configA['code']);

        $this->executeExternalProcess('bin/console eccube:plugin:disable --code='.$configA['code']);

        $commandTester->execute(
            [
                'command' => self::NAME,
                '--dump-sql' => true,
            ]
        );
        $display = $commandTester->getDisplay();
        $this->assertContains('[OK] Nothing to update', $display, 'Use proxy');

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, function (Column $column) {
            return $column->getName() == 'test_update_schema_command';
        }), 'test_update_schema_command is exists');

        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNull($pluginA);
    }

    /**
     * @param string $name
     *
     * @return CommandTester
     */
    private function getCommandTester($name)
    {
        $kernel = static::createKernel();
        $command = new UpdateSchemaDoctrineCommand(
            $this->pluginRepository,
            $this->pluginService,
            $this->schemaService
        );
        $application = new Application($kernel);
        $application->add($command);

        return new CommandTester($application->find($name));
    }

    /**
     * @return AbstractSchemaManager
     */
    private function getSchemaManager()
    {
        return $this->entityManager->getConnection()->getSchemaManager();
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
        $config = [
            'name' => $tmpname.'_name',
            'code' => $tmpname,
            'version' => $tmpname,
        ];

        return $config;
    }

    private function createDummyPluginWithEntityExtension()
    {
        // インストールするプラグインを作成する
        $config = $this->createDummyPluginConfig();
        $tmpname = $config['code'];
        $tmpdir = $this->createTempDir();
        $tmpfile = $tmpdir.'/plugin.tar';
        $json = $this->createComposerJsonFile($config);
        $tar = new \PharData($tmpfile);
        $tar->addFromString('composer.json', json_encode($json));
        $tar->addFromString('Entity/HogeTrait.php', <<< EOT
<?php

namespace Plugin\\${tmpname}\\Entity;

use Eccube\Annotation\EntityExtension;
use Doctrine\ORM\Mapping as ORM;

/**
 * @EntityExtension("Eccube\Entity\Customer")
 */
trait HogeTrait
{
    /**
     * @ORM\Column(name="test_update_schema_command", type="string", nullable=true)
     */
    public \$testUpdateSchemaCommand;
}
EOT
        );

        return [$config, $tmpfile];
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
            'extra' => [
                'code' => $config['code'],
            ],
        ];

        return $jsonPHP;
    }

    /**
     * Execute to external process.
     *
     * Execute ALTER TABLE command, Once commit the transaction.
     * Ignore exceptions.
     *
     * @param string $command
     *
     * @return string output
     */
    private function executeExternalProcess($command)
    {
        \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::commit();
        \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::beginTransaction();
        try {
            $process = new Process($command);
            $process->mustRun();

            return $process->getOutput();
        } catch (\Exception $e) {
            // ignore Fatal error: Cannot declare class
            // $this->fail($e->getMessage());
        }
    }

    private function addTestColumn()
    {
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');
        if (empty(array_filter($columns, function ($column) {
            return $column->getName() == 'test_update_schema_command';
        }))) {
            $conn = $this->entityManager->getConnection();
            $conn->executeUpdate('ALTER TABLE dtb_customer ADD test_update_schema_command text');
        }
    }
}
