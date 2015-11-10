<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151110174227 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $t=$schema->getTable('dtb_mail_history');
        if($t->hasColumn('creator_id')){
            $this->addSql('alter table dtb_mail_history change column creator_id creator_id int(11) default null;');
            $t->dropColumn('stock_unlimited_tmp');
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
