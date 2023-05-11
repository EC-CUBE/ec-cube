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
final class Version20190821081036 extends AbstractMigration
{
    const NAME = 'dtb_csv';

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $taxRateExists = $this->connection->fetchOne("SELECT COUNT(*) FROM dtb_csv WHERE csv_type_id = 1 AND entity_name = ? AND field_name = 'TaxRule' AND reference_field_name = 'tax_rate'", ['Eccube\\\\Entity\\\\ProductClass']);
        if ($taxRateExists == 0) {
            $this->addSql("INSERT INTO dtb_csv (csv_type_id, creator_id, entity_name, field_name, reference_field_name, disp_name, sort_no, enabled, create_date, update_date, discriminator_type) VALUES (1, null , ?, 'TaxRule', 'tax_rate', '税率', 31, false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,'csv')", ['Eccube\\\\Entity\\\\ProductClass']);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
