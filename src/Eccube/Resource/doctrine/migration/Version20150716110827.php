<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150716110827 extends AbstractMigration
{

    const NAME = 'dtb_csv';

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

        $table->addColumn('csv_id', 'integer', array(
            'autoincrement' => true,
        ));
        $table->addColumn('csv_type', 'smallint', array('NotNull' => true));
        $table->addColumn('entity_name', 'text', array('NotNull' => true));
        $table->addColumn('field_name', 'text', array('NotNull' => true));
        $table->addColumn('reference_field_name', 'text', array('NotNull' => false));
        $table->addColumn('disp_name', 'text', array('NotNull' => false));
        $table->addColumn('rank', 'smallint', array('NotNull' => true));
        $table->addColumn('enable_flg', 'smallint', array('NotNull' => true));
        $table->addColumn('creator_id', 'integer', array('NotNull' => true));
        $table->addColumn('create_date', 'datetime', array('NotNull' => true));
        $table->addColumn('update_date', 'datetime', array('NotNull' => true));
        $table->setPrimaryKey(array('csv_id'));

        $targetTable = $schema->getTable('mtb_csv_type');
        $table->addForeignKeyConstraint(
            $targetTable,
            array('csv_type'),
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
