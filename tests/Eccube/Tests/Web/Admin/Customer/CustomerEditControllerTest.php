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

namespace Eccube\Tests\Web\Admin\Customer;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class CustomerEditControllerTest
 */
class CustomerEditControllerTest extends AbstractAdminWebTestCase
{
    /** @var Customer */
    protected $Customer;

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
    }

    /**
     * createFormData
     *
     * @return array
     */
    protected function createFormData()
    {
        $faker = $this->getFaker();
        $email = $faker->safeEmail;
        $password = $faker->lexify('????????');
        $birth = $faker->dateTimeBetween;

        $form = [
            'name' => ['name01' => $faker->lastName, 'name02' => $faker->firstName],
            'kana' => ['kana01' => $faker->lastKanaName, 'kana02' => $faker->firstKanaName],
            'company_name' => $faker->company,
            'postal_code' => $faker->postcode,
            'address' => ['pref' => '5', 'addr01' => $faker->city, 'addr02' => $faker->streetAddress],
            'phone_number' => $faker->phoneNumber,
            'email' => $email,
            'password' => ['first' => $password, 'second' => $password],
            'birth' => $birth->format('Y').'-'.$birth->format('n').'-'.$birth->format('j'),
            'sex' => 1,
            'job' => 1,
            'status' => 1,
            'point' => 0,
            '_token' => 'dummy',
        ];

        return $form;
    }

    /**
     * testIndex
     */
    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_customer_edit', ['id' => $this->Customer->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * testIndex
     */
    public function testIndexBackButton()
    {
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_customer_edit', ['id' => $this->Customer->getId()])
        );

        $this->expected = '会員一覧';
        $this->actual = $crawler->filter('#customer_form > div.c-conversionArea > div > div > div:nth-child(1) > div')->text();
        $this->assertContains($this->expected, $this->actual);
    }

    /**
     * testIndexWithPost
     */
    public function testIndexWithPost()
    {
        $form = $this->createFormData();
        $this->client->request(
            'POST',
            $this->generateUrl('admin_customer_edit', ['id' => $this->Customer->getId()]),
            ['admin_customer' => $form]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl(
                'admin_customer_edit',
                ['id' => $this->Customer->getId()]
            )
        ));
        $EditedCustomer = $this->container->get(CustomerRepository::class)->find($this->Customer->getId());

        $this->expected = $form['email'];
        $this->actual = $EditedCustomer->getEmail();
        $this->verify();
    }

    /**
     * testNew
     */
    public function testNew()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_customer_new')
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * testNewWithPost
     */
    public function testNewWithPost()
    {
        $form = $this->createFormData();
        $this->client->request(
            'POST',
            $this->generateUrl('admin_customer_new'),
            ['admin_customer' => $form]
        );

        $NewCustomer = $this->container->get(CustomerRepository::class)->findOneBy(['email' => $form['email']]);
        $this->assertNotNull($NewCustomer);
        $this->assertTrue($form['email'] == $NewCustomer->getEmail());
    }

    /**
     * testShowOrder
     */
    public function testShowOrder()
    {
        $id = $this->Customer->getId();

        //add Order pendding status for this customer
        $Order = $this->createOrder($this->Customer);
        $OrderStatus = $this->container->get(OrderStatusRepository::class)->find(OrderStatus::PAID);
        $Order->setOrderStatus($OrderStatus);
        $this->Customer->addOrder($Order);
        $this->entityManager->persist($this->Customer);
        $this->entityManager->flush();

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_customer_edit', ['id' => $id])
        );

        $orderListing = $crawler->filter('#orderHistory > div')->text();
        $this->assertRegexp('/'.$Order->getOrderNo().'/', $orderListing);
    }

    public function testNotShowProcessingOrder()
    {
        $id = $this->Customer->getId();

        //add Order pending status for this customer
        $Order = $this->createOrder($this->Customer);
        $OrderStatus = $this->container->get(OrderStatusRepository::class)->find(OrderStatus::PROCESSING);
        $Order->setOrderStatus($OrderStatus);
        $this->Customer->addOrder($Order);
        $this->entityManager->persist($Order);
        $this->entityManager->persist($this->Customer);
        $this->entityManager->flush();
        $this->entityManager->detach($this->Customer);
        $this->entityManager->detach($Order);
        unset($this->Customer);

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_customer_edit', ['id' => $id])
        );

        $orderListing = $crawler->filter('#orderHistory')->text();
        $this->assertContains('この会員の購入履歴がありません', $orderListing);
    }

    /**
     * testCustomerWithdraw
     */
    public function testCustomerWithdraw()
    {
        $form = $this->createFormData();
        $form['status'] = 3;
        $this->client->request(
            'POST',
            $this->generateUrl('admin_customer_edit', ['id' => $this->Customer->getId()]),
            ['admin_customer' => $form]
        );

        $EditedCustomer = $this->container->get(CustomerRepository::class)->find($this->Customer->getId());

        $this->assertRegExp('/@dummy.dummy/', $EditedCustomer->getEmail());
    }


    /**
     * testMailNoRFC
     */
    public function testMailNoRFC()
    {
        $form = $this->createFormData();
        // RFCに準拠していないメールアドレスを設定
        $form['email'] = 'aa..@example.com';

        $this->client->request(
            'POST',
            $this->generateUrl('admin_customer_edit', ['id' => $this->Customer->getId()]),
            ['admin_customer' => $form]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl(
                'admin_customer_edit',
                ['id' => $this->Customer->getId()]
            )
        ));
        $EditedCustomer = $this->container->get(CustomerRepository::class)->find($this->Customer->getId());

        $this->expected = $form['email'];
        $this->actual = $EditedCustomer->getEmail();
        $this->verify();
    }
}
