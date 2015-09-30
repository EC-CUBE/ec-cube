<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\EccubeDatabaseTestCase;

use Eccube\Application;
use Eccube\Entity\Master\DeviceType;

class ProductRepositoryTest extends EccubeDatabaseTestCase
{
    public function testGetInstance()
    {
        $Product = self::$app['eccube.repository.product']->find(1);
        $this->assertNotNull($Product);
        $this->assertEquals(1, $Product->getId());
    }
}
