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

namespace Eccube\Tests\Util;

use Eccube\Common\EccubeConfig;
use Eccube\Util\CacheUtil;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheClearer\Psr6CacheClearer;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * ビルドディレクトリへ書き込めるかどうかによる分岐を検証する.
 */
final class CacheUtilTest extends TestCase
{
    private string $workDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workDir = sys_get_temp_dir().'/eccube-cache-util-'.bin2hex(random_bytes(6));
        foreach (['build', 'cache', 'runtime'] as $name) {
            mkdir($this->workDir.'/'.$name, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        foreach (['build', 'cache', 'runtime'] as $name) {
            if (is_dir($this->workDir.'/'.$name)) {
                chmod($this->workDir.'/'.$name, 0755);
            }
        }
        (new Filesystem())->remove($this->workDir);
        parent::tearDown();
    }

    public function testCanClearBuildCacheWhenBothDirectoriesAreWritable(): void
    {
        $this->assertTrue($this->cacheUtil()->canClearBuildCache());
    }

    public function testCannotClearBuildCacheWhenBuildDirIsReadOnly(): void
    {
        $this->skipWhenRunningAsRoot();
        chmod($this->workDir.'/build', 0555);

        $this->assertFalse($this->cacheUtil()->canClearBuildCache());
    }

    /**
     * cache:clear は build と cache の双方へ書き込むため, どちらか一方でも書けなければ実行できない.
     */
    public function testCannotClearBuildCacheWhenCacheDirIsReadOnly(): void
    {
        $this->skipWhenRunningAsRoot();
        chmod($this->workDir.'/cache', 0555);

        $this->assertFalse($this->cacheUtil()->canClearBuildCache());
    }

    /**
     * 実行時キャッシュの削除はランタイムディレクトリだけを対象とし, ビルド生成物は残す.
     */
    public function testClearRuntimeCacheRemovesOnlyRuntimeArtifacts(): void
    {
        $runtimeDir = $this->workDir.'/runtime';
        foreach (['twig', 'translations', 'htmlpurifier', 'mcp-sessions'] as $name) {
            mkdir($runtimeDir.'/'.$name, 0755, true);
        }
        mkdir($this->workDir.'/build/twig', 0755, true);

        $message = $this->cacheUtil()->clearRuntimeCache();

        $this->assertDirectoryDoesNotExist($runtimeDir.'/twig');
        $this->assertDirectoryDoesNotExist($runtimeDir.'/translations');
        $this->assertDirectoryDoesNotExist($runtimeDir.'/htmlpurifier');
        // キャッシュではないものは削除しない
        $this->assertDirectoryExists($runtimeDir.'/mcp-sessions');
        // ビルド生成物には触れない
        $this->assertDirectoryExists($this->workDir.'/build/twig');
        $this->assertStringContainsString('eccube:cache:build', $message);
    }

    /**
     * prod では build 側の読み取り専用キャッシュが優先されるため, 双方を削除しないと
     * 管理画面で更新したテンプレートが反映されない.
     */
    public function testClearTwigCacheRemovesBothRuntimeAndBuildCaches(): void
    {
        mkdir($this->workDir.'/runtime/twig', 0755, true);
        mkdir($this->workDir.'/build/twig', 0755, true);

        $this->cacheUtil()->clearTwigCache();

        $this->assertDirectoryDoesNotExist($this->workDir.'/runtime/twig');
        $this->assertDirectoryDoesNotExist($this->workDir.'/build/twig');
    }

    /**
     * ビルドディレクトリへ書き込めない構成では build 側は残る (eccube:cache:build に委ねる).
     */
    public function testClearTwigCacheKeepsBuildCacheWhenBuildDirIsReadOnly(): void
    {
        $this->skipWhenRunningAsRoot();
        mkdir($this->workDir.'/runtime/twig', 0755, true);
        mkdir($this->workDir.'/build/twig', 0755, true);
        chmod($this->workDir.'/build', 0555);

        $this->cacheUtil()->clearTwigCache();

        $this->assertDirectoryDoesNotExist($this->workDir.'/runtime/twig');
        $this->assertDirectoryExists($this->workDir.'/build/twig');
    }

    private function skipWhenRunningAsRoot(): void
    {
        if (getmyuid() === 0) {
            $this->markTestSkipped('root は書き込み権限の検査を通過するため検証できません.');
        }
    }

    private function cacheUtil(): CacheUtil
    {
        $eccubeConfig = $this->createMock(EccubeConfig::class);
        $eccubeConfig->method('get')->willReturnCallback(fn (string $key): mixed => match ($key) {
            'kernel.build_dir' => $this->workDir.'/build',
            'kernel.cache_dir' => $this->workDir.'/cache',
            'eccube_runtime_dir' => $this->workDir.'/runtime',
            default => null,
        });

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with('cache.global_clearer')->willReturn(new Psr6CacheClearer());

        return new CacheUtil(
            $this->createStub(KernelInterface::class),
            $container,
            $eccubeConfig,
            $this->createStub(LoggerInterface::class),
        );
    }
}
