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

namespace Eccube\Tests\Doctrine;

use Eccube\Entity\Product;
use Eccube\Repository\ProductRepository;
use Eccube\Tests\EccubeTestCase;

class TimeZoneTest extends EccubeTestCase
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function setUp()
    {
        parent::setUp();

        $this->productRepository = $this->entityManager->getRepository(\Eccube\Entity\Product::class);

        // 2000-01-01 00:00:00 +09 (jst)
        // 1999-12-31 15:00:00 +00 (utc)
        // の日時データを登録
        $sql = "
            insert into dtb_product (
                id,
                name,
                create_date,
                update_date,
                discriminator_type)
            values(
                999,
                '商品名',
                '1999-12-31 15:00:00',
                '1999-12-31 15:00:00',
                'product');";

        $this->entityManager->getConnection()->exec($sql);
    }

    public function testOrmFind()
    {
        $product = $this->productRepository->find(999);

        // jstに変換されて取得されるはず.
        $expected = '2000-01-01 00:00:00';
        $actual = $product->getCreateDate()->format('Y-m-d H:i:s');

        $this->assertEquals($expected, $actual);
    }

    /**
     * ORMで、2000-01-01 00:00:00(jst)を登録した場合のテスト.
     *
     * ORMでfindすると、 2000-01-01 00:00:00(jst) が取得できる.
     * データベース上では1999-12-31 15:00:00(utc)で登録されている.
     */
    public function testOrmPersist()
    {
        $product = new Product();
        $product->setName('商品名');

        $this->entityManager->persist($product);
        $this->entityManager->flush($product);

        // jstでcreate dateを登録
        $timezone = new \DateTimeZone(self::$container->getParameter('timezone'));
        $createDate = new \DateTime('2000-01-01 00:00:00', $timezone);

        $product->setCreateDate($createDate);
        $this->entityManager->flush($product);

        // emtity managerの管理対象からはずす
        $this->entityManager->detach($product);

        $id = $product->getId();

        // jstに変換されて取得できるはず
        $product = $this->productRepository->find($id);
        $expected = '2000-01-01 00:00:00';
        $actual = $product->getCreateDate()->format('Y-m-d H:i:s');

        $this->assertEquals($expected, $actual);

        $sql = 'select id, create_date from dtb_product where id = ?';
        $stmt = $this->entityManager->getConnection()->executeQuery($sql, [$id]);
        $product = $stmt->fetch();

        // utcで登録されているはず
        $expected = '1999-12-31 15:00:00';
        $actual = new \DateTime($product['create_date'], new \DateTimeZone('UTC'));

        $this->assertEquals($expected, $actual->format('Y-m-d H:i:s'));
    }

    public function testDbalSelect()
    {
        $sql = 'select create_date from dtb_product where id = 999';
        $stmt = $this->entityManager->getConnection()->executeQuery($sql);
        $product = $stmt->fetch();

        // dbalでselectした場合, utc時刻をそのまま取得
        $expected = '1999-12-31 15:00:00';
        $actual = new \DateTime($product['create_date'], new \DateTimeZone('UTC'));

        $this->assertEquals($expected, $actual->format('Y-m-d H:i:s'));

        // convertToPHPValueでjst時刻に変換可能
        $timezone = new \DateTimeZone(self::$container->getParameter('timezone'));
        $expected = new \DateTime('2000-01-01 00:00:00', $timezone);
        $actual = $this->entityManager->getConnection()->convertToPHPValue($product['create_date'], 'datetimetz');

        $this->assertEquals($expected, $actual);
    }

    public function testDbalInsert()
    {
        // jstで登録
        $timezone = new \DateTimeZone(self::$container->getParameter('timezone'));
        $createDate = new \DateTime('2000-01-01 00:00:00', $timezone);
        $updateDate = new \DateTime('2000-01-01 00:00:00', $timezone);

        $this->entityManager->getConnection()->insert('dtb_product', [
            'id' => 9999,
            'name' => '商品名',
            'create_date' => $createDate,
            'update_date' => $updateDate,
            'discriminator_type' => 'product',
        ], [
            'update_date' => 'datetimetz',
            'create_date' => 'datetimetz',
        ]);

        $sql = 'select id, create_date from dtb_product where id = 9999';
        $stmt = $this->entityManager->getConnection()->executeQuery($sql);
        $product = $stmt->fetch();

        // utcに変換されて登録されている
        $expected = '1999-12-31 15:00:00';
        $actual = new \DateTime($product['create_date'], new \DateTimeZone('UTC'));

        $this->assertEquals($expected, $actual->format('Y-m-d H:i:s'));
    }
}
