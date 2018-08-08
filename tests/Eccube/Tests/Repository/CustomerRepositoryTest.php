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

use Eccube\Entity\Master\CustomerStatus;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Eccube\Entity\Customer;

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

        $this->customerRepo = $this->container->get(CustomerRepository::class);
        $this->masterOrderStatusRepo = $this->container->get(OrderStatusRepository::class);
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

    public function testUpdateBuyData()
    {
        $Order = $this->createOrder($this->Customer);

        $OrderStatus = $this->masterOrderStatusRepo->find(OrderStatus::NEW);

        $Order->setOrderStatus($OrderStatus);
        $this->entityManager->persist($Order);
        $this->entityManager->flush();

        $this->actual = 1;
        $this->customerRepo->updateBuyData($this->Customer, OrderStatus::NEW);
        $this->expected = $this->Customer->getBuyTimes();
        $this->verify();

        $OrderStatus = $this->masterOrderStatusRepo->find(OrderStatus::CANCEL);

        $Order->setOrderStatus($OrderStatus);
        $this->entityManager->persist($Order);
        $this->entityManager->flush();

        $this->actual = 0;
        $this->customerRepo->updateBuyData($this->Customer, OrderStatus::CANCEL);
        $this->expected = $this->Customer->getBuyTimes();
        $this->verify();
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
