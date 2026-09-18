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
final class Version20230515023836 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $exists = $this->connection->fetchOne("SELECT count(*) FROM dtb_mail_template WHERE file_name = 'Mail/customer_change_notify.twig'");
        if ($exists == 0) {
            $this->addSql("
                INSERT INTO dtb_mail_template (creator_id, name, file_name, mail_subject, create_date, update_date, discriminator_type)
                VALUES (null, '会員情報変更通知メール', 'Mail/customer_change_notify.twig', '会員情報変更のお知らせ', '2017-03-07 10:14:52', '2017-03-07 10:14:52', 'mailtemplate');");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
