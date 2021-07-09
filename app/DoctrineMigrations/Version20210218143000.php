<?php declare(strict_types=1);

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
final class Version20210218143000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $blockId = $this->connection->fetchColumn("SELECT id FROM dtb_block WHERE file_name = 'browsing_history'");

        if (!$blockId) {
            $blockId = $this->connection->fetchColumn('SELECT MAX(id) + 1 FROM dtb_block');

            $this->addSql("INSERT INTO dtb_block (
                   id, device_type_id, block_name, file_name, create_date, update_date, use_controller, deletable, discriminator_type
                ) VALUES(
                    ?, 10, '最近チェックした商品', 'browsing_history', '2021-02-18 14:30:00', '2021-02-18 14:30:00', false, false, 'block'
                )", [$blockId]);
        }
    }

    public function down(Schema $schema): void
    {
        $blockId = $this->connection->fetchColumn("SELECT id FROM dtb_block WHERE file_name = 'browsing_history'");
        if ($blockId > 0) {
            $this->addSql("DELETE FROM dtb_block_position WHERE block_id = ?", [$blockId]);
            $this->addSql("DELETE FROM dtb_block WHERE file_name = 'browsing_history'");
        }
    }
}
