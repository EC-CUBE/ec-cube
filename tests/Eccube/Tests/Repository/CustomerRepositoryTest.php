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

use Eccube\Entity\Customer;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * CustomerRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class CustomerRepositoryTest extends EccubeTestCase
{
    /**
     * @var string
     */
    protected $email;

    /**
     * @var Customer
     */
    protected $Customer;

    /**
     * @var CustomerRepository
     */
    protected $customerRepo;

    /**
     * @var OrderStatusRepository
     */
    protected $masterOrderStatusRepo;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->email = 'customer@example.com';
        $this->Customer = $this->createCustomer($this->email);

        $this->customerRepo = $this->entityManager->getRepository(\Eccube\Entity\Customer::class);
        $this->masterOrderStatusRepo = $this->entityManager->getRepository(\Eccube\Entity\Master\OrderStatus::class);
    }

    public function testNewCustomer()
    {
        // TODO https://github.com/EC-CUBE/ec-cube/issues/870
        $Customer = $this->customerRepo->newCustomer();

        $this->expected = 1;
        $this->actual = $Customer->getStatus()->getId();
        $this->verify();
    }

    public function testGetProvisionalCustomerBySecretKey()
    {
        $this->expected = $this->Customer->getSecretKey();
        $Status = $this->entityManager->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::NONACTIVE);
        $this->Customer->setStatus($Status);
        $this->entityManager->flush();

        $Customer = $this->customerRepo->getProvisionalCustomerBySecretKey($this->expected);
        $this->actual = $Customer->getSecretKey();
        $this->verify('secretは'.$this->expected.'ではありません');
    }

    public function testGetProvisionalCustomerBySecretKeyWithException()
    {
        $secret = $this->Customer->getSecretKey();

        // CustomerStatus::REGULARなので取得できないはず
        $Customer = $this->customerRepo->getProvisionalCustomerBySecretKey($secret);
        $this->assertNull($Customer);
    }

    public function testGetRegularCustomerByEmail()
    {
        // XXX loadUserByUsername() と同じ役割？
        $this->actual = $this->Customer;
        $this->expected = $this->customerRepo->getRegularCustomerByEmail($this->email);
        $this->verify();
    }

    public function testGetRegularCustomerByResetKey()
    {
        $expire = '+'.$this->eccubeConfig['eccube_customer_reset_expire'].' min';
        $reset_key = $this->customerRepo->getResetPassword();
        $this->Customer
            ->setResetKey($reset_key)
            ->setResetExpire(new \DateTime($expire));
        $this->entityManager->flush();

        $Customer = $this->customerRepo->getRegularCustomerByResetKey($reset_key);

        $this->assertNotNull($Customer);
    }

    public function testGetRegularCustomerByResetKeyWithException()
    {
        $expire = '-'.$this->eccubeConfig['eccube_customer_reset_expire'].' min';
        $reset_key = $this->customerRepo->getResetPassword();
        $this->Customer
            ->setResetKey($reset_key)
            ->setResetExpire(new \DateTime($expire));
        $this->entityManager->flush();

        $Customer = $this->customerRepo->getRegularCustomerByResetKey($reset_key);
        $this->assertNull($Customer);
    }

    public function testGetQueryBuilderBySearchDataByMulti2147483648()
    {
        $Customer = $this->createCustomer('2147483648@example.com');
        $actual = $this->customerRepo->getQueryBuilderBySearchData(['multi' => '2147483648'])
            ->getQuery()
            ->getResult();

        self::assertEquals($Customer, $actual[0]);
    }
}

class DummyCustomer implements UserInterface
{
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {
        return 'password';
    }

    public function getSalt()
    {
        return 'salt';
    }

    public function getUsername()
    {
        return 'user';
    }

    public function eraseCredentials()
    {
        return;
    }
}
