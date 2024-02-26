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
use Eccube\Entity\Master\LoginHistoryStatus;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210319122142 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $lang = env('ECCUBE_LOCALE');
        $statuses = [
            LoginHistoryStatus::FAILURE => $lang === 'en' ? 'Failure' : '失敗',
            LoginHistoryStatus::SUCCESS => $lang === 'en' ? 'Success' : '成功',
        ];

        $sortNo = $this->connection->fetchOne('SELECT MAX(sort_no) + 1 FROM mtb_login_history_status');
        if (is_null($sortNo)) {
            $sortNo = 0;
        }

        foreach ($statuses as $id => $name) {
            $statusExists = $this->connection->fetchOne(
                'SELECT COUNT(*) FROM mtb_login_history_status WHERE id = :id',
                ['id' => $id]
            );

            if ($statusExists == 0) {
                $this->addSql(
                    "INSERT INTO mtb_login_history_status (id, name, sort_no, discriminator_type) VALUES (?, ?, ?, 'loginhistorystatus')",
                    [$id, $name, $sortNo++]
                );
            }
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
