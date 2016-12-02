<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Tests\Doctrine\ORM\Tools;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\ProductTag;
use Eccube\Tests\EccubeTestCase;

class PaginationTest extends EccubeTestCase
{
    protected $expectedIds = array();

    public function setUp()
    {
        parent::setUp();

        // mysqlの場合, トランザクション中にcreate tableを行うと暗黙的にcommitされてしまい, テストデータをロールバックできない
        // そのため, create tableを行った後に, 再度トランザクションを開始するようにしている
        /** @var EntityManager $em */
        $em = $this->app['orm.em'];
        $conn = $em->getConnection();
        $conn->rollback();
        $this->dropTable($conn->getWrappedConnection());
        $this->createTable($conn->getWrappedConnection());
        $conn->beginTransaction();

        // テスト用のエンティティを用意
        $config = $em->getConfiguration();
        $driver = $config->newDefaultAnnotationDriver(__DIR__, false);
        $chain = $config->getMetadataDriverImpl();
        $chain->addDriver($driver, __NAMESPACE__);

        // 初期データより大きい値を指定
        $price02 = $this->getFaker()->randomNumber(9);
        for ($i = 0; $i < 30; $i++) {
            $Product = $this->createProduct(null, 3);
            $this->expectedIds[] = $Product->getId();

            $ProductClasses = $Product->getProductClasses();
            foreach ($ProductClasses as $ProductClass) {
                // product.idの昇順になるよう, product_class.price02を設定する
                $ProductClass->setPrice02($price02 - $i);
                $em->flush($ProductClass);
            }
        }
    }

    public function tearDown()
    {
        /** @var EntityManager $em */
        $em = $this->app['orm.em'];
        $conn = $em->getConnection();
        $conn->rollback();
        $this->dropTable($conn->getWrappedConnection());
        $conn->beginTransaction();

        parent::tearDown();
    }

    protected function createTable(\Doctrine\DBAL\Driver\Connection $conn)
    {
        $sql = 'CREATE TABLE test_entity(id INT, col INT, PRIMARY KEY(id));';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }

    protected function dropTable(\Doctrine\DBAL\Driver\Connection $conn)
    {
        $sql = 'DROP TABLE IF EXISTS test_entity;';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }

    /**
     * wrap-queries' => true時に, PostgreSQLで正常にソート出来ない不具合が解消されているかどうかのテスト
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1618
     */
    public function testSortWithWrapQueriesTrue()
    {
        $qb = $this->app['eccube.repository.product']
            ->getQueryBuilderBySearchDataForAdmin(array());

        // product_class.price02 でソートするようカスタマイズ
        $qb->orderBy('pc.price02', 'DESC');

        $pageMax = 10;
        $pagination = $this->app['paginator']()->paginate(
            $qb,
            1,
            $pageMax,
            array('wrap-queries' => true)
        );

        $actualIds = array();
        foreach ($pagination as $Product) {
            $actualIds[] = $Product->getId();
        }

        $this->expected = array_slice($this->expectedIds, 0, $pageMax);
        $this->actual = $actualIds;
        $this->verify('product_class.price02 降順なので, id 昇順にソートされるはず');
        $this->assertEquals($pageMax, count($this->actual), 'paginatorの結果は'.$pageMax.'件');
    }

    /**
     * 外部のエンティティとJoinできるかどうかのテスト
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1916
     */
    public function testSortWithJoinPluginEntity()
    {
        // idの昇順になるようにcolを設定する
        $em = $this->app['orm.em'];
        $count = count($this->expectedIds);
        foreach ($this->expectedIds as $id) {
            $TestEntity = new TestEntity();
            $TestEntity->id = $id;
            $TestEntity->col = $count--;
            $em->persist($TestEntity);
            $em->flush($TestEntity);
        }

        $qb = $this->app['eccube.repository.product']
            ->getQueryBuilderBySearchData(array());

        // テスト用のエンティティとjoinし,ソートする.
        $qb
            ->addSelect('COALESCE(test.col, 0) as HIDDEN col')
            ->leftJoin('Eccube\Tests\Doctrine\ORM\Tools\TestEntity', 'test', 'WITH', 'p.id = test.id')
            ->groupBy('p')
            ->addGroupBy('test')
            ->orderBy('col', 'DESC')
            ->addOrderBy('p.id', 'DESC');

        $Products = $qb->getQuery()->getResult();
        $expectedIds = array();
        foreach ($Products as $Product) {
            $expectedIds[] = $Product->getId();
        }

        $pageMax = 10;
        try {
            $pagination = $this->app['paginator']()->paginate(
                $qb,
                1,
                $pageMax,
                array('wrap-queries' => true)
            );
            $this->assertTrue(true);
        } catch (\RuntimeException $e) {
            // \RuntimeExceptionは解消されているはず
            $this->fail($e->getMessage());
        }

        $actualIds = array();
        foreach ($pagination as $Product) {
            $actualIds[] = $Product->getId();
        }

        $this->expected = array_slice($this->expectedIds, 0, $pageMax);
        $this->actual = $actualIds;
        $this->verify('test_entity.col 降順なので, id 昇順にソートされるはず');
        $this->assertEquals($pageMax, count($this->actual), 'paginatorの結果は'.$pageMax.'件');
    }

