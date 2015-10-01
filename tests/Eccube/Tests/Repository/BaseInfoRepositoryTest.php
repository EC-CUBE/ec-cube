<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Entity\BaseInfo;


/**
 * BaseInfoRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class BaseInfoRepositoryTest extends EccubeTestCase
{
    private $id;

    public function setUp()
    {
        parent::setUp();

        $faker = $this->getFaker();

        $this->expected = $faker->company;

        $BaseInfo = new BaseInfo();
        $BaseInfo
             ->setCompanyName($this->expected)
             ->setShopName($faker->company)
             ->setAddr01($faker->address)
             ->setAddr02($faker->secondaryAddress)
             ->setEmail01($faker->email)
             ->setLatitude($faker->latitude)
             ->setLongitude($faker->longitude)
             ->setUpdateDate($faker->dateTime('now'));

         $this->app['orm.em']->persist($BaseInfo);
         $this->app['orm.em']->flush();
         $this->id = $BaseInfo->getId();
    }

    public function testGetBaseInfo()
    {
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->assertNotNull($BaseInfo);
        $this->assertEquals(1, $BaseInfo->getId());
    }

    public function testGetBaseInfoWithId()
    {
         $Result = $this->app['eccube.repository.base_info']->get($this->id);
         $this->assertNotNull($Result);

         $this->actual = $Result->getCompanyName();

         $this->verify('会社名は '.$this->expected.' ではありません');
     }
}
