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

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * 店舗情報の構造化データ（JSON-LD / schema.org）拡張用カラムを dtb_base_info に追加する.
 *
 * - same_as            : SNS 等の公式 URL（改行区切り, Organization.sameAs）
 * - founding_date      : 稼働開始日（Organization.foundingDate）
 * - number_of_employees: 従業員数（Organization.numberOfEmployees）
 * - copyright_year     : 著作権表示の開始年（WebSite.copyrightYear）
 * - site_image         : サイト代表画像 URL（Organization.image）
 */
final class Version20260722000000 extends AbstractMigration
{
    public const NAME = 'dtb_base_info';

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $table = $schema->getTable(self::NAME);
        if ($table->hasColumn('same_as')) {
            return;
        }

        // TEXT 型は MySQL では LONGTEXT にマップされるため、schema:update との差分を避けて分岐する
        $textType = $this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform ? 'LONGTEXT' : 'TEXT';

        $this->addSql('ALTER TABLE dtb_base_info ADD same_as '.$textType.' DEFAULT NULL');
        $this->addSql('ALTER TABLE dtb_base_info ADD founding_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE dtb_base_info ADD number_of_employees INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dtb_base_info ADD copyright_year INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dtb_base_info ADD site_image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $table = $schema->getTable(self::NAME);
        if (!$table->hasColumn('same_as')) {
            return;
        }

        $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN same_as');
        $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN founding_date');
        $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN number_of_employees');
        $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN copyright_year');
        $this->addSql('ALTER TABLE dtb_base_info DROP COLUMN site_image');
    }
}
