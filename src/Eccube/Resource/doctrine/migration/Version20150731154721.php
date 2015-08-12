<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150731154721 extends AbstractMigration
{

    const NAME = 'dtb_base_info';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable(self::NAME);

        if (!$table->hasColumn('option_remember_me')) {
            $table->addColumn('option_remember_me', 'smallint', array('NotNull' => false, 'default' => 0));
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
