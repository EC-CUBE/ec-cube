<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170131083319 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $tables = $schema->getTables();
        $app = \Eccube\Application::getInstance();
        $em = $app['orm.em'];
        $metadatas = $em->getMetadataFactory()->getAllMetaData();

        foreach ($metadatas as $ClassMetadata) {
            $Table = $schema->getTable($ClassMetadata->table['name']);
            if (!$Table->hasColumn('discriminator_type')) {
                $Table->addColumn('discriminator_type', 'string', array('NotNull' => false));
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $tables = $schema->getTables();
        foreach ($tables as $Table) {
            if ($Table->hasColumn('discriminator_type')) {
                $Table->dropColumn('discriminator_type');
            }
        }
    }
}
