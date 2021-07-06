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
    protected $customerAddressRepository;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->customerAddressRepository = $this->entityManager->getRepository(\Eccube\Entity\CustomerAddress::class);
    }

    public function testDelete()
    {
        $faker = $this->getFaker();
        $CustomerAddress = new CustomerAddress();
        $CustomerAddress->setCustomer($this->Customer)
            ->setName01($faker->lastName)
            ->setName02($faker->firstName);
        $this->entityManager->persist($CustomerAddress);
        $this->entityManager->flush();

        $id = $CustomerAddress->getId();
        $this->customerAddressRepository->delete($CustomerAddress);

        $CustomerAddress = $this->customerAddressRepository->find($id);
        $this->assertNull($CustomerAddress);
    }
}
