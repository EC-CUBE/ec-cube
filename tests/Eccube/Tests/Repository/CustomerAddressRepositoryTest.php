<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Master\CustomerStatus;

/**
 * CustomerAddressRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class CustomerAddressRepositoryTest extends EccubeTestCase
{
    protected $Customer;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
    }

    public function testFindOrCreateByCustomerAndId()
    {
        $CustomerAddress = $this->app['eccube.repository.customer_address']->findOrCreateByCustomerAndId($this->Customer, null);
        $this->assertNotNull($CustomerAddress);

        $faker = $this->getFaker();

        $CustomerAddress
            ->setName01($faker->lastName)
            ->setName02($faker->firstName);
        $this->app['orm.em']->persist($CustomerAddress);
        $this->app['orm.em']->flush();

        $id = $CustomerAddress->getId();
        $this->assertNotNull($id);

        $ExistsCustomerAddress = $this->app['eccube.repository.customer_address']->findOrCreateByCustomerAndId($this->Customer, $id);
        $this->assertNotNull($ExistsCustomerAddress);

        $this->expected = $id;
        $this->actual = $ExistsCustomerAddress->getId();
        $this->verify('ID は'.$this->expected.'ではありません');
        $this->assertSame($this->Customer, $ExistsCustomerAddress->getCustomer());
    }

    public function testFindOrCreateByCustomerAndIdWithException()
    {
        try {
            $CustomerAddress = $this->app['eccube.repository.customer_address']->findOrCreateByCustomerAndId($this->Customer, 9999);
            $this->fail();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $this->expected = 'No result was found for query although at least one row was expected.';
            $this->actual = $e->getMessage();
            $this->verify();
        }
    }

    public function testDeleteByCustomerAndId()
    {
        $CustomerAddress = $this->app['eccube.repository.customer_address']->findOrCreateByCustomerAndId($this->Customer, null);
        $this->app['orm.em']->persist($CustomerAddress);
        $this->app['orm.em']->flush();

        $result = $this->app['eccube.repository.customer_address']->deleteByCustomerAndId($this->Customer, $CustomerAddress->getId());
        $this->assertTrue($result);
    }

    public function testDeleteByCustomerAndIdWithException()
    {
        $result = $this->app['eccube.repository.customer_address']->deleteByCustomerAndId($this->Customer, 9999);
        $this->assertFalse($result);
    }
}
