<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161219143135 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->createIndex($schema, 'dtb_customer', array('email'), 'dtb_customer_email_idx', array('email' => 191));
        $this->createIndex($schema, 'dtb_order', array('order_email'), 'dtb_order_order_email_idx', array('order_email' => 191));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // XXX 前回のインデックスサイズを記憶しておくのは難しいため削除のみ
        $this->dropIndex($schema, 'dtb_customer', 'dtb_customer_email_idx');
        $this->dropIndex($schema, 'dtb_order', 'dtb_order_order_email_idx');
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
        if ($table->hasIndex($indexName)) {
            $this->dropIndex($schema, $tableName, $indexName);
        }
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
        }
        return true;
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
