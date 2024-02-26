<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230928014611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // 重複した在庫がdtb_product_stockにあるのか確認する。
        $exists = $this->connection->fetchAllKeyValue(" 
SELECT product_class_id, product_class_id_count
FROM (
         SELECT
             product_class_id,
             COUNT(product_class_id) AS product_class_id_count
         FROM
             dtb_product_stock
         GROUP BY
             product_class_id
     ) AS subquery
WHERE product_class_id_count > 1;
");

        // 重複在庫がある場合、dtb_product_class.stockを正として、それ以外の在庫情報は削除する
        if (count($exists) != 0) {
            foreach ($exists as $pc_id => $value) {
                $stock = $this->connection->fetchOne("SELECT stock FROM dtb_product_class WHERE id = :id", ["id" => $pc_id]);
                $this->addSql("DELETE FROM dtb_product_stock WHERE product_class_id = :pc_id", ["pc_id" => $pc_id]);
                $this->addSql("INSERT INTO dtb_product_stock (product_class_id, creator_id, stock, create_date, update_date, discriminator_type) VALUES (:pc_id, NULL, :stock, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'productstock')", ["pc_id" => $pc_id, "stock" => $stock]);
            }
        }
    }

    public function down(Schema $schema): void
    {
    }
}
