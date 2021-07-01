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
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201218044542 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $pointExists = $this->connection->fetchColumn("SELECT COUNT(*) FROM dtb_csv WHERE csv_type_id = 2 AND field_name = 'point'");

        if ($pointExists == 0) {
            $sortNo = $this->connection->fetchColumn('SELECT MAX(sort_no) + 1 FROM dtb_csv WHERE csv_type_id = 2');
            $this->addSql("INSERT INTO dtb_csv (
                csv_type_id, creator_id, entity_name, field_name, disp_name, sort_no, enabled, create_date, update_date, discriminator_type
            ) VALUES (
                2, null, ?, 'point', 'ポイント', $sortNo, false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'csv'
            )",
            ['Eccube\\\\Entity\\\\Customer']);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
