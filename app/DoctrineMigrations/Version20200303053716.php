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
final class Version20200303053716 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE dtb_delivery_duration SET duration = -1 WHERE id = 9 and duration = 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE dtb_delivery_duration SET duration = 0 WHERE id = 9 and duration = -1');
    }
}
