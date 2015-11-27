<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151124184644 extends AbstractMigration
{

    const NAME = 'dtb_authority_role';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        if ($schema->hasTable(self::NAME)) {
            return true;
        }
        $table = $schema->createTable(self::NAME);

        $table->addColumn('authority_role_id', 'integer', array(
            'autoincrement' => true,
        ));
        $table->addColumn('authority_id', 'smallint', array('NotNull' => true));
        $table->addColumn('deny_url', 'text', array('NotNull' => true));
        $table->addColumn('creator_id', 'integer', array('NotNull' => true));
        $table->addColumn('create_date', 'datetime', array('NotNull' => true));
        $table->addColumn('update_date', 'datetime', array('NotNull' => true));
        $table->setPrimaryKey(array('authority_role_id'));

        $targetTable = $schema->getTable('mtb_authority');
        $table->addForeignKeyConstraint(
            $targetTable,
            array('authority_id'),
            array('id')
        );

        $targetTable = $schema->getTable('dtb_member');
        $table->addForeignKeyConstraint(
            $targetTable,
            array('creator_id'),
            array('member_id')
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        if (!$schema->hasTable(self::NAME)) {
            return true;
        }
        $schema->dropTable(self::NAME);

    }
}
