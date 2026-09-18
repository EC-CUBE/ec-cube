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

final class Version20260316234241 extends AbstractMigration
{
    public const NAME = 'dtb_base_info';

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $table = $schema->getTable(self::NAME);
        if ($table->hasColumn('option_guest_purchase')) {
            return;
        }

        $this->addSql('ALTER TABLE dtb_base_info ADD option_guest_purchase BOOLEAN NOT NULL DEFAULT true');
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $table = $schema->getTable(self::NAME);
        if (!$table->hasColumn('option_guest_purchase')) {
            return;
        }

        $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN option_guest_purchase');
    }
}
