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
final class Version20201127000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $count = $this->connection->fetchColumn("SELECT COUNT(*) FROM dtb_page WHERE url = 'entry_confirm'");
        if ($count > 0) {
            return;
        }
        $pageId = $this->connection->fetchColumn('SELECT MAX(id) FROM dtb_page');
        $sortNo = $this->connection->fetchColumn('SELECT MAX(sort_no) FROM dtb_page_layout');

        $pageId++;
        $this->addSql("INSERT INTO dtb_page (
            id, master_page_id, page_name, url, file_name, edit_type, create_date, update_date, meta_robots, discriminator_type
        ) VALUES(
            $pageId, 18, '会員登録(確認ページ)', 'entry_confirm', 'Entry/confirm', 3, '2020-01-12 01:15:03', '2020-01-12 01:15:03', 'noindex', 'page'
        )");

        $sortNo++;
        $this->addSql("INSERT INTO dtb_page_layout (page_id, layout_id, sort_no, discriminator_type) VALUES ($pageId, 2, $sortNo, 'pagelayout')");

        $pageId++;
        $this->addSql("INSERT INTO dtb_page (
            id, master_page_id, page_name, url, file_name, edit_type, create_date, update_date, meta_robots, discriminator_type
        ) VALUES(
            $pageId, 12, 'MYページ/退会手続き(確認ページ)', 'mypage_withdraw_confirm', 'Mypage/withdraw_confirm', 3, '2020-01-12 01:15:03', '2020-01-12 01:15:03', 'noindex', 'page'
        )");

        $sortNo++;
        $this->addSql("INSERT INTO dtb_page_layout (page_id, layout_id, sort_no, discriminator_type) VALUES ($pageId, 2, $sortNo, 'pagelayout')");

        $pageId++;
        $this->addSql("INSERT INTO dtb_page (
            id, master_page_id, page_name, url, file_name, edit_type, create_date, update_date, meta_robots, discriminator_type
        ) VALUES(
            $pageId, 16, 'お問い合わせ(確認ページ)', 'contact_confirm', 'Contact/confirm', 3, '2020-01-12 01:15:03', '2020-01-12 01:15:03', 'noindex', 'page'
        )");

        $sortNo++;
        $this->addSql("INSERT INTO dtb_page_layout (page_id, layout_id, sort_no, discriminator_type) VALUES ($pageId, 2, $sortNo, 'pagelayout')");

        if ($this->platform->getName() === 'postgresql') {
            $this->addSql("SELECT setval('dtb_page_id_seq', $pageId)");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
