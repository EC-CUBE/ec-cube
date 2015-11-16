<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151110174227 extends AbstractMigration
{

    const DTB_MAIL_HISTORY = 'dtb_mail_history';
    const DTB_MEMBER = 'dtb_member';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // dtb_category
        $t_dtb_mail_history = $schema->getTable(self::DTB_MAIL_HISTORY);

        $keyName = '';
        if($t_dtb_mail_history->hasColumn('creator_id')){
            $keys = $t_dtb_mail_history->getForeignKeys();
            foreach ($keys as $key) {
                $column = $key->getColumns();
                if ($column[0] == 'creator_id') {
                    $keyName = $key->getName();
                    break;
                }
            }
        }

        if (!empty($keyName)) {
            $t_dtb_mail_history->removeForeignKey($keyName);
        }
        $t_dtb_mail_history->changeColumn('creator_id', array('NotNull' => false));

        $targetTable = $schema->getTable(self::DTB_MEMBER);
        $t_dtb_mail_history->addForeignKeyConstraint(
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
        // this up() migration is auto-generated, please modify it to your needs
        // dtb_category
    }
}
