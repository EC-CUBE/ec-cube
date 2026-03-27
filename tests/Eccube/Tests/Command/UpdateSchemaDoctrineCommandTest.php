<?php

declare(strict_types=1);

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

use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\Persistence\ManagerRegistry;
use Eccube\Command\UpdateSchemaDoctrineCommand;
use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Eccube\Service\SchemaService;
use Eccube\Tests\EccubeTestCase;
use Faker\Generator;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

#[Group('update-schema-doctrine')]
final class UpdateSchemaDoctrineCommandTest extends EccubeTestCase
{
    private ?PluginService $pluginService = null;

    private ?SchemaService $schemaService = null;

    private ?PluginRepository $pluginRepository = null;

    public const NAME = 'eccube:schema:update';

    /**
     * 連続してテストを実行すると、プロキシ関係でテストが失敗する。
     * １メソッドごとに実行すること。
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $conn = $this->entityManager->getConnection();
        // https://github.com/dmaicher/doctrine-test-bundle#troubleshooting
        $platform = $conn->getDatabasePlatform()->getName();
        if ('postgresql' !== $platform) {
            $this->markTestSkipped('does not support of '.$platform);
        }
        $files = Finder::create()
            ->in(static::getContainer()->getParameter('kernel.project_dir').'/app/proxy/entity')
            ->files();
        $f = new Filesystem();
        $f->remove($files);
        $this->pluginRepository = $this->entityManager->getRepository(Plugin::class);
        $this->pluginService = static::getContainer()->get(PluginService::class);
        $this->schemaService = static::getContainer()->get(SchemaService::class);
    }

    #[\Override]
    protected function tearDown(): void
    {
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');
        foreach ($columns as $column) {
            if ($column->getName() == 'test_update_schema_command') {
                $conn = $this->entityManager->getConnection();
                $conn->executeUpdate('ALTER TABLE dtb_customer DROP test_update_schema_command');
            }
        }
        // Restore exception handler to prevent risky test warning
        restore_exception_handler();
        // プロパティをクリア
        // parent::tearDown();
    }

    /**
     * 中のプロパティをクリアしている
     */
    #[\Override]
    public static function tearDownAfterClass(): void
    {
    }

    public function testHelpWithOriginalDoctrineCommand()
    {
        $this->addTestColumn();

        $tester = $this->getCommandTester(self::NAME);
        $tester->execute(
            ['command' => self::NAME]
        );
        $display = $tester->getDisplay();

        $this->assertStringContainsString('eccube:schema:update --force', $display);
        $this->assertStringContainsString('eccube:schema:update --dump-sql', $display);
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

        $this->assertStringContainsString('eccube:schema:update --force', $display);
        $this->assertStringContainsString('eccube:schema:update --dump-sql', $display);
    }

