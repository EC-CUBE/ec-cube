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

use Eccube\Repository\CustomerAddressRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class CustomerEditControllerTest
 */
class CustomerDeliveryEditControllerTest extends AbstractAdminWebTestCase
{
    protected $Customer;

    /**
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepo;

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->customerAddressRepo = $this->container->get(CustomerAddressRepository::class);
    }

    /**
     * createFormData
     *
     * @return array
     */
    protected function deliveryFormData()
    {
        $faker = $this->getFaker();
        $form = [
            'name' => ['name01' => $faker->lastName, 'name02' => $faker->firstName],
            'kana' => ['kana01' => $faker->lastKanaName, 'kana02' => $faker->firstKanaName],
            'company_name' => $faker->company,
            'postal_code' => $faker->postcode,
            'address' => ['pref' => '5', 'addr01' => $faker->city, 'addr02' => $faker->streetAddress],
            'phone_number' => $faker->phoneNumber,
            '_token' => 'dummy',
        ];

        return $form;
    }

    /**
     * testDeliveryNew
     */
    public function testRoutingDelivery()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_customer_delivery_new', ['id' => $this->Customer->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * testDeliveryNew
     */
    public function testDeliveryNew()
    {
        $form = $this->deliveryFormData();
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_customer_delivery_new', ['id' => $this->Customer->getId()]),
            ['customer_address' => $form]
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));
    }
}
