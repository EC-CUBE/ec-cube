<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150728172928 extends AbstractMigration
{

    const NAME = 'dtb_product';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $t = $schema->getTable(self::NAME);

        if ($t->hasColumn('delivery_date_id')) {

            $keys = $t->getForeignKeys();
            foreach ($keys as $key) {
                $column = $key->getColumns();
                if ($column[0] == 'delivery_date_id') {
                    $keyName = $key->getName();
                    $t->removeForeignKey($keyName);
                    $t->dropColumn('delivery_date_id');
                    break;
                }
            }

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
