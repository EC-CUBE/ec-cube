<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Inheritance Mapping に対応するため, discriminator_type カラムを追加する.
 *
 * EC-CUBE3.0.x からアップグレードする際は, ソースコードを更新する前に, このマイグレーションを実行する必要がある.
 */
class Version20170131092324 extends AbstractMigration
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
            $type = strtolower($ClassMetadata->reflClass->getShortName());
            $this->addSql('UPDATE '.$ClassMetadata->table['name']." SET discriminator_type = :type", ['type' => $type]);
            $Table = $schema->getTable($ClassMetadata->table['name']);
            $Column = $Table->getColumn('discriminator_type');
            $Column->setNotnull(true);
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
                $Column = $Table->getColumn('discriminator_type');
                $Column->setNotnull(false);
            }
        }
    }
}
