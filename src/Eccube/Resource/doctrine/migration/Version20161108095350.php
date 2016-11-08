<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Application;
use Eccube\Entity\Master\ProductListOrderBy;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161108095350 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $app = Application::getInstance();
        $repository = $app['orm.em']->getRepository('Eccube\Entity\Master\ProductListOrderBy');

        // 価格が高い順ソートの追加
        $ProductListOrderBy = $repository->find(3);
        if (is_null($ProductListOrderBy)) {
            $rank = $repository->createQueryBuilder('pl')
                ->select('MAX(pl.rank)')
                ->getQuery()
                ->getSingleScalarResult();
            $ProductListOrderBy = new ProductListOrderBy();
            $ProductListOrderBy->setId(3);
            $ProductListOrderBy->setName('価格が高い順');
            $ProductListOrderBy->setRank($rank + 1);
            $app['orm.em']->persist($ProductListOrderBy);
            $app['orm.em']->flush($ProductListOrderBy);
        }

        // "価格順"の名称を"価格が低い順"へ変更
        $ProductListOrderBy = $repository->find(1);
        if (!is_null($ProductListOrderBy) && $ProductListOrderBy->getName() === '価格順') {
            $ProductListOrderBy->setName('価格が低い順');
            $app['orm.em']->persist($ProductListOrderBy);
            $app['orm.em']->flush($ProductListOrderBy);
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
