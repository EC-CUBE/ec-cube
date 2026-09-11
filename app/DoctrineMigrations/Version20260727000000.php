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
 * FAQ ブロック（dtb_block）を既存環境へ投入する。
 *
 * dtb_faq テーブルそのものは Entity 属性が源泉で schema:update が反映するため、ここでは扱わない。
 */
final class Version20260727000000 extends AbstractMigration
{
    public const NAME = 'dtb_block';

    public function up(Schema $schema): void
    {
        // テーブルが存在しない場合終了
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $blockExists = $this->connection->fetchOne("SELECT COUNT(*) FROM dtb_block WHERE file_name = 'faq'");
        if ($blockExists == 0) {
            $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, use_controller, deletable, discriminator_type) VALUES (10, 'FAQ', 'faq', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0', 'block')");
        }
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // テーブルが存在しない場合終了
        if (!$schema->hasTable(self::NAME)) {
            return;
        }

        $this->addSql("DELETE FROM dtb_block WHERE file_name = 'faq'");
    }
}
