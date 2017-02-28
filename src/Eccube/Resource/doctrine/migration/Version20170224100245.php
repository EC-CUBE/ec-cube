<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170224100245 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $Table = $schema->getTable('dtb_payment');
        if (!$Table->hasColumn('method_class')) {
            $Table->addColumn('method_class', 'string', [
                'notnull' => false,
                'length' => 255
            ]);
        }

        if (!$Table->hasColumn('service_class')) {
            $Table->addColumn('service_class', 'string', [
                'notnull' => false,
                'length' => 255
            ]);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $Table = $schema->getTable('dtb_payment');
        if ($Table->hasColumn('method_class')) {
            $Table->dropColumn('method_class');
        }
        if ($Table->hasColumn('service_class')) {
            $Table->dropColumn('service_class');
        }
    }
}