    /**
     * EC-CUBE本体のエンティティとjoinし, 検索するテスト
     */
    public function testWhereWithJoinEntity()
    {
        // `新商品`のTagが登録されたProductを生成
        $Tag = $this->app['eccube.repository.master.tag']
            ->find(1);
        $Member = $this->app['eccube.repository.member']
            ->find(2);
        $Product = $this->app['eccube.repository.product']
            ->find(reset($this->expectedIds));

        $ProductTag = new ProductTag();
        $ProductTag->setCreator($Member);
        $ProductTag->setProduct($Product);
        $ProductTag->setTag($Tag);
        $Product->addProductTag($ProductTag);

        $this->app['orm.em']->persist($ProductTag);
        $this->app['orm.em']->flush(array($Product, $ProductTag));

        $qb = $this->app['eccube.repository.product']
            ->getQueryBuilderBySearchData(array());

        // 商品タグとjoinして検索
        $qb->innerJoin('p.ProductTag', 'ptag')
            ->innerJoin('ptag.Tag', 'tag')
            ->andWhere($qb->expr()->in('ptag.Tag', ':Tag'))
            ->setParameter(':Tag', $Tag);

        $expectedIds = array();
        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            $expectedIds[] = $result->getId();
        }

        $pagination = $this->app['paginator']()->paginate(
            $qb,
            1,
            30,
            array('wrap-queries' => true)
        );

        $actualIds = array();
        foreach ($pagination as $result) {
            $actualIds[] = $result->getId();
        }

        $this->expected = $expectedIds;
        $this->actual = $actualIds;
        // tagが登録されたProductは1件のみ.
        $this->assertTrue(count($this->actual) === 1);
        $this->verify();
    }

    /**
     * 外部からwhere句を追加するテスト
     */
    public function testWhereWithSubQueryPluginEntity()
    {
        $TestEntity = new TestEntity();
        $TestEntity->id = reset($this->expectedIds);
        $TestEntity->col = 123;
        $this->app['orm.em']->persist($TestEntity);
        $this->app['orm.em']->flush($TestEntity);

        $qb = $this->app['eccube.repository.product']
            ->getQueryBuilderBySearchData(array());

        // テスト用のエンティティを検索するクエリ
        $repository = $this->app['orm.em']->getRepository('Eccube\Tests\Doctrine\ORM\Tools\TestEntity');
        $testQb = $repository->createQueryBuilder('test');
        $testQb->select('test.id');
        $testQb->where('test.col = :col');
        $testQb->setParameter('col', 123);

        // 本体のクエリビルダに追加する
        $qb->andWhere($qb->expr()->in('p.id', $testQb->getDQL()));
        $parameters = $testQb->getParameters();
        foreach ($parameters as $parameter) {
            $qb->setParameter($parameter->getName(), $parameter->getValue());
        }

        $expectedIds = array();
        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            $expectedIds[] = $result->getId();
        }

        $pagination = $this->app['paginator']()->paginate(
            $qb,
            1,
            30,
            array('wrap-queries' => true)
        );

        $actualIds = array();
        foreach ($pagination as $result) {
            $actualIds[] = $result->getId();
        }

        $this->expected = $expectedIds;
        $this->actual = $actualIds;
        // 1件のみマッチする
        $this->assertTrue(count($this->actual) === 1);
        $this->verify();
    }
}

/**
 * テスト用のエンティティ
 *
 * @ORM\Entity(repositoryClass="Eccube\Tests\Doctrine\ORM\Tools\TestRepository")
 * @ORM\Table(name="test_entity")
 */
class TestEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public $id;

    /**
     * @ORM\Column(type="integer")
     */
    public $col;
}

class TestRepository extends EntityRepository
{
}
