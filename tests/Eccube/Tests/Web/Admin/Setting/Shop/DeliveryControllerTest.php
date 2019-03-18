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

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Entity\Delivery;
use Eccube\Entity\DeliveryFee;
use Eccube\Entity\Payment;
use Eccube\Entity\PaymentOption;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class DeliveryControllerTest
 */
class DeliveryControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @return mixed
     */
    public function createDelivery()
    {
        $faker = $this->getFaker();
        // create new delivery
        $Delivery = new Delivery();
        $Delivery->setConfirmUrl($faker->url);
        $Delivery->setVisible(true);
        $this->entityManager->persist($Delivery);
        $this->entityManager->flush();

        $Prefs = $this->container->get(PrefRepository::class)->findAll();

        foreach ($Prefs as $Pref) {
            $DeliveryFee = $this->container->get(DeliveryFeeRepository::class)
                ->findOneBy([
                    'Delivery' => $Delivery,
                    'Pref' => $Pref,
                ]);
            if (!$DeliveryFee) {
                $DeliveryFee = new DeliveryFee();
                $DeliveryFee->setDelivery($Delivery)
                    ->setPref($Pref);
            }
            $DeliveryFee->setFee($faker->randomNumber(3));

            $this->entityManager->persist($DeliveryFee);
            $this->entityManager->flush();

            $Delivery->addDeliveryFee($DeliveryFee);
        }

        $Payment = new Payment();
        $Payment->setVisible(true);
        $this->entityManager->persist($Payment);
        $this->entityManager->flush();
        $PaymentOption = new PaymentOption();
        $PaymentOption->setDelivery($Delivery);
        $PaymentOption->setPayment($Payment);
        $PaymentOption->setDeliveryId($Delivery->getId());
        $PaymentOption->setPaymentId($Payment->getId());
        $this->entityManager->persist($PaymentOption);
        $this->entityManager->flush();

        $Delivery->addPaymentOption($PaymentOption);
        $this->entityManager->persist($Delivery);
        $this->entityManager->flush();

        return $Delivery;
    }

    /**
     * test routing delivery
     */
    public function testRouting()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_shop_delivery'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Routing new test
     */
    public function testRoutingNew()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_shop_delivery_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Delivery new test
     *
     * @param bool $isSuccess
     * @param bool $expected
     * @dataProvider dataSubmitProvider
     */
    public function testNew($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['name'] = '';
        }

        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_delivery_new'),
            [
                'delivery' => $formData,
            ]
        );

        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();
    }

    /**
     * test routing edit
     */
    public function testRoutingEdit()
    {
        $Delivery = $this->createDelivery();
        $this->client->request('GET', $this->generateUrl('admin_setting_shop_delivery_edit', ['id' => $Delivery->getId()]));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Delivery edit test
     *
     * @param bool $isSuccess
     * @param bool $expected
     * @dataProvider dataSubmitProvider
     */
    public function testEdit($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['name'] = '';
        }

        $Delivery = $this->createDelivery();

        $this->client->request('POST',
            $this->generateUrl('admin_setting_shop_delivery_edit', ['id' => $Delivery->getId()]),
            [
                'delivery' => $formData,
            ]
        );

        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();
    }

    /**
     * test delete
     */
    public function testDeleteSuccess()
    {
        $Delivery = $this->createDelivery();
        $pid = $Delivery->getId();
        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_setting_shop_delivery_delete', ['id' => $pid])
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->actual = $this->entityManager->find(Delivery::class, $pid);
    }

    /**
     * test delete fail
     */
    public function testDeleteFail()
    {
        $pid = 9999;
        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_setting_shop_delivery_delete', ['id' => $pid])
        );
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testMoveSortNo()
    {
        $DeliveryOne = $this->createDelivery();
        $oldSortNo = $DeliveryOne->getSortNo();
        $DeliveryTwo = $this->createDelivery();
        $newSortNo = $DeliveryTwo->getSortNo();

        $request = [
            $DeliveryOne->getId() => $newSortNo,
            $DeliveryTwo->getId() => $oldSortNo,
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_delivery_sort_no_move'),
            $request,
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = $newSortNo;
        $this->actual = $DeliveryOne->getSortNo();
        $this->verify();
    }

    public function createFormData()
    {
        $faker = $this->getFaker();

        $deliveryFree = [];
        // 47 93 ?
        for ($i = 48; $i <= 93; $i++) {
            $tmpFee = $faker->randomNumber(5);
            if (mt_rand(0, 1)) {
                $tmpFee = number_format($tmpFee);
            }
            $deliveryFree[$i] = ['fee' => $tmpFee];
        }

        $i = 0;
        $form = [
            '_token' => 'dummy',
            'name' => $faker->word,
            'service_name' => $faker->word,
            'description' => $faker->word,
            'confirm_url' => $faker->url,
            'sale_type' => rand(1, 2),
            'payments' => ['1'],
            'visible' => 1,
            'delivery_times' => [
                ['delivery_time' => 'AM', 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => 'PM', 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
                ['delivery_time' => $faker->word, 'sort_no' => $i++, 'visible' => 1],
            ],
            'free_all' => $faker->randomNumber(5),
            'delivery_fees' => $deliveryFree,
        ];

        return $form;
    }

    public function dataSubmitProvider()
    {
        return [
            [false, false],
            [true, true],
            // To do implement
        ];
    }

    //    TO DO : implement
}
