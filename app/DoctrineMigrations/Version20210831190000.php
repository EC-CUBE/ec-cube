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
final class Version20210831190000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // update お届け時間ID
        $this->addSql("UPDATE dtb_csv
                SET field_name = 'time_id', reference_field_name = NULL
                WHERE id IN (119, 190)");

        // delete 送料ID
        $this->addSql('DELETE FROM dtb_csv WHERE id IN (122, 193)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
