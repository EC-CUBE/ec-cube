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

use Eccube\Command\CacheBuildCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\RebootableInterface;

/**
 * 書き込み権限が不足しているときの振る舞いを検証する.
 *
 * 実際のビルドはコンテナの再構築を伴うためここでは扱わない.
 */
final class CacheBuildCommandTest extends TestCase
{
    private string $workDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workDir = sys_get_temp_dir().'/eccube-cache-build-'.bin2hex(random_bytes(6));
        mkdir($this->workDir, 0755, true);
    }

    protected function tearDown(): void
    {
        // 読み取り専用にしたディレクトリを消せるよう権限を戻す
        foreach (['build', 'cache'] as $name) {
            if (is_dir($this->workDir.'/'.$name)) {
                chmod($this->workDir.'/'.$name, 0755);
            }
        }
        (new Filesystem())->remove($this->workDir);
        parent::tearDown();
    }

    public function testReturnsManualActionRequiredWhenBuildDirIsNotWritable(): void
    {
        $this->skipWhenRunningAsRoot();

        $buildDir = $this->workDir.'/build';
        $cacheDir = $this->workDir.'/cache';
        mkdir($buildDir, 0555);
        mkdir($cacheDir, 0755);

        $tester = $this->tester($buildDir, $cacheDir);

        $this->assertSame(CacheBuildCommand::EXIT_MANUAL_ACTION_REQUIRED, $tester->execute([]));
        $this->assertStringContainsString($buildDir, $tester->getDisplay());
        $this->assertStringContainsString('eccube:doctor:permissions', $tester->getDisplay());
    }

    /**
     * cache ディレクトリの書き込み権限も必要になる (Kernel::buildContainer() の検査).
     */
    public function testReturnsManualActionRequiredWhenCacheDirIsNotWritable(): void
    {
        $this->skipWhenRunningAsRoot();

        $buildDir = $this->workDir.'/build';
        $cacheDir = $this->workDir.'/cache';
        mkdir($buildDir, 0755);
        mkdir($cacheDir, 0555);

        $tester = $this->tester($buildDir, $cacheDir);

        $this->assertSame(CacheBuildCommand::EXIT_MANUAL_ACTION_REQUIRED, $tester->execute([]));
        $this->assertStringContainsString($cacheDir, $tester->getDisplay());
    }

    private function skipWhenRunningAsRoot(): void
    {
        if (getmyuid() === 0) {
            $this->markTestSkipped('root は書き込み権限の検査を通過するため検証できません.');
        }
    }

    private function tester(string $buildDir, string $cacheDir): CommandTester
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('getParameter')->willReturnCallback(static fn (string $key): mixed => match ($key) {
            'kernel.build_dir' => $buildDir,
            'kernel.cache_dir' => $cacheDir,
            'eccube_runtime_dir' => $cacheDir.'/../runtime',
            default => null,
        });

        $kernel = $this->createMockForIntersectionOfInterfaces([KernelInterface::class, RebootableInterface::class]);
        $kernel->method('getContainer')->willReturn($container);
        $kernel->method('getEnvironment')->willReturn('prod');
        $kernel->method('isDebug')->willReturn(false);
        // 検査で弾かれるため再構築は行われない
        $kernel->expects($this->never())->method('reboot');

        $command = new CacheBuildCommand();
        $command->setApplication(new Application($kernel));

        return new CommandTester($command);
    }
}
