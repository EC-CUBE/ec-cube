<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220603074035 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $statusExists = $this->connection->fetchColumn(
            'SELECT COUNT(*) FROM mtb_csv_type WHERE id = 6'
        );
        if ($statusExists == 0) {
            $this->addSql("INSERT INTO mtb_csv_type ('id', 'name', 'sort_no', 'discriminator_type') VALUES (6, 'Class NameCSV', 6, 'csvtype');");
        }
        $statusExists = $this->connection->fetchColumn(
            'SELECT COUNT(*) FROM mtb_csv_type WHERE id = 7'
        );
        if ($statusExists == 0) {
            $this->addSql("INSERT INTO mtb_csv_type ('id', 'name', 'sort_no', 'discriminator_type') VALUES (7, 'Class Category CSV', 7, 'csvtype');");
        }
        $statusExists = $this->connection->fetchColumn(
            'SELECT COUNT(*) FROM dtb_csv WHERE id = 206'
        );
        if ($statusExists == 0) {
            $this->addSql("INSERT INTO dtb_csv ('id','csv_type_id','creator_id','entity_name','field_name','reference_field_name','disp_name','sort_no','enabled','create_date','update_date','discriminator_type') VALUES ('206','6',,'Eccube\\Entity\\ClassName','id',,'規格ID','1','1','2021-05-18 01:26:41','2021-05-18 01:26:41','csv');");
        }
        $statusExists = $this->connection->fetchColumn(
            'SELECT COUNT(*) FROM dtb_csv WHERE id = 207'
        );
        if ($statusExists == 0) {
            $this->addSql("INSERT INTO dtb_csv ('id','csv_type_id','creator_id','entity_name','field_name','reference_field_name','disp_name','sort_no','enabled','create_date','update_date','discriminator_type') VALUES ('207','6',,'Eccube\\Entity\\ClassName','name',,'規格名','2','1','2021-05-18 01:26:41','2021-05-18 01:26:41','csv');");
        }
        $statusExists = $this->connection->fetchColumn(
            'SELECT COUNT(*) FROM dtb_csv WHERE id = 208'
        );
        if ($statusExists == 0) {
            $this->addSql("INSERT INTO dtb_csv ('id','csv_type_id','creator_id','entity_name','field_name','reference_field_name','disp_name','sort_no','enabled','create_date','update_date','discriminator_type') VALUES ('208','6',,'Eccube\\Entity\\ClassName','backend_name',,'管理名','3','1','2021-05-18 01:26:41','2021-05-18 01:26:41','csv');");
        }
        $statusExists = $this->connection->fetchColumn(
            'SELECT COUNT(*) FROM dtb_csv WHERE id = 209'
        );
        if ($statusExists == 0) {
            $this->addSql("INSERT INTO dtb_csv ('id','csv_type_id','creator_id','entity_name','field_name','reference_field_name','disp_name','sort_no','enabled','create_date','update_date','discriminator_type') VALUES ('209','7',,'Eccube\\Entity\\ClassCategory','id',,'規格分類ID','1','1','2021-05-18 01:26:41','2021-05-18 01:26:41','csv');");
        }
        $statusExists = $this->connection->fetchColumn(
            'SELECT COUNT(*) FROM dtb_csv WHERE id = 210'
        );
        if ($statusExists == 0) {
            $this->addSql("INSERT INTO dtb_csv ('id','csv_type_id','creator_id','entity_name','field_name','reference_field_name','disp_name','sort_no','enabled','create_date','update_date','discriminator_type') VALUES ('210','7',,'Eccube\\Entity\\ClassCategory','ClassName','id','規格ID','2','1','2021-05-18 01:26:41','2021-05-18 01:26:41','csv');");
        }
        $statusExists = $this->connection->fetchColumn(
            'SELECT COUNT(*) FROM dtb_csv WHERE id = 211'
        );
        if ($statusExists == 0) {
            $this->addSql("INSERT INTO dtb_csv ('id','csv_type_id','creator_id','entity_name','field_name','reference_field_name','disp_name','sort_no','enabled','create_date','update_date','discriminator_type') VALUES ('211','7',,'Eccube\\Entity\\ClassCategory','name',,'規格分類名','3','1','2021-05-18 01:26:41','2021-05-18 01:26:41','csv');");
        }
        $statusExists = $this->connection->fetchColumn(
            'SELECT COUNT(*) FROM dtb_csv WHERE id = 212'
        );
        if ($statusExists == 0) {
            $this->addSql("INSERT INTO dtb_csv ('id','csv_type_id','creator_id','entity_name','field_name','reference_field_name','disp_name','sort_no','enabled','create_date','update_date','discriminator_type') VALUES ('212','7',,'Eccube\\Entity\\ClassCategory','backend_name',,'分類管理名','4','1','2021-05-18 01:26:41','2021-05-18 01:26:41','csv');");
        }
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
