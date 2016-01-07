<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Entity\Master\CsvType;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150716110252 extends AbstractMigration
{

    const NAME = 'mtb_csv_type';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->hasTable(self::NAME)) {
            return true;
        }

        $app = \Eccube\Application::getInstance();
        $em = $app["orm.em"];

        $CsvType = new CsvType();
        $CsvType->setId(1);
        $CsvType->setName('商品CSV');
        $CsvType->setRank(3);
        $em->persist($CsvType);

        $CsvType = new CsvType();
        $CsvType->setId(2);
        $CsvType->setName('会員CSV');
        $CsvType->setRank(4);
        $em->persist($CsvType);

        $CsvType = new CsvType();
        $CsvType->setId(3);
        $CsvType->setName('受注CSV');
        $CsvType->setRank(1);
        $em->persist($CsvType);

        $CsvType = new CsvType();
        $CsvType->setId(4);
        $CsvType->setName('配送CSV');
        $CsvType->setRank(2);
        $em->persist($CsvType);

        $CsvType = new CsvType();
        $CsvType->setId(5);
        $CsvType->setName('カテゴリCSV');
        $CsvType->setRank(5);
        $em->persist($CsvType);

        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
//        $this->addSql('DELETE FROM ' . self::NAME);

    }
}
