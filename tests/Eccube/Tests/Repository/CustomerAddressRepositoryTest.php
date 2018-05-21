<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Repository;

use Eccube\Entity\CustomerAddress;
use Eccube\Repository\CustomerAddressRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * CustomerAddressRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class CustomerAddressRepositoryTest extends EccubeTestCase
{
    protected $Customer;

    /**
     * @var CustomerAddressRepository
     */
    protected $customerAddress;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->customerAddress = $this->container->get(CustomerAddressRepository::class);
    }

    public function testFindOrCreateByCustomerAndId()
    {
        $CustomerAddress = $this->customerAddress->findOrCreateByCustomerAndId($this->Customer, null);
        $this->assertNotNull($CustomerAddress);

        $faker = $this->getFaker();

        $CustomerAddress
            ->setName01($faker->lastName)
            ->setName02($faker->firstName);
        $this->entityManager->persist($CustomerAddress);
        $this->entityManager->flush();

        $id = $CustomerAddress->getId();
        $this->assertNotNull($id);

        $ExistsCustomerAddress = $this->customerAddress->findOrCreateByCustomerAndId($this->Customer, $id);
        $this->assertNotNull($ExistsCustomerAddress);

        $this->expected = $id;
        $this->actual = $ExistsCustomerAddress->getId();
        $this->verify('ID は'.$this->expected.'ではありません');
        $this->assertSame($this->Customer, $ExistsCustomerAddress->getCustomer());
    }

    public function testFindOrCreateByCustomerAndIdWithException()
    {
        try {
            $this->customerAddress->findOrCreateByCustomerAndId($this->Customer, 9999);
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
        $this->entityManager->persist($CustomerAddress);
        $this->entityManager->flush();

        $id = $CustomerAddress->getId();
        $this->customerAddress->delete($CustomerAddress);

        $CustomerAddress = $this->customerAddress->find($id);
        $this->assertNull($CustomerAddress);
    }
}
