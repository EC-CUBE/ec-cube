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
final class Version20211111050712 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE dtb_page SET meta_robots = 'noindex' WHERE url = 'contact_complete' AND ( meta_robots = '' OR meta_robots is null )");
        $this->addSql("UPDATE dtb_page SET meta_robots = 'noindex' WHERE url = 'entry_complete' AND ( meta_robots = '' OR meta_robots is null )");
        $this->addSql("UPDATE dtb_page SET meta_robots = 'noindex' WHERE url = 'shopping_login' AND ( meta_robots = '' OR meta_robots is null )");
        $this->addSql("UPDATE dtb_page SET meta_robots = 'noindex' WHERE url = 'shopping_nonmember' AND ( meta_robots = '' OR meta_robots is null )");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
