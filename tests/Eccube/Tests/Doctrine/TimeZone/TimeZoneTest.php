<?php

namespace Eccube\Tests\Doctrine;

use Eccube\Entity\Product;
use Eccube\Tests\EccubeTestCase;

class TimeZoneTest extends EccubeTestCase
{
    public function setUp()
    {
        parent::setUp();

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

        /** @var \Eccube\Application $app */
        $app = $this->app;
        $app['db']->exec($sql);
    }

    public function testOrmFind()
    {
        /** @var \Eccube\Application $app */
        $app = $this->app;

        $product = $app['eccube.repository.product']->find(999);

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
        /** @var \Eccube\Application $app */
        $app = $this->app;

        $product = new Product();
        $product->setName('商品名');

        $app['orm.em']->persist($product);
        $app['orm.em']->flush($product);

        // jstでcreate dateを登録
        $timezone = new \DateTimeZone($app['config']['timezone']);
        $createDate = new \DateTime('2000-01-01 00:00:00', $timezone);

        $product->setCreateDate($createDate);
        $app['orm.em']->flush($product);

        // emtity managerの管理対象からはずす
        $app['orm.em']->detach($product);

        $id = $product->getId();

        // jstに変換されて取得できるはず
        $product = $app['eccube.repository.product']->find($id);
        $expected = '2000-01-01 00:00:00';
        $actual = $product->getCreateDate()->format('Y-m-d H:i:s');

        $this->assertEquals($expected, $actual);

        $sql = 'select id, create_date from dtb_product where id = ?';
        $stmt = $app['db']->executeQuery($sql, [$id]);
        $product = $stmt->fetch();

        // utcで登録されているはず
        $expected = '1999-12-31 15:00:00';
        $actual = new \DateTime($product['create_date'], new \DateTimeZone('UTC'));

        $this->assertEquals($expected, $actual->format('Y-m-d H:i:s'));
    }

    public function testDbalSelect()
    {
        /** @var \Eccube\Application $app */
        $app = $this->app;

        $sql = 'select create_date from dtb_product where id = 999';
        $stmt = $app['db']->executeQuery($sql);
        $product = $stmt->fetch();

        // dbalでselectした場合, utc時刻をそのまま取得
        $expected = '1999-12-31 15:00:00';
        $actual = new \DateTime($product['create_date'], new \DateTimeZone('UTC'));

        $this->assertEquals($expected, $actual->format('Y-m-d H:i:s'));

        // convertToPHPValueでjst時刻に変換可能
        $timezone = new \DateTimeZone($app['config']['timezone']);
        $expected = new \DateTime('2000-01-01 00:00:00', $timezone);
        $actual = $app['db']->convertToPHPValue($product['create_date'], 'datetimetz');

        $this->assertEquals($expected, $actual);
    }

    public function testDbalInsert()
    {
        /** @var \Eccube\Application $app */
        $app = $this->app;

        // jstで登録
        $timezone = new \DateTimeZone($app['config']['timezone']);
        $createDate = new \DateTime('2000-01-01 00:00:00', $timezone);
        $updateDate = new \DateTime('2000-01-01 00:00:00', $timezone);

        $app['db']->insert('dtb_product', [
            'id' => 9999,
            'name' => '商品名',
            'create_date' => $createDate,
            'update_date' => $updateDate,
            'discriminator_type' => 'product'
        ], [
            'update_date' => 'datetimetz',
            'create_date' => 'datetimetz',
        ]);

        $sql = 'select id, create_date from dtb_product where id = 9999';
        $stmt = $app['db']->executeQuery($sql);
        $product = $stmt->fetch();

        // utcに変換されて登録されている
        $expected = '1999-12-31 15:00:00';
        $actual = new \DateTime($product['create_date'], new \DateTimeZone('UTC'));

        $this->assertEquals($expected, $actual->format('Y-m-d H:i:s'));
    }

}
