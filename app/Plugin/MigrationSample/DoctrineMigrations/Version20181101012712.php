<?php

namespace Plugin\MigrationSample\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181101012712 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $Table = $schema->getTable('dtb_base_info');
        if ($Table->hasColumn('migration_sample')) {
            $this->addSql('UPDATE dtb_base_info SET migration_sample = ? WHERE id = 1', ['up']);
            dump('up');
        }
    }

    public function down(Schema $schema) : void
    {
        $Table = $schema->getTable('dtb_base_info');
        if ($Table->hasColumn('migration_sample')) {
            $this->addSql('UPDATE dtb_base_info SET migration_sample = ? WHERE id = 1', ['down']);
            dump('down');
        }
    }
}
