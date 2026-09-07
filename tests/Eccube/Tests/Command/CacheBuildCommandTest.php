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
use Eccube\Tests\EffectiveUserTrait;
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
    use EffectiveUserTrait;

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
        $entries = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->workDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($entries as $entry) {
            if ($entry instanceof \SplFileInfo && $entry->isDir()) {
                chmod($entry->getPathname(), 0755);
            }
        }
        (new Filesystem())->remove($this->workDir);
        parent::tearDown();
    }

    public function testReturnsManualActionRequiredWhenBuildDirIsNotWritable(): void
    {
        $this->skipIfRoot();

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
        $this->skipIfRoot();

        $buildDir = $this->workDir.'/build';
        $cacheDir = $this->workDir.'/cache';
        mkdir($buildDir, 0755);
        mkdir($cacheDir, 0555);

        $tester = $this->tester($buildDir, $cacheDir);

        $this->assertSame(CacheBuildCommand::EXIT_MANUAL_ACTION_REQUIRED, $tester->execute([]));
        $this->assertStringContainsString($cacheDir, $tester->getDisplay());
    }

    /**
     * ビルドディレクトリの親にも書き込み権限が必要になる.
     *
     * warmup 先を同じ親に別名で作り, 最後に rename で差し替えるため, ビルドディレクトリ自体へ
     * 書き込めても親へ書き込めなければ差し替えられない. 事前に検査しないと mkdir が例外になり,
     * 権限の案内を出せないまま終わる.
     */
    public function testReturnsManualActionRequiredWhenBuildDirParentIsNotWritable(): void
    {
        $this->skipIfRoot();

        $parent = $this->workDir.'/lane-s';
        $buildDir = $parent.'/build';
        $cacheDir = $this->workDir.'/cache';
        mkdir($buildDir, 0755, true);
        mkdir($cacheDir, 0755);
        chmod($parent, 0555);

        $tester = $this->tester($buildDir, $cacheDir);

        $this->assertSame(CacheBuildCommand::EXIT_MANUAL_ACTION_REQUIRED, $tester->execute([]));
        $this->assertStringContainsString($parent, $tester->getDisplay());
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
