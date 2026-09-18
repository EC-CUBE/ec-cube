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

namespace Eccube\Tests\EventListener;

use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Cache\Adapter\DoctrineDbalAdapter;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\CacheStorage;

/**
 * スロットリングのデータストレージ(DB)に関するテスト.
 *
 * services.yaml の eccube.rate_limiter.dbal_adapter と同じ設定で
 * DoctrineDbalAdapter を組み立て, dtb_rate_limiter テーブル
 * (Eccube\Entity\RateLimiter) に読み書きできることを確認する.
 */
#[Group('rate-limiter-storage')]
final class RateLimiterStorageTest extends EccubeTestCase
{
    private const TABLE = 'dtb_rate_limiter';

    private function createAdapter(): DoctrineDbalAdapter
    {
        return new DoctrineDbalAdapter(
            $this->entityManager->getConnection(),
            '',
            0,
            [
                'db_table' => self::TABLE,
                'db_id_col' => 'cache_key',
                'db_data_col' => 'cache_data',
                'db_lifetime_col' => 'cache_lifetime',
                'db_time_col' => 'cache_time',
            ]
        );
    }

    private function countRows(): int
    {
        return (int) $this->entityManager->getConnection()
            ->fetchOne('SELECT COUNT(*) FROM '.self::TABLE);
    }

    /**
     * dtb_rate_limiter テーブルが Entity のメタデータから生成され存在すること.
     */
    public function testTableExists(): void
    {
        $schemaManager = $this->entityManager->getConnection()->createSchemaManager();

        $this->assertTrue($schemaManager->tablesExist([self::TABLE]), self::TABLE.' テーブルが存在しません');
    }

    /**
     * Entity の列名が adapter の options と一致し, カラムが揃っていること.
     */
    public function testColumnsMatchAdapterOptions(): void
    {
        $schemaManager = $this->entityManager->getConnection()->createSchemaManager();
        $columns = array_map(
            fn ($column) => $column->getName(),
            $schemaManager->listTableColumns(self::TABLE)
        );

        foreach (['cache_key', 'cache_data', 'cache_lifetime', 'cache_time'] as $expected) {
            $this->assertContains($expected, $columns, "列 {$expected} がありません");
        }
    }

    /**
     * DoctrineDbalAdapter 経由で DB に読み書きできること.
     */
    public function testReadWriteThroughAdapter(): void
    {
        $adapter = $this->createAdapter();
        $adapter->deleteItem('throttle_test');

        $item = $adapter->getItem('throttle_test');
        $this->assertFalse($item->isHit());

        $item->set('stored-value');
        $this->assertTrue($adapter->save($item));

        // DB にレコードが書き込まれていること
        $this->assertGreaterThan(0, $this->countRows());

        // 別インスタンスからでも取り出せる(＝ファイルではなく DB に永続化されている)こと
        $fetched = $this->createAdapter()->getItem('throttle_test');
        $this->assertTrue($fetched->isHit());
        $this->assertSame('stored-value', $fetched->get());

        $adapter->deleteItem('throttle_test');
    }

    /**
     * RateLimiter コンポーネントが DB ストレージ上でスロットリングを行えること.
     */
    public function testRateLimiterConsumesOnDbStorage(): void
    {
        $storage = new CacheStorage($this->createAdapter());
        $factory = new RateLimiterFactory(
            [
                'id' => 'test_throttle',
                'policy' => 'fixed_window',
                'limit' => 2,
                'interval' => '30 minutes',
            ],
            $storage
        );

        $limiter = $factory->create('192.0.2.1');

        $this->assertTrue($limiter->consume()->isAccepted());
        $this->assertTrue($limiter->consume()->isAccepted());
        // limit を超えたら拒否される
        $this->assertFalse($limiter->consume()->isAccepted());

        // 状態が DB に保存されていること
        $this->assertGreaterThan(0, $this->countRows());

        $limiter->reset();
    }
}
