<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20161014100031 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->createIndex($schema, 'dtb_product_class', array('price02'), 'dtb_product_class_price02_idx');
        $this->createIndex($schema, 'dtb_product_class', array('stock', 'stock_unlimited'), 'dtb_product_class_stock_stock_unlimited_idx');
        $this->createIndex($schema, 'dtb_customer', array('email'), 'dtb_customer_email_idx', array('email' => 256));
        $this->createIndex($schema, 'dtb_customer', array('create_date'), 'dtb_customer_create_date_idx');
        $this->createIndex($schema, 'dtb_customer', array('update_date'), 'dtb_customer_update_date_idx');
        $this->createIndex($schema, 'dtb_customer', array('last_buy_date'), 'dtb_customer_last_buy_date_idx');
        $this->createIndex($schema, 'dtb_customer', array('buy_times'), 'dtb_customer_buy_times_idx');
        $this->createIndex($schema, 'dtb_customer', array('buy_total'), 'dtb_customer_buy_total_idx');
        $this->createIndex($schema, 'dtb_order', array('pre_order_id'), 'dtb_order_pre_order_id_idx', array('pre_order_id' => 40));
        $this->createIndex($schema, 'dtb_order', array('order_email'), 'dtb_order_order_email_idx', array('order_email' => 256));
        $this->createIndex($schema, 'dtb_order', array('order_date'), 'dtb_order_order_date_idx');
        $this->createIndex($schema, 'dtb_order', array('payment_date'), 'dtb_order_payment_date_idx');
        $this->createIndex($schema, 'dtb_order', array('commit_date'), 'dtb_order_commit_date_idx');
        $this->createIndex($schema, 'dtb_order', array('update_date'), 'dtb_order_update_date_idx');
        $this->createIndex($schema, 'dtb_page_layout', array('url'), 'dtb_page_layout_url_idx', array('url' => 128));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->dropIndex($schema, 'dtb_product_class', 'dtb_product_class_price02_idx');
        $this->dropIndex($schema, 'dtb_product_class', 'dtb_product_class_stock_stock_unlimited_idx');
        $this->dropIndex($schema, 'dtb_customer', 'dtb_customer_email_idx');
        $this->dropIndex($schema, 'dtb_customer', 'dtb_customer_create_date_idx');
        $this->dropIndex($schema, 'dtb_customer', 'dtb_customer_update_date_idx');
        $this->dropIndex($schema, 'dtb_customer', 'dtb_customer_last_buy_date_idx');
        $this->dropIndex($schema, 'dtb_customer', 'dtb_customer_buy_times_idx');
        $this->dropIndex($schema, 'dtb_customer', 'dtb_customer_buy_total_idx');
        $this->dropIndex($schema, 'dtb_order', 'dtb_order_pre_order_id_idx');
        $this->dropIndex($schema, 'dtb_order', 'dtb_order_pre_order_email_idx');
        $this->dropIndex($schema, 'dtb_order', 'dtb_order_pre_order_date_idx');
        $this->dropIndex($schema, 'dtb_order', 'dtb_order_pre_payment_date_idx');
        $this->dropIndex($schema, 'dtb_order', 'dtb_order_pre_commit_date_idx');
        $this->dropIndex($schema, 'dtb_order', 'dtb_order_pre_update_date_idx');
        $this->dropIndex($schema, 'dtb_page_layout', 'dtb_page_layout_url_idx');
    }

    /**
     * @param Schema $schema
     * @param string $tableName
     * @param array $columns
     * @param string $indexName
     * @param array $length
     */
    protected function createIndex(Schema $schema, $tableName, array $columns, $indexName, array $length = array())
    {
        if (!$schema->hasTable($tableName)) {
            return false;
        }

        $table = $schema->getTable($tableName);
        if (!$table->hasIndex($indexName)) {
            if ($this->connection->getDatabasePlatform()->getName() == "mysql" && !empty($length)) {
                $cols = array();
                foreach ($length as $column => $len) {
                    $cols[] = sprintf('%s(%d)', $column, $len);
                }
                $this->addSql('CREATE INDEX '.$indexName.' ON '.$tableName.'('.implode(', ', $cols).');');
            } else {
                $table->addIndex($columns, $indexName);
            }
            return true;
        }
        return false;
    }

    /**
     * @param Schema $schema
     * @param string $tableName
     * @param string $indexName
     */
    protected function dropIndex(Schema $schema, $tableName, $indexName)
    {
        if (!$schema->hasTable($tableName)) {
            return false;
        }
        $table = $schema->getTable($tableName);
        if ($table->hasIndex($indexName)) {
            $table->dropIndex($indexName);
            return true;
        }
        return false;
    }
}
