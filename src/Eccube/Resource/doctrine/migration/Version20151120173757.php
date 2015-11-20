<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151120173757 extends AbstractMigration
{
    const DTB_CUSTOMER='dtb_customer';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // dtb_customer
        $t_dtb_customer = $schema->getTable(self::DTB_CUSTOMER);
        if($t_dtb_customer->hasColumn('kana01')){
            $t_dtb_customer->changeColumn('kana01', array('NotNull' => true));
        }
        if($t_dtb_customer->hasColumn('kana02')){
            $t_dtb_customer->changeColumn('kana02', array('NotNull' => true));
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
