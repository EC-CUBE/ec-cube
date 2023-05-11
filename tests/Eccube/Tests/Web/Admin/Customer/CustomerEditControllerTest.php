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

namespace Eccube\Tests\Web\Admin\Customer;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
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
    protected function setUp(): void
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
        $password = $faker->lexify('????????????').'a1';
        $birth = $faker->dateTimeBetween;

        $form = [
            'name' => ['name01' => $faker->lastName, 'name02' => $faker->firstName],
            'kana' => ['kana01' => $faker->lastKanaName, 'kana02' => $faker->firstKanaName],
            'company_name' => $faker->company,
            'postal_code' => $faker->postcode,
            'address' => ['pref' => '5', 'addr01' => $faker->city, 'addr02' => $faker->streetAddress],
            'phone_number' => $faker->phoneNumber,
            'email' => $email,
            'plain_password' => ['first' => $password, 'second' => $password],
            'birth' => $birth->format('Y').'-'.$birth->format('m').'-'.$birth->format('d'),
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
        $this->assertStringContainsString($this->expected, $this->actual);
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
        $EditedCustomer = $this->entityManager->getRepository(\Eccube\Entity\Customer::class)->find($this->Customer->getId());

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

        $NewCustomer = $this->entityManager->getRepository(\Eccube\Entity\Customer::class)->findOneBy(['email' => $form['email']]);
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
        $OrderStatus = $this->entityManager->getRepository(\Eccube\Entity\Master\OrderStatus::class)->find(OrderStatus::PAID);
        $Order->setOrderStatus($OrderStatus);
        $this->Customer->addOrder($Order);
        $this->entityManager->persist($this->Customer);
        $this->entityManager->flush();

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_customer_edit', ['id' => $id])
        );

        $orderListing = $crawler->filter('#orderHistory > div')->text();
        $this->assertMatchesRegularExpression('/'.$Order->getOrderNo().'/', $orderListing);
    }

    public function testNotShowProcessingOrder()
    {
        $id = $this->Customer->getId();

        //add Order pending status for this customer
        $Order = $this->createOrder($this->Customer);
        $OrderStatus = $this->entityManager->getRepository(\Eccube\Entity\Master\OrderStatus::class)->find(OrderStatus::PROCESSING);
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
        $this->assertStringContainsString('この会員の購入履歴がありません', $orderListing);
    }

    /**
     * testShowOrders
     */
    public function testShowOrders()
    {
        $id = $this->Customer->getId();

        //add Order paid status for this customer
        $Order = $this->createOrder($this->Customer);
        $OrderStatus = $this->entityManager->getRepository(\Eccube\Entity\Master\OrderStatus::class)->find(OrderStatus::PAID);
        $Order->setOrderStatus($OrderStatus);
        $this->Customer->addOrder($Order);
        $this->entityManager->persist($this->Customer);
        $this->entityManager->flush();

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_customer_edit', ['id' => $id])
        );

        // デフォルトの表示件数確認テスト
        $this->expected = '50件';
        $this->actual = $crawler->filter('#orderHistory select.form-select > option:selected')->text();
        $this->verify('デフォルトの表示件数確認テスト');

        // 表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_customer_edit', ['id' => $id, 'page_no' => 1, 'page_count' => 999999]));
        $this->expected = '50件';
        $this->actual = $crawler->filter('#orderHistory select.form-select > option:selected')->text();
        $this->verify('表示件数入力値は正しくない場合はデフォルトの表示件数になるテスト');

        // 表示件数70件テスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_customer_edit', ['id' => $id, 'page_no' => 1, 'page_count' => 70]));
        $this->expected = '70件';
        $this->actual = $crawler->filter('#orderHistory select.form-select > option:selected')->text();
        $this->verify('表示件数70件テスト');

        // 表示件数はSESSIONから取得するテスト
        $crawler = $this->client->request('GET', $this->generateUrl('admin_customer_edit', ['id' => $id, 'page_no' => 1, 'page_count' => 100]));
        $this->expected = '100件';
        $this->actual = $crawler->filter('#orderHistory select.form-select > option:selected')->text();
        $this->verify('表示件数はSESSIONから取得するテスト');
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

        $EditedCustomer = $this->entityManager->getRepository(\Eccube\Entity\Customer::class)->find($this->Customer->getId());

        $this->assertMatchesRegularExpression('/@dummy.dummy/', $EditedCustomer->getEmail());
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
        $EditedCustomer = $this->entityManager->getRepository(\Eccube\Entity\Customer::class)->find($this->Customer->getId());

        $this->expected = $form['email'];
        $this->actual = $EditedCustomer->getEmail();
        $this->verify();
    }
}
