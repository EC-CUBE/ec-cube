<?php

namespace Eccube\Tests\Repository;

use Eccube\Entity\CustomerAddress;
use Eccube\Tests\EccubeTestCase;

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
        $this->markTestIncomplete(get_class($this).' は未実装です');
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

    public function testDelete()
    {
        $CustomerAddress = new CustomerAddress();
        $CustomerAddress->setCustomer($this->Customer);
        $this->app['orm.em']->persist($CustomerAddress);
        $this->app['orm.em']->flush();

        $id = $CustomerAddress->getId();
        $this->app['eccube.repository.customer_address']->delete($CustomerAddress);

        $CustomerAddress = $this->app['eccube.repository.customer_address']->find($id);
        $this->assertNull($CustomerAddress);
    }
}
