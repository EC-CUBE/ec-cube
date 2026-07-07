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
 * エージェントコマースの区分値マスタ (mtb_checkout_session_status / mtb_agent_protocol) の初期データを投入する.
 *
 * テーブル自体は doctrine:schema:update でエンティティから生成される。
 * 新規インストールは import_csv (definition.yml) で投入されるため、
 * 本マイグレーションは既存インストールのアップグレード時の backfill を担う
 * (空テーブルのときのみ INSERT する冪等実装)。
 */
final class Version20260611120000 extends AbstractMigration
{
    /**
     * @var array<string, string>
     */
    private const INSERTS = [
        'mtb_checkout_session_status' => "INSERT INTO mtb_checkout_session_status (id, name, sort_no, discriminator_type) VALUES (1, 'incomplete', 1, 'checkoutsessionstatus'), (2, 'ready', 2, 'checkoutsessionstatus'), (3, 'completed', 3, 'checkoutsessionstatus'), (4, 'canceled', 4, 'checkoutsessionstatus'), (5, 'expired', 5, 'checkoutsessionstatus')",
        'mtb_agent_protocol' => "INSERT INTO mtb_agent_protocol (id, name, sort_no, discriminator_type) VALUES (1, 'acp', 1, 'agentprotocol'), (2, 'ucp', 2, 'agentprotocol')",
    ];

    public function up(Schema $schema): void
    {
        foreach (self::INSERTS as $table => $sql) {
            if (!$schema->hasTable($table)) {
                continue;
            }

            // 既にデータがある場合 (新規インストールの fixtures 投入済み等) は二重投入しない.
            $count = $this->connection->fetchOne('SELECT COUNT(*) FROM '.$table);
            if ($count > 0) {
                continue;
            }

            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        // 新規インストールでは up() が no-op (import_csv 投入済み) のため、
        // 一律 DELETE すると import_csv 由来の初期データまで巻き添えで消える。
        // マスタ初期データは不可逆として扱い、ロールバックでは何もしない。
        $this->throwIrreversibleMigrationException('エージェントコマースの区分値マスタはマスタ初期データのため down は非対応です.');
    }
}
