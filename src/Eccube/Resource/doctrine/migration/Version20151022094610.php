<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151022094610 extends AbstractMigration
{

    const DTB_CATEGORY='dtb_category';
    const DTB_CLASS_CATEGORY='dtb_class_category';
    const DTB_CLASS_NAME='dtb_class_name';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // dtb_category
        $t_dtb_category=$schema->getTable(self::DTB_CATEGORY);
        if($t_dtb_category->hasColumn('category_name')){
            $t_dtb_category->changeColumn('category_name', array('NotNull'=>true));
        }
        if($t_dtb_category->hasColumn('rank')){
            $t_dtb_category->changeColumn('rank', array('NotNull'=>true));
        }
        // dtb_class_category
        $t_dtb_class_category=$schema->getTable(self::DTB_CLASS_CATEGORY);
        if($t_dtb_class_category->hasColumn('name')){
            $t_dtb_class_category->changeColumn('name', array('NotNull'=>true));
        }
        if($t_dtb_class_category->hasColumn('rank')){
            $t_dtb_class_category->changeColumn('rank', array('NotNull'=>true));
        }
        // dtb_class_name
        $t_dtb_class_name=$schema->getTable(self::DTB_CLASS_NAME);
        if($t_dtb_class_name->hasColumn('name')){
            $t_dtb_class_name->changeColumn('name', array('NotNull'=>true));
        }
        if($t_dtb_class_name->hasColumn('rank')){
            $t_dtb_class_name->changeColumn('rank', array('NotNull'=>true));
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
