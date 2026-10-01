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
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * 営業時間（構造化データ schema.org OpeningHoursSpecification）用の dtb_opening_hours を作成する.
 */
final class Version20260722010000 extends AbstractMigration
{
    public const NAME = 'dtb_opening_hours';

    public function up(Schema $schema): void
    {
        if ($schema->hasTable(self::NAME)) {
            return;
        }

        $table = $schema->createTable(self::NAME);
        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true, 'unsigned' => true]);
        $table->addColumn('base_info_id', Types::INTEGER, ['unsigned' => true, 'notnull' => false]);
        $table->addColumn('day_of_week', Types::SIMPLE_ARRAY, ['notnull' => false]);
        $table->addColumn('opens', Types::TIME_MUTABLE, ['notnull' => false]);
        $table->addColumn('closes', Types::TIME_MUTABLE, ['notnull' => false]);
        $table->addColumn('sort_no', Types::INTEGER, ['default' => 0]);
        $table->addColumn('discriminator_type', Types::STRING, ['length' => 255]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['base_info_id'], 'dtb_opening_hours_base_info_id_idx');
        $table->addForeignKeyConstraint('dtb_base_info', ['base_info_id'], ['id'], [], 'fk_opening_hours_base_info');
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $schema->dropTable(self::NAME);
    }
}
