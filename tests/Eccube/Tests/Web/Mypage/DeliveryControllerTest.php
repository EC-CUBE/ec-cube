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

namespace Eccube\Tests\Web\Mypage;

use Eccube\Entity\Customer;
use Eccube\Tests\Web\AbstractWebTestCase;

class DeliveryControllerTest extends AbstractWebTestCase
{
    /**
     * @var Customer
     */
    protected $Customer;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->createCustomerAddress($this->Customer);
    }

    protected function createFormData()
    {
        $faker = $this->getFaker();
        $email = $faker->safeEmail;
        $password = $faker->lexify('????????');

        $form = [
            'name' => [
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ],
            'kana' => [
                'kana01' => $faker->lastKanaName,
                'kana02' => $faker->firstKanaName,
            ],
            'company_name' => $faker->company,
            'postal_code' => $faker->postcode,
            'address' => [
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ],
            'phone_number' => $faker->phoneNumber,
            '_token' => 'dummy',
        ];

        return $form;
    }

    public function testIndex()
    {
        $this->logInTo($this->Customer);
        $client = $this->client;

        $client->request(
            'GET',
            $this->generateUrl('mypage_delivery')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testNew()
    {
        $this->logInTo($this->Customer);
        $client = $this->client;

        $client->request(
            'GET',
            $this->generateUrl('mypage_delivery_new')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testNewWithPost()
    {
        $this->logInTo($this->Customer);
        $client = $this->client;

        $form = $this->createFormData();
        $crawler = $client->request(
            'POST',
            $this->generateUrl('mypage_delivery_new'),
            ['customer_address' => $form]
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('mypage_delivery')));
    }

    public function testEdit()
    {
        $this->logInTo($this->Customer);
        $client = $this->client;

        $CustomerAddress = $this->entityManager->getRepository(\Eccube\Entity\CustomerAddress::class)->findOneBy(
            ['Customer' => $this->Customer]
        );

        $crawler = $client->request(
            'GET',
            $this->generateUrl('mypage_delivery_edit', ['id' => $CustomerAddress->getId()])
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testEditWithPost()
    {
        $this->logInTo($this->Customer);

        $CustomerAddress = $this->entityManager->getRepository(\Eccube\Entity\CustomerAddress::class)->findOneBy(
            ['Customer' => $this->Customer]
        );

        $form = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('mypage_delivery_edit', ['id' => $CustomerAddress->getId()]),
            ['customer_address' => $form]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('mypage_delivery')));

        $this->expected = $form['name']['name01'];
        $this->actual = $CustomerAddress->getName01();
        $this->verify();
    }

    public function testDelete()
    {
        $this->logInTo($this->Customer);

        $CustomerAddress = $this->entityManager->getRepository(\Eccube\Entity\CustomerAddress::class)->findOneBy(
            ['Customer' => $this->Customer]
        );
        $id = $CustomerAddress->getId();

        $form = $this->createFormData();
        $crawler = $this->client->request(
            'DELETE',
            $this->generateUrl('mypage_delivery_delete', ['id' => $id])
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('mypage_delivery')));

        $CustomerAddress = $this->entityManager->getRepository(\Eccube\Entity\CustomerAddress::class)->find($id);
        $this->assertNull($CustomerAddress);
    }

    public function testDeleteWithFailure()
    {
        $this->logInTo($this->Customer);

        $crawler = $this->client->request(
            'DELETE',
            $this->generateUrl('mypage_delivery_delete', ['id' => 999999999])
        );

        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/pull/4127
     */
    public function testDeliveryCountOver()
    {
        $this->logInTo($this->Customer);

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('mypage_delivery')
        );

        // お届け先上限のエラーがないことを確認.
        $this->assertCount(0, $crawler->filter('span.ec-errorMessage'));

        // お届け先上限まで登録
        $max = self::$container->getParameter('eccube_deliv_addr_max');
        for ($i = 0; $i < $max; $i++) {
            $this->createCustomerAddress($this->Customer);
        }

        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('mypage_delivery')
        );

        // お届け先上限のエラーメッセージが表示されることを確認
        $errorMessage = $crawler->filter('span.ec-errorMessage');

        $this->assertCount(1, $errorMessage);
        $this->assertSame(
            sprintf('お届け先登録の上限の%s件に達しています。お届け先を入力したい場合は、削除か変更を行ってください。', $max),
            $errorMessage->text()
        );
    }
}
