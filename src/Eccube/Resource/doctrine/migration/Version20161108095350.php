<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Application;
use Eccube\Entity\Master\ProductListOrderBy;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

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

        // mtb_product_list_orderbyが初期状態から変更がある場合は、マイグレーションを適用しない
        $default = array(
            array(
                'id' => 1,
                'name' => '価格順',
                'rank' => 0,
            ),
            array(
                'id' => 2,
                'name' => '新着順',
                'rank' => 1,
            ),
        );
        $entities = $repository->createQueryBuilder('pl')
            ->orderBy('pl.id', 'ASC')
            ->getQuery()
            ->getArrayResult();

        if ($entities !== $default) {
            return;
        }

        // 価格が高い順を追加
        $ProductListOrderBy = new ProductListOrderBy();
        $ProductListOrderBy->setId(3);
        $ProductListOrderBy->setName('価格が高い順');
        $ProductListOrderBy->setRank(2);
        $app['orm.em']->persist($ProductListOrderBy);
        $app['orm.em']->flush($ProductListOrderBy);

        // "価格順"の名称を"価格が低い順"へ変更
        $ProductListOrderBy = $repository->find(1);
        if (!is_null($ProductListOrderBy) && $ProductListOrderBy->getName() === '価格順') {
            $ProductListOrderBy->setName('価格が低い順');
            $app['orm.em']->persist($ProductListOrderBy);
            $app['orm.em']->flush($ProductListOrderBy);
        }

        // 価格が低い順->価格が高い順->新着順の順にrankを振り直す
        // 価格が低い順
        $entity = $repository->find(1);
        $entity->setRank(0);
        $app['orm.em']->flush($entity);

        // 価格が高い順
        $entity = $repository->find(3);
        $entity->setRank(1);
        $app['orm.em']->flush($entity);

        // 新着順
        $entity = $repository->find(2);
        $entity->setRank(2);
        $app['orm.em']->flush($entity);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
