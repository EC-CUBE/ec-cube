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

namespace Eccube\Tests\Cache;

use Eccube\Cache\WriteFailsafeFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class WriteFailsafeFilesystemAdapterTest extends TestCase
{
    private string $workDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workDir = sys_get_temp_dir().'/eccube-cache-failsafe-'.bin2hex(random_bytes(6));
        mkdir($this->workDir, 0755, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->workDir)) {
            chmod($this->workDir, 0755);
        }
        (new Filesystem())->remove($this->workDir);
        parent::tearDown();
    }

    public function testSavesWhenDirectoryIsWritable(): void
    {
        $adapter = new WriteFailsafeFilesystemAdapter('ns', 0, $this->workDir);

        $item = $adapter->getItem('key');
        $item->set('value');

        $this->assertTrue($adapter->save($item));
        $this->assertSame('value', $adapter->getItem('key')->get());
    }

    /**
     * 書き込めない場合でも例外にせず, 保存できたものとして扱う (警告も記録しない).
     */
    public function testGivesUpSavingWhenDirectoryIsNotWritable(): void
    {
        $this->skipWhenRunningAsRoot();

        // 先に書き込み可能な状態でキャッシュを作り, その後で書き込み不可にする
        $writable = new WriteFailsafeFilesystemAdapter('ns', 0, $this->workDir);
        $item = $writable->getItem('warm');
        $item->set('warmed');
        $writable->save($item);

        chmod($this->workDir, 0555);

        $adapter = new WriteFailsafeFilesystemAdapter('ns', 0, $this->workDir);
        $newItem = $adapter->getItem('cold');
        $newItem->set('value');

        $this->assertTrue($adapter->save($newItem), '保存できなくても失敗として扱わない');
        $this->assertFalse($adapter->getItem('cold')->isHit(), '実際には保存されない');
        // 読み取りは従来どおり行える
        $this->assertSame('warmed', $adapter->getItem('warm')->get());
    }

    /**
     * 未作成のディレクトリは, 存在する直近の親で判定する (初回起動時に書き込みを諦めない).
     */
    public function testTreatsMissingDirectoryAsWritableWhenParentIsWritable(): void
    {
        $adapter = new WriteFailsafeFilesystemAdapter('ns', 0, $this->workDir.'/not-yet/created');

        $item = $adapter->getItem('key');
        $item->set('value');

        $this->assertTrue($adapter->save($item));
        $this->assertSame('value', $adapter->getItem('key')->get());
    }

    private function skipWhenRunningAsRoot(): void
    {
        if (getmyuid() === 0) {
            $this->markTestSkipped('root は書き込み権限の検査を通過するため検証できません.');
        }
    }
}
