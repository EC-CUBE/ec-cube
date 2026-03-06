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
final class Version20230116061053 extends AbstractMigration
{
    public const NAME = 'dtb_csv';

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
