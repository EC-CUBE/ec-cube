<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230116061053 extends AbstractMigration
{
    const NAME = 'dtb_csv';

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable(self::NAME)) {
            return;
        }
        $exists = $this->connection->fetchOne("SELECT count(*) FROM dtb_csv WHERE csv_type_id = 1 AND entity_name = ? AND field_name = 'visible'", ['Eccube\\\\Entity\\\\ProductClass']);
        if ($exists == 0) {
            $this->addSql("INSERT INTO dtb_csv (csv_type_id, creator_id, entity_name, field_name, disp_name, sort_no, enabled, create_date, update_date, discriminator_type) VALUES (1, null, 'Eccube\\\\Entity\\\\ProductClass', 'visible', '商品規格表示フラグ', 32, false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'csv')");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
