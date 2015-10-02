<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Entity\BaseInfo;


/**
 * BaseInfoRepository test cases.
 *
 * このテストケースは、 BaseInfoRepository のテストと、 EccubeTestCase のサンプルコードを兼ねています.
 *
 * テストコードのプログラミングスタイルについては、
 * [JUnit実践講座](http://objectclub.jp/community/memorial/homepage3.nifty.com/masarl/article/junit.html)
 * が参考になります.
 *
 * @author Kentaro Ohkouchi
 */
class BaseInfoRepositoryTest extends EccubeTestCase
{
    private $id;

    public function setUp()
    {
        // テスト時に Application やデータベース接続が不要な場合は、この行を削除してください.
        parent::setUp();

        // ダミーデータを生成する Faker
        $faker = $this->getFaker();

        // テスト用のデータを生成する.
        $BaseInfo = new BaseInfo();
        $BaseInfo
            ->setCompanyName('company')
            ->setShopName($faker->company)
            ->setAddr01($faker->address)
            ->setAddr02($faker->secondaryAddress)
            ->setEmail01($faker->email)
            ->setLatitude($faker->latitude)
            ->setLongitude($faker->longitude)
            ->setUpdateDate($faker->dateTime('now'));

        /*
         * ここでは Doctrine ORM を使用しているが、オブジェクトキャッシュ等により、
         * 期待した結果が得られない場合がある.
         * 必要に応じて、Doctrine DBAL や PDO を使用すること.
         */
        $this->app['orm.em']->persist($BaseInfo);
        $this->app['orm.em']->flush();
        $this->id = $BaseInfo->getId();
    }

    public function testGetBaseInfoWithId()
    {
        /*
         * テストコードは、できるだけ問題領域のみを記述すること.
         * 簡潔に記述することで、実装時にコピー&ペースト可能なサンプルコードとしても活用できます.
         */
        $BaseInfo = $this->app['eccube.repository.base_info']->get($this->id);
        $this->assertNotNull($BaseInfo);

        $this->expected = 'company';
        $this->actual = $BaseInfo->getCompanyName();

        $this->verify('会社名は '.$this->expected.' ではありません');
    }

    public function testGetBaseInfo()
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->assertNotNull($BaseInfo);
        $this->assertEquals(1, $BaseInfo->getId());
    }
}
