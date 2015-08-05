<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150805105421 extends AbstractMigration
{
    protected $targetTables = array(
        'dtb_send_history',
        'dtb_send_customer',
    );

    protected $targetSequences = array(
        'dtb_send_history_send_id_seq',
    );

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        foreach ($this->targetTables as $table) {
            if ($schema->hasTable($table)) {
                $schema->dropTable($table);
            }
        }

        if ($this->connection->getDatabasePlatform()->getName() == "postgresql") {
            foreach ($this->targetSequences as $seq) {
                if ($schema->hasSequence($seq)) {
                    $schema->dropSequence($seq);
                }
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
