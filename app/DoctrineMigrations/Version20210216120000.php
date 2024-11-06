<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210216120000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // データ存在チェック
        $count = $this->connection->fetchOne("SELECT COUNT(*) FROM dtb_block WHERE block_name = 'Googleアナリティクス'");
        if ($count > 0) {
            return;
        }

        // idを取得する
        $id = $this->connection->fetchOne('SELECT MAX(id) FROM dtb_block');
        $id++;

        $this->addSql("INSERT INTO dtb_block (id, block_name, file_name, use_controller, deletable, create_date, update_date, device_type_id, discriminator_type) VALUES ($id, 'Googleアナリティクス', 'google_analytics', false, false, '2021-02-16 12:00:00', '2021-02-16 12:00:00', 10, 'block')");
        $this->addSql("INSERT INTO dtb_block_position (section, block_id, layout_id, block_row, discriminator_type) VALUES (1, $id, 1, 0, 'blockposition')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
