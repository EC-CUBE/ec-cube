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

namespace Eccube\Tests\Repository;

use Eccube\Entity\BaseInfo;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Tests\EccubeTestCase;

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
    /**
     * @var  string
     */
    private $id;

    /**
     * @var  BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
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
            ->setUpdateDate($faker->dateTime('now'));

        /*
         * ここでは Doctrine ORM を使用しているが、オブジェクトキャッシュ等により、
         * 期待した結果が得られない場合がある.
         * 必要に応じて、Doctrine DBAL や PDO を使用すること.
         */
        $this->entityManager->persist($BaseInfo);
        $this->entityManager->flush();
        $this->id = $BaseInfo->getId();
        $this->baseInfoRepository = $this->entityManager->getRepository(\Eccube\Entity\BaseInfo::class);
    }

    public function testGetBaseInfoWithId()
    {
        /*
         * テストコードは、できるだけ問題領域のみを記述すること.
         * 簡潔に記述することで、実装時にコピー&ペースト可能なサンプルコードとしても活用できます.
         */
        $BaseInfo = $this->baseInfoRepository->get($this->id);
        $this->assertNotNull($BaseInfo);

        $this->expected = 'company';
        $this->actual = $BaseInfo->getCompanyName();

        $this->verify('会社名は '.$this->expected.' ではありません');
    }

    public function testGetBaseInfo()
    {
        $BaseInfo = $this->baseInfoRepository->get();
        $this->assertNotNull($BaseInfo);
        $this->assertEquals(1, $BaseInfo->getId());
    }
}