    #[Group(name: 'update-schema-doctrine-install')]
    public function testInstallPluginWithNoProxy(): void
    {
        [$configA, $fileA] = $this->createDummyPluginWithEntityExtension();
        $this->pluginService->install($fileA);

        // Symfony 7.4以降では、同一プロセス内でEntityManagerのメタデータが永続化されるため、
        // 外部プロセスでコマンドを実行してメタデータをリセットする
        $display = $this->executeExternalProcess('bin/console '.self::NAME.' --no-proxy --dump-sql');

        $this->assertStringContainsString(
            'ALTER TABLE dtb_customer DROP test_update_schema_command',
            (string) $display,
            '--no-proxy is do not use proxy'
        );

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, fn (Column $column) => $column->getName() == 'test_update_schema_command'), 'test_update_schema_command is exists');

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNotInstanceOf(Plugin::class, $pluginA);
    }

    #[Group(name: 'update-schema-doctrine-install')]
    public function testInstallPluginWithProxy(): void
    {
        $commandTester = $this->getCommandTester(self::NAME);

        [$configA, $fileA] = $this->createDummyPluginWithEntityExtension();
        $this->pluginService->install($fileA);

        $commandTester->execute(
            [
                'command' => self::NAME,
                '--dump-sql' => true,
            ]
        );
        $display = $commandTester->getDisplay();
        $this->assertStringContainsString('[OK] Nothing to update', $display, 'Use proxy');

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, fn (Column $column) => $column->getName() == 'test_update_schema_command'), 'test_update_schema_command is exists');

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);

        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNotInstanceOf(Plugin::class, $pluginA);
    }

    #[Group(name: 'update-schema-doctrine-install')]
    public function testEnablePluginWithNoProxy(): void
    {
        [$configA, $fileA] = $this->createDummyPluginWithEntityExtension();

        $this->pluginService->install($fileA);

        $this->executeExternalProcess('bin/console eccube:plugin:enable --code='.$configA['code']);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);

        // Symfony 7.4以降では、同一プロセス内でEntityManagerのメタデータが永続化されるため、
        // 外部プロセスでコマンドを実行してメタデータをリセットする
        $display = $this->executeExternalProcess('bin/console '.self::NAME.' --no-proxy --dump-sql');

        $this->assertStringContainsString('[OK] Nothing to update', (string) $display, '--no-proxy is do not use proxy');

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, fn (Column $column) => $column->getName() == 'test_update_schema_command'), 'test_update_schema_command is exists');

        $this->executeExternalProcess('bin/console eccube:plugin:disable --code='.$configA['code']);
        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNotInstanceOf(Plugin::class, $pluginA);
    }

    #[Group(name: 'update-schema-doctrine-install')]
    public function testEnablePluginWithProxy(): void
    {
        $commandTester = $this->getCommandTester(self::NAME);
        [$configA, $fileA] = $this->createDummyPluginWithEntityExtension();
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
        $this->assertStringContainsString('[OK] Nothing to update', $display, 'Use proxy');

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, fn (Column $column) => $column->getName() == 'test_update_schema_command'), 'test_update_schema_command is exists');

        $this->executeExternalProcess('bin/console eccube:plugin:disable --code='.$configA['code']);
        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNotInstanceOf(Plugin::class, $pluginA);
    }

    #[Group(name: 'update-schema-doctrine-install')]
    public function testDisablePluginWithNoProxy(): void
    {
        [$configA, $fileA] = $this->createDummyPluginWithEntityExtension();
        $this->pluginService->install($fileA);

        $this->executeExternalProcess('bin/console eccube:plugin:enable --code='.$configA['code']);
        $this->executeExternalProcess('bin/console eccube:plugin:disable --code='.$configA['code']);

        // プラグインを無効化した後、プロキシファイルを削除してメタデータをクリアする
        $files = Finder::create()
            ->in(static::getContainer()->getParameter('kernel.project_dir').'/app/proxy/entity')
            ->files();
        $f = new Filesystem();
        $f->remove($files);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);

        // Symfony 7.4以降では、同一プロセス内でEntityManagerのメタデータが永続化されるため、
        // 外部プロセスでコマンドを実行してメタデータをリセットする
        $display = $this->executeExternalProcess('bin/console '.self::NAME.' --no-proxy --dump-sql');

        $this->assertStringContainsString(
            'ALTER TABLE dtb_customer DROP test_update_schema_command',
            (string) $display,
            '--no-proxy is do not use proxy'
        );

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, fn (Column $column) => $column->getName() == 'test_update_schema_command'), 'test_update_schema_command is exists');

        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNotInstanceOf(Plugin::class, $pluginA);
    }

    #[Group(name: 'update-schema-doctrine-install')]
    public function testDisablePluginWithProxy(): void
    {
        $commandTester = $this->getCommandTester(self::NAME);

        [$configA, $fileA] = $this->createDummyPluginWithEntityExtension();
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
        $this->assertStringContainsString('[OK] Nothing to update', $display, 'Use proxy');

        /** @var AbstractSchemaManager $schema */
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');

        $this->assertCount(1, array_filter($columns, fn (Column $column) => $column->getName() == 'test_update_schema_command'), 'test_update_schema_command is exists');

        $this->executeExternalProcess('bin/console eccube:plugin:uninstall --code='.$configA['code']);

        $this->entityManager->detach($pluginA);

        $pluginA = $this->pluginRepository->findOneBy(['code' => $configA['code']]);
        $this->assertNotInstanceOf(Plugin::class, $pluginA);
    }

    private function getCommandTester(string $name): CommandTester
    {
        // 既存のカーネルを使う（連続実行時にキャッシュが正しく生成される）
        $kernel = static::$kernel ?? static::createKernel();
        if (!$kernel->getContainer()) {
            $kernel->boot();
        }
        $command = new UpdateSchemaDoctrineCommand(
            $this->pluginRepository,
            $this->pluginService,
            $this->schemaService,
            static::getContainer()->get(ManagerRegistry::class)
        );
        $application = new Application($kernel);
        $application->add($command);

        return new CommandTester($application->find($name));
    }

    private function getSchemaManager(): AbstractSchemaManager
    {
        return $this->entityManager->getConnection()->getSchemaManager();
    }

    // テスト用のダミープラグインを配置する
    private function createTempDir()
    {
        $t = sys_get_temp_dir().'/plugintest.'.sha1((string) mt_rand());
        if (!mkdir($t)) {
            throw new \Exception("$t ".$php_errormsg);
        }

        return $t;
    }

    private function createDummyPluginConfig()
    {
        $tmpname = 'dummy'.sha1((string) mt_rand());

        return [
            'name' => $tmpname.'_name',
            'code' => $tmpname,
            'version' => $tmpname,
        ];
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

namespace Plugin\\{$tmpname}\\Entity;

use Eccube\Attribute\EntityExtension;
use Doctrine\ORM\Mapping as ORM;

 #[\Eccube\Attribute\EntityExtension(\Eccube\Entity\Customer::class)]
trait HogeTrait
{
    #[ORM\Column(name: 'test_update_schema_command', type: 'text', nullable: true)]
    public ?string \$testUpdateSchemaCommand = null;
}
EOT
        );

        return [$config, $tmpfile];
    }

    /**
     * @param $config
     */
    private function createComposerJsonFile($config): array
    {
        /** @var Generator $faker */
        $faker = $this->getFaker();

        return [
            'name' => $config['name'],
            'description' => $faker->word(),
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
    }

    /**
     * Execute to external process.
     *
     * Execute ALTER TABLE command, Once commit the transaction.
     * Ignore exceptions.
     *
     * @return string output
     */
    private function executeExternalProcess(string $command): ?string
    {
        StaticDriver::commit();
        StaticDriver::beginTransaction();
        try {
            $process = new Process(explode(' ', $command));
            $process->mustRun();

            // Symfony ConsoleのOutputInterfaceはstderrに出力する場合がある
            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();

            // 両方を結合して返す（通常はどちらか一方のみに出力される）
            return $output ?: $errorOutput;
        } catch (\Exception) {
            // ignore Fatal error: Cannot declare class
            // $this->fail($e->getMessage());
            return null;
        }
    }

    private function addTestColumn()
    {
        $schema = $this->getSchemaManager();
        $columns = $schema->listTableColumns('dtb_customer');
        if (empty(array_filter($columns, fn ($column) => $column->getName() == 'test_update_schema_command'))) {
            $conn = $this->entityManager->getConnection();
            $conn->executeUpdate('ALTER TABLE dtb_customer ADD test_update_schema_command text');
        }
    }
}
