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

final class Version20260602120000 extends AbstractMigration
{
    public const NAME = 'dtb_base_info';

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $table = $schema->getTable(self::NAME);

        if (!$table->hasColumn('acp_enabled')) {
            $this->addSql('ALTER TABLE dtb_base_info ADD acp_enabled BOOLEAN NOT NULL DEFAULT false');
        }
        if (!$table->hasColumn('ucp_enabled')) {
            $this->addSql('ALTER TABLE dtb_base_info ADD ucp_enabled BOOLEAN NOT NULL DEFAULT false');
        }
        if (!$table->hasColumn('acp_feed_enabled')) {
            $this->addSql('ALTER TABLE dtb_base_info ADD acp_feed_enabled BOOLEAN NOT NULL DEFAULT false');
        }
        if (!$table->hasColumn('ucp_catalog_api_enabled')) {
            $this->addSql('ALTER TABLE dtb_base_info ADD ucp_catalog_api_enabled BOOLEAN NOT NULL DEFAULT false');
        }
        if (!$table->hasColumn('ucp_catalog_requires_auth')) {
            $this->addSql('ALTER TABLE dtb_base_info ADD ucp_catalog_requires_auth BOOLEAN NOT NULL DEFAULT false');
        }
        if (!$table->hasColumn('google_pay_merchant_id')) {
            $this->addSql('ALTER TABLE dtb_base_info ADD google_pay_merchant_id VARCHAR(255) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $table = $schema->getTable(self::NAME);

        if ($table->hasColumn('acp_enabled')) {
            $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN acp_enabled');
        }
        if ($table->hasColumn('ucp_enabled')) {
            $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN ucp_enabled');
        }
        if ($table->hasColumn('acp_feed_enabled')) {
            $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN acp_feed_enabled');
        }
        if ($table->hasColumn('ucp_catalog_api_enabled')) {
            $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN ucp_catalog_api_enabled');
        }
        if ($table->hasColumn('ucp_catalog_requires_auth')) {
            $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN ucp_catalog_requires_auth');
        }
        if ($table->hasColumn('google_pay_merchant_id')) {
            $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN google_pay_merchant_id');
        }
    }
}
