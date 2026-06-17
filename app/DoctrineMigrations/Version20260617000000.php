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
 * 受注管理用メモ(order_memo)の CSV 出力項目を dtb_csv へ追加する.
 *
 * 新規インストールは import_csv/{ja,en}/dtb_csv.csv で反映されるため、
 * 本マイグレーションは既存環境向け(冪等)。
 */
final class Version20260617000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // 商品 CSV(csv_type_id = 1)へ Product.order_memo を追加
        $productExists = $this->connection->fetchOne("SELECT COUNT(*) FROM dtb_csv WHERE csv_type_id = 1 AND field_name = 'order_memo'");
        if ($productExists == 0) {
            $sortNo = $this->connection->fetchOne('SELECT MAX(sort_no) + 1 FROM dtb_csv WHERE csv_type_id = 1');
            $this->addSql("INSERT INTO dtb_csv (
                csv_type_id, creator_id, entity_name, field_name, disp_name, sort_no, enabled, create_date, update_date, discriminator_type
            ) VALUES (
                1, null, ?, 'order_memo', '受注管理用メモ', $sortNo, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'csv'
            )",
                ['Eccube\\\\Entity\\\\Product']);
        }

        // 配送/出荷 CSV(csv_type_id = 4)へ OrderItem.order_memo を追加
        $orderItemExists = $this->connection->fetchOne("SELECT COUNT(*) FROM dtb_csv WHERE csv_type_id = 4 AND field_name = 'order_memo'");
        if ($orderItemExists == 0) {
            $sortNo = $this->connection->fetchOne('SELECT MAX(sort_no) + 1 FROM dtb_csv WHERE csv_type_id = 4');
            $this->addSql("INSERT INTO dtb_csv (
                csv_type_id, creator_id, entity_name, field_name, disp_name, sort_no, enabled, create_date, update_date, discriminator_type
            ) VALUES (
                4, null, ?, 'order_memo', '受注管理用メモ', $sortNo, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'csv'
            )",
                ['Eccube\\\\Entity\\\\OrderItem']);
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM dtb_csv WHERE csv_type_id = 1 AND entity_name = 'Eccube\\\\Entity\\\\Product' AND field_name = 'order_memo'");
        $this->addSql("DELETE FROM dtb_csv WHERE csv_type_id = 4 AND entity_name = 'Eccube\\\\Entity\\\\OrderItem' AND field_name = 'order_memo'");
    }
}
