<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Doctrine\ORM\Tools;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\ProductTag;
use Eccube\Entity\Tag;
use Eccube\Repository\MemberRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\TagRepository;
use Eccube\Tests\EccubeTestCase;
use Knp\Component\Pager\PaginatorInterface;

class PaginationTest extends EccubeTestCase
{
    /**
     * @var array
     */
    protected $expectedIds = [];

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setUp()
    {
        parent::setUp();

        $this->productRepository = $this->entityManager->getRepository(\Eccube\Entity\Product::class);
        $this->paginator = self::$container->get(PaginatorInterface::class);
        $this->tagRepository = $this->entityManager->getRepository(\Eccube\Entity\Tag::class);
        $this->memberRepository = $this->entityManager->getRepository(\Eccube\Entity\Member::class);

        // mysqlの場合, トランザクション中にcreate tableを行うと暗黙的にcommitされてしまい, テストデータをロールバックできない
        // そのため, create tableを行った後に, 再度トランザクションを開始するようにしている
        /** @var EntityManager $em */
        $em = $this->entityManager;
        $conn = $em->getConnection();
        if (!$conn->isConnected()) {
            $conn->connect();
        }
        if ($conn->isTransactionActive()) {
            $conn->rollback();
        }

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
        for ($i = 0; $i < 5; $i++) {
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
        $em = $this->entityManager;
        if ($em) {
            $conn = $em->getConnection();
            $conn->rollback();
            $this->dropTable($conn->getWrappedConnection());
            $conn->beginTransaction();
        }

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
     * @see https://github.com/EC-CUBE/ec-cube/issues/1618
     */
    public function testSortWithWrapQueriesTrue()
    {
        $qb = $this->productRepository->getQueryBuilderBySearchDataForAdmin([]);

        // product_class.price02 でソートするようカスタマイズ
        $qb->orderBy('pc.price02', 'DESC');

        $pageMax = 3;
        $pagination = $this->paginator->paginate(
            $qb,
            1,
            $pageMax,
            ['wrap-queries' => true]
        );

        $actualIds = [];
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
     * @see https://github.com/EC-CUBE/ec-cube/issues/1916
     */
    public function testSortWithJoinPluginEntity()
    {
        // idの昇順になるようにcolを設定する
        $em = $this->entityManager;
        $count = count($this->expectedIds);
        foreach ($this->expectedIds as $id) {
            $TestEntity = new TestEntity();
            $TestEntity->id = $id;
            $TestEntity->col = $count--;
            $em->persist($TestEntity);
            $em->flush($TestEntity);
        }

        $qb = $this->productRepository->getQueryBuilderBySearchData([]);

        // テスト用のエンティティとjoinし,ソートする.
        $qb
            ->addSelect('COALESCE(test.col, 0) as HIDDEN col')
            ->leftJoin('Eccube\Tests\Doctrine\ORM\Tools\TestEntity', 'test', 'WITH', 'p.id = test.id')
            ->groupBy('p')
            ->addGroupBy('test')
            ->orderBy('col', 'DESC')
            ->addOrderBy('p.id', 'DESC');

        $Products = $qb->getQuery()->getResult();
        $expectedIds = [];
        foreach ($Products as $Product) {
            $expectedIds[] = $Product->getId();
        }

        $pageMax = 3;
        try {
            $pagination = $this->paginator->paginate(
                $qb,
                1,
                $pageMax,
                ['wrap-queries' => true]
            );
            $this->assertTrue(true);
        } catch (\RuntimeException $e) {
            // \RuntimeExceptionは解消されているはず
            $this->fail($e->getMessage());

            return;
        }

        $actualIds = [];
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
        $MaxTag = $this->tagRepository->findOneBy([], ['sort_no' => 'DESC']);
        $Tag = new Tag();
        $Tag->setName('join-test');
        $Tag->setSortNo($MaxTag->getSortNo() + 1);
        $this->entityManager->persist($Tag);
        $this->entityManager->flush();

        $Member = $this->memberRepository->find(2);
        $Product = $this->productRepository->find(reset($this->expectedIds));

        $ProductTag = new ProductTag();
        $ProductTag->setCreator($Member);
        $ProductTag->setProduct($Product);
        $ProductTag->setTag($Tag);
        $Product->addProductTag($ProductTag);

        $this->entityManager->persist($ProductTag);
        $this->entityManager->flush();

        $qb = $this->productRepository->getQueryBuilderBySearchData([]);

        // 商品タグとjoinして検索
        $qb->innerJoin('p.ProductTag', 'ptag')
            ->innerJoin('ptag.Tag', 'tag')
            ->andWhere($qb->expr()->in('ptag.Tag', ':Tag'))
            ->setParameter(':Tag', $Tag);

        $expectedIds = [];
        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            $expectedIds[] = $result->getId();
        }

        $pagination = $this->paginator->paginate(
            $qb,
            1,
            3,
            ['wrap-queries' => true]
        );

        $actualIds = [];
        foreach ($pagination as $result) {
            $actualIds[] = $result->getId();
        }

        $this->expected = $expectedIds;
        $this->actual = $actualIds;
        // tagが登録されたProductは1件のみ.
        $this->assertSame(count($this->actual), 1);
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
        $this->entityManager->persist($TestEntity);
        $this->entityManager->flush($TestEntity);

        $qb = $this->productRepository->getQueryBuilderBySearchData([]);

        // テスト用のエンティティを検索するクエリ
        $repository = $this->entityManager->getRepository('Eccube\Tests\Doctrine\ORM\Tools\TestEntity');
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

        $expectedIds = [];
        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            $expectedIds[] = $result->getId();
        }

        $pagination = $this->paginator->paginate(
            $qb,
            1,
            3,
            ['wrap-queries' => true]
        );

        $actualIds = [];
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
