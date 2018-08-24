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
