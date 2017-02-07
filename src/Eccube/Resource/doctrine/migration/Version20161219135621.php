<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161219135621 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // 後続の Version20161108095350.php でインデックスを再作成するため一旦削除する
        // 同一マイグレーションファイル内でインデックスの drop/create をしようとするとエラーになるため
        $this->dropIndex($schema, 'dtb_customer', 'dtb_customer_email_idx');
        $this->dropIndex($schema, 'dtb_order', 'dtb_order_order_email_idx');
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
