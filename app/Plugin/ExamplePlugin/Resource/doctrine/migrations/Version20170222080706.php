<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170222080706 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $Table = $schema->getTable('dtb_payment');
        if (!$Table->hasColumn('use_paypal')) {
            $Table->addColumn('use_paypal','smallint', [
                'notnul' => false,
                'default' => 0
            ]);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $Table = $schema->getTable('dtb_payment');
        if ($Table->hasColumn('use_paypal')) {
            $Table->dropColumn('use_paypal');
        }
    }
}
