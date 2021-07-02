<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210216115000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $blockId = $this->connection->fetchColumn("SELECT id FROM dtb_block WHERE file_name = 'social_buttons'");

        // dtb_block に ソーシャルブロックがなければ作成する
        if (!$blockId) {
            $blockId = $this->connection->fetchColumn('SELECT MAX(id) + 1 FROM dtb_block');

            $this->addSql("INSERT INTO dtb_block (
            id, device_type_id, block_name, file_name, create_date, update_date, use_controller, deletable, discriminator_type
        ) VALUES(
            ?, 10, 'ソーシャルボタン', 'social_buttons', '2021-02-12 14:00:00', '2021-02-12 14:00:00', false, false, 'block'
        )", [$blockId]);
        }
    }

    public function down(Schema $schema): void
    {
        // dtb_block に ソーシャルブロックがあれば削除
        $blockId = $this->connection->fetchColumn("SELECT id FROM dtb_block WHERE file_name = 'social_buttons'");
        if ($blockId > 0) {
            $this->addSql("DELETE FROM dtb_block_position WHERE block_id = ?", [$blockId]);
            $this->addSql("DELETE FROM dtb_block WHERE file_name = 'social_buttons'");
        }
    }
}
