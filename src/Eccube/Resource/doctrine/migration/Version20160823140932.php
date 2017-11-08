<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160823140932 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($this->platform->getName() == 'postgresql') {
            $qb = $this->connection->createQueryBuilder();
            $max = $qb->select('max(id) + 1')
                ->from('dtb_help')
                ->execute()
                ->fetchColumn();
            $this->addSql("SELECT setval('dtb_help_id_seq', $max);");
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
