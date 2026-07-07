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

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * mtb_checkout_session_status へ正規化ステータス requires_action (6) / in_progress (7) を追加する.
 *
 * complete を「中断→再開」状態機械として再設計したことに伴い、追加認証 (3DS) / escalation 待ちの
 * `requires_action` と非同期決済確定待ちの `in_progress` を中間状態として導入する (#6777)。
 *
 * 新規インストールは import_csv (definition.yml) で 7 行とも投入されるため、本マイグレーションは
 * 既存インストール (1〜5 のみ投入済) のアップグレード時 backfill を担う。行単位で存在を確認し
 * 二重投入しない冪等実装とする。
 */
final class Version20260617120000 extends AbstractMigration
{
    /**
     * @var array<int, array{int, string}>
     */
    private const ROWS = [
        [6, 'requires_action'],
        [7, 'in_progress'],
    ];

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('mtb_checkout_session_status')) {
            return;
        }

        foreach (self::ROWS as [$id, $name]) {
            $exists = $this->connection->fetchOne(
                'SELECT COUNT(*) FROM mtb_checkout_session_status WHERE id = :id',
                ['id' => $id]
            );
            if ($exists > 0) {
                continue;
            }

            $this->addSql(
                'INSERT INTO mtb_checkout_session_status (id, name, sort_no, discriminator_type) VALUES (:id, :name, :sort_no, :discriminator_type)',
                ['id' => $id, 'name' => $name, 'sort_no' => $id, 'discriminator_type' => 'checkoutsessionstatus']
            );
        }
    }

    public function down(Schema $schema): void
    {
        // マスタ初期データは不可逆として扱い、ロールバックでは何もしない
        // (一律 DELETE すると import_csv 由来の初期データまで巻き添えになるため)。
        $this->throwIrreversibleMigrationException('エージェントコマースの区分値マスタはマスタ初期データのため down は非対応です.');
    }
}
