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

namespace Eccube\Tests\Command\Env;

use Eccube\Command\Env\EnvSetCommand;
use Eccube\Service\EnvFileService;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * ECCUBE_ADMIN_ROUTE の検証. 管理画面と同じ制約を使うため, コンテナの validator で組み立てる.
 *
 * 一時ディレクトリを projectDir にするため, 実際の .env には触れない.
 */
final class EnvSetCommandAdminRouteTest extends EccubeTestCase
{
    private const ENV = "APP_ENV=prod\nECCUBE_ADMIN_ROUTE=admin\n";

    private ?string $projectDir = null;

    private ?Filesystem $fs = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();
        $this->projectDir = sys_get_temp_dir().'/eccube-env-admin-route-'.bin2hex(random_bytes(6));
        $this->fs->dumpFile($this->projectDir.'/.env', self::ENV);
    }

    protected function tearDown(): void
    {
        if (null !== $this->fs && null !== $this->projectDir) {
            $this->fs->remove($this->projectDir);
        }

        parent::tearDown();
    }

    #[DataProvider(methodName: 'invalidParams')]
    public function testRejectsInvalidAdminRoute(string $value): void
    {
        $tester = $this->set(['assignments' => ['ECCUBE_ADMIN_ROUTE='.$value]]);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
        $this->assertStringContainsString('ECCUBE_ADMIN_ROUTE', $tester->getDisplay());
        $this->assertSame(self::ENV, file_get_contents($this->projectDir.'/.env'), '.env を書き換えないこと');
    }

    public static function invalidParams(): \Iterator
    {
        yield ['_profiler'];
        yield ['_wdt'];
        yield ['css'];
        yield ['images'];
        yield ['js'];
        yield ['cart'];
        yield ['admin/route'];
        yield [''];
        yield [str_repeat('a', 256)];
    }

    public function testRejectsWholeAssignmentsWhenAdminRouteIsInvalid(): void
    {
        $tester = $this->set(['assignments' => ['ECCUBE_TEMPLATE_CODE=default', 'ECCUBE_ADMIN_ROUTE=images']]);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
        $this->assertSame(self::ENV, file_get_contents($this->projectDir.'/.env'), '他のキーも書き込まないこと');
    }

    public function testAcceptsValidAdminRoute(): void
    {
        $tester = $this->set(['assignments' => ['ECCUBE_ADMIN_ROUTE=management'], '--no-cache-clear' => true]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode(), $tester->getDisplay());
        $this->assertStringContainsString("ECCUBE_ADMIN_ROUTE=management\n", (string) file_get_contents($this->projectDir.'/.env'));
    }

    /**
     * @param array<string, mixed> $input
     */
    private function set(array $input): CommandTester
    {
        $command = new EnvSetCommand(new EnvFileService($this->projectDir), $this->projectDir, static::getContainer()->get('validator'), $this->eccubeConfig);
        $tester = new CommandTester($command);
        $tester->execute($input);

        return $tester;
    }
}
