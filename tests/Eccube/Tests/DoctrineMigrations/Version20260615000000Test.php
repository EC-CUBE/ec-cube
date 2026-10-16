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
use Doctrine\DBAL\Schema\Schema;
use DoctrineMigrations\Version20260615000000;
use Eccube\Tests\EccubeTestCase;
use Psr\Log\NullLogger;

// DoctrineMigrations 名前空間は composer の autoload に含まれないため直接読み込む
require_once __DIR__.'/../../../../app/DoctrineMigrations/Version20260615000000.php';

final class Version20260615000000Test extends EccubeTestCase
{
    private ?Connection $conn = null;

    /** DELETE する前の MAX(id)+1。tearDown でシーケンスをこれより前に戻さないために記録する */
    private ?int $nextMailTemplateId = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->conn = $this->entityManager->getConnection();
        $this->nextMailTemplateId = (int) $this->conn->fetchOne('SELECT COALESCE(MAX(id), 0) + 1 FROM dtb_mail_template');
    }

    protected function tearDown(): void
    {
        // setval はトランザクションのロールバック対象外のため、後続テストの採番を壊さないよう戻す。
        // DELETE はこの後のロールバックで取り消され行が戻るので、DELETE する前の MAX(id)+1 を下回らないようにする
        if ($this->conn !== null && $this->conn->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $this->conn->executeQuery(
                "SELECT setval(pg_get_serial_sequence('dtb_mail_template', 'id'), GREATEST(:next, (SELECT COALESCE(MAX(id), 0) + 1 FROM dtb_mail_template)), false)",
                ['next' => $this->nextMailTemplateId ?? 1]
            );
        }
        parent::tearDown();
    }

    public function testInsertsMailTemplateWhenMissing(): void
    {
        // 返品申請テンプレートが未投入の環境を再現する。ID は指定せず採番に任せるため、全 DB で確かめる
        $this->conn->executeStatement("DELETE FROM dtb_mail_template WHERE file_name = 'Mail/refund_request_notify.twig'");
        if ($this->conn->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            // PostgreSQL では、ID 指定の INSERT 後に setval されずシーケンスが MAX(id) より遅れた状態にする。
            // MySQL / SQLite は ID 指定の INSERT でも採番が追随するため、この遅れは起きない
            $this->conn->executeQuery("SELECT setval(pg_get_serial_sequence('dtb_mail_template', 'id'), 1, false)");
        }

        $this->runUp();

        $count = $this->conn->fetchOne("SELECT COUNT(*) FROM dtb_mail_template WHERE file_name = 'Mail/refund_request_notify.twig'");
        $this->assertSame(1, (int) $count);
    }

    public function testInsertsMailTemplateWhenItsIdIsUsedByAnotherTemplate(): void
    {
        // 4.3 以前にメールテンプレートを追加し、返品申請テンプレートの ID（fixture では 10）が別のテンプレートで使用済みの環境を再現する
        $this->conn->executeStatement("UPDATE dtb_mail_template SET file_name = 'Mail/shop_custom.twig' WHERE file_name = 'Mail/refund_request_notify.twig'");

        $this->runUp();

        $count = $this->conn->fetchOne("SELECT COUNT(*) FROM dtb_mail_template WHERE file_name = 'Mail/refund_request_notify.twig'");
        $this->assertSame(1, (int) $count);
        $count = $this->conn->fetchOne("SELECT COUNT(*) FROM dtb_mail_template WHERE file_name = 'Mail/shop_custom.twig'");
        $this->assertSame(1, (int) $count, '使用済みの ID のテンプレートを上書きしない');
    }

    private function runUp(): void
    {
        $migration = new Version20260615000000($this->conn, new NullLogger());
        $migration->up(new Schema());
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
}
