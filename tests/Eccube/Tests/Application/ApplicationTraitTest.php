<?php

namespace Eccube\Tests\Application;

use Eccube\Tests\EccubeTestCase;
use Eccube\Entity\Master\CustomerStatus;
use Symfony\Component\HttpFoundation\Request;

/**
 * ApplicationTrait test cases.
 */
class ApplicationTraitTest extends EccubeTestCase
{
    public function testFind()
    {
        $Status = $this->app->find(CustomerStatus::class, CustomerStatus::ACTIVE);

        self::assertInstanceOf(CustomerStatus::class, $Status);
        self::assertEquals(CustomerStatus::ACTIVE, $Status->getId());
    }

    public function testWithFailure()
    {
        try {
            $Status = $this->app->find('\Foo\Bar\Class', CustomerStatus::ACTIVE);
            self::fail();
        } catch (\InvalidArgumentException $e) {
            self::assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }

    public function testWithNotEntity()
    {
        try {
            $Status = $this->app->find(Request::class, 1);
            self::fail();
        } catch (\Doctrine\Common\Persistence\Mapping\MappingException $e) {
            self::assertInstanceOf(\Doctrine\Common\Persistence\Mapping\MappingException::class, $e);
        }
    }
}
