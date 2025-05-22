<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312170000 extends AbstractMigration
{
    const NAME = 'dtb_block';

    public function up(Schema $schema): void
    {
        // テーブルが存在しない場合終了
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $blockExists = $this->connection->fetchOne("SELECT COUNT(*) FROM dtb_block WHERE file_name = 'auto_new_item'");
        if ($blockExists == 0) {
            $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, use_controller, deletable, discriminator_type) VALUES (10, '新着商品（自動取得）', 'auto_new_item', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0', 'block')");
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM dtb_block WHERE file_name = 'auto_new_item'");
    }
}