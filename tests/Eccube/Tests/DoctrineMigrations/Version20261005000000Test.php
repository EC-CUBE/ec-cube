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

namespace Eccube\Tests\DoctrineMigrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use DoctrineMigrations\Version20261005000000;
use Eccube\Tests\EccubeTestCase;
use Psr\Log\NullLogger;

// DoctrineMigrations 名前空間は composer の autoload に含まれないため直接読み込む
require_once __DIR__.'/../../../../app/DoctrineMigrations/Version20261005000000.php';

final class Version20261005000000Test extends EccubeTestCase
{
    private const TABLES = ['dtb_mail_template', 'dtb_csv'];

    private ?Connection $conn = null;

    /**
     * テーブルごとの、テスト開始時の MAX(id)+1。tearDown でシーケンスをこれより前に戻さないために記録する
     *
     * @var array<string, int>|null
     */
    private ?array $nextIds = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->conn = $this->entityManager->getConnection();
        foreach (self::TABLES as $table) {
            $this->nextIds[$table] = (int) $this->conn->fetchOne(sprintf('SELECT COALESCE(MAX(id), 0) + 1 FROM %s', $table));
        }
    }

    protected function tearDown(): void
    {
        // setval はトランザクションのロールバック対象外のため、後続テストの採番を壊さないよう戻す。
        // テスト中の変更はこの後のロールバックで取り消されるので、テスト開始時の MAX(id)+1 を下回らないようにする
        if ($this->conn !== null && $this->isPostgreSQL()) {
            foreach (self::TABLES as $table) {
                $this->conn->executeQuery(
                    sprintf("SELECT setval(pg_get_serial_sequence('%1\$s', 'id'), GREATEST(:next, (SELECT COALESCE(MAX(id), 0) + 1 FROM %1\$s)), false)", $table),
                    ['next' => $this->nextIds[$table] ?? 1]
                );
            }
        }
        parent::tearDown();
    }

    public function testUpInsertsMailTemplateAndFixesSequences(): void
    {
        // 修正前の Version20260615000000 を適用した環境を再現する。
        // id=10 が別のテンプレートで使用済みのため返品申請テンプレートが入らず、PostgreSQL ではシーケンスが MAX(id) より遅れている
        $this->conn->executeStatement("UPDATE dtb_mail_template SET file_name = 'Mail/shop_custom.twig' WHERE file_name = 'Mail/refund_request_notify.twig'");
        if ($this->isPostgreSQL()) {
            foreach (self::TABLES as $table) {
                $this->conn->executeQuery(sprintf("SELECT setval(pg_get_serial_sequence('%s', 'id'), 1, false)", $table));
            }
        }

        $this->runUp();

        $count = $this->conn->fetchOne("SELECT COUNT(*) FROM dtb_mail_template WHERE file_name = 'Mail/refund_request_notify.twig'");
        $this->assertSame(1, (int) $count);
        $count = $this->conn->fetchOne("SELECT COUNT(*) FROM dtb_mail_template WHERE file_name = 'Mail/shop_custom.twig'");
        $this->assertSame(1, (int) $count, '使用済みの ID のテンプレートを上書きしない');

        if ($this->isPostgreSQL()) {
            foreach (self::TABLES as $table) {
                $max = (int) $this->conn->fetchOne(sprintf('SELECT MAX(id) FROM %s', $table));
                $this->assertSame($max + 1, $this->nextSequenceValue($table), $table.' の次の採番が MAX(id)+1 になっていない');
            }
        }
    }

    public function testUpDoesNotInsertMailTemplateWhenItExists(): void
    {
        $before = (int) $this->conn->fetchOne("SELECT COUNT(*) FROM dtb_mail_template WHERE file_name = 'Mail/refund_request_notify.twig'");
        $this->assertSame(1, $before, '前提: fixture で返品申請テンプレートが投入されている');

        $this->runUp();

        $count = $this->conn->fetchOne("SELECT COUNT(*) FROM dtb_mail_template WHERE file_name = 'Mail/refund_request_notify.twig'");
        $this->assertSame(1, (int) $count);
    }

    private function runUp(): void
    {
        $migration = new Version20261005000000($this->conn, new NullLogger());
        // up() は hasTable() でテーブルの有無を見るため、実際のスキーマを渡す
        $migration->up($this->conn->createSchemaManager()->introspectSchema());
        // 失敗してもトランザクションを使える状態に戻し、tearDown の setval を実行できるようにする（savepoint まで戻す）
        $this->conn->beginTransaction();
        try {
            foreach ($migration->getSql() as $query) {
                $this->conn->executeStatement($query->getStatement(), $query->getParameters(), $query->getTypes());
            }
            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * シーケンスを進めずに、次に採番される値を返す.
     */
    private function nextSequenceValue(string $table): int
    {
        $sequence = $this->conn->fetchOne(sprintf("SELECT pg_get_serial_sequence('%s', 'id')", $table));
        $row = $this->conn->fetchAssociative(sprintf('SELECT last_value, is_called FROM %s', $sequence));

        return (int) $row['last_value'] + ($row['is_called'] ? 1 : 0);
    }

    private function isPostgreSQL(): bool
    {
        return $this->conn->getDatabasePlatform() instanceof PostgreSQLPlatform;
    }
}
