<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151113150301 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        if ($this->connection->getDatabasePlatform()->getName() == 'mysql') {
            $table = $schema->getTable('dtb_customer');
            $table->changeColumn('name01', array('NotNull' => true));

            $this->addSql('ALTER TABLE dtb_csv CHANGE disp_name disp_name LONGTEXT NOT NULL');
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
