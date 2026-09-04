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

use Eccube\Util\RuntimeCachePoolClearer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * cache:clear でランタイムディレクトリの cache pool が削除されることを検証する.
 */
final class RuntimeCachePoolClearerTest extends TestCase
{
    private string $runtimeDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runtimeDir = sys_get_temp_dir().'/eccube-runtime-pool-'.bin2hex(random_bytes(6));
        mkdir($this->runtimeDir, 0755, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->runtimeDir)) {
            chmod($this->runtimeDir, 0755);
        }
        (new Filesystem())->remove($this->runtimeDir);
        parent::tearDown();
    }

    public function testPoolsAreRemoved(): void
    {
        mkdir($this->runtimeDir.'/pools/system', 0755, true);
        file_put_contents($this->runtimeDir.'/pools/system/entry.php', '<?php return [];');

        (new RuntimeCachePoolClearer($this->runtimeDir))->clear($this->runtimeDir);

        $this->assertDirectoryDoesNotExist($this->runtimeDir.'/pools');
    }

    /**
     * pool 以外のランタイムキャッシュ (セッションや一時領域) は削除しない.
     */
    public function testOtherRuntimeDirectoriesAreKept(): void
    {
        mkdir($this->runtimeDir.'/pools/app', 0755, true);
        mkdir($this->runtimeDir.'/mcp-sessions', 0755, true);
        mkdir($this->runtimeDir.'/twig', 0755, true);

        (new RuntimeCachePoolClearer($this->runtimeDir))->clear($this->runtimeDir);

        $this->assertDirectoryDoesNotExist($this->runtimeDir.'/pools');
        $this->assertDirectoryExists($this->runtimeDir.'/mcp-sessions');
        $this->assertDirectoryExists($this->runtimeDir.'/twig');
    }

    public function testMissingPoolsDirectoryIsIgnored(): void
    {
        (new RuntimeCachePoolClearer($this->runtimeDir))->clear($this->runtimeDir);

        $this->assertDirectoryExists($this->runtimeDir);
    }

    /**
     * 権限を分離した構成ではランタイムディレクトリは Web サーバー所有のため削除できない.
     * cache:clear 全体を失敗させず, 実行時キャッシュの削除は cache:pool:clear に委ねる.
     */
    public function testUnwritableDirectoryDoesNotThrow(): void
    {
        if (getmyuid() === 0) {
            $this->markTestSkipped('root は書き込み権限の検査を通過するため検証できません.');
        }

        mkdir($this->runtimeDir.'/pools/system', 0755, true);
        chmod($this->runtimeDir, 0555);

        (new RuntimeCachePoolClearer($this->runtimeDir))->clear($this->runtimeDir);

        $this->assertDirectoryExists($this->runtimeDir.'/pools');
    }
}
