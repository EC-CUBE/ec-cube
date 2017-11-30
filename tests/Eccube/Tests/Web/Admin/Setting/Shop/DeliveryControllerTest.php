<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Entity\Delivery;
use Eccube\Entity\PaymentOption;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DeliveryControllerTest
 * @package Eccube\Tests\Web\Admin\Setting\Shop
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
        $Delivery = $this->app['eccube.repository.delivery']->findOrCreate(0);
        $Delivery->setConfirmUrl($faker->url);
        $Delivery->setVisible(true);
        $this->app['orm.em']->persist($Delivery);
        $this->app['orm.em']->flush();

        $Prefs = $this->app['eccube.repository.master.pref']->findAll();

        foreach ($Prefs as $Pref) {
            $DeliveryFee = $this->app['eccube.repository.delivery_fee']
                ->findOrCreate(array(
                    'Delivery' => $Delivery,
                    'Pref' => $Pref,
                ));
            $DeliveryFee->setFee($faker->randomNumber(3));

            $this->app['orm.em']->persist($DeliveryFee);
            $this->app['orm.em']->flush();

            $Delivery->addDeliveryFee($DeliveryFee);
        }

        $Payment = $this->app['eccube.repository.payment']->findOrCreate(0);
        $this->app['orm.em']->persist($Payment);
        $this->app['orm.em']->flush();
        $PaymentOption = new PaymentOption();
        $PaymentOption->setDelivery($Delivery);
        $PaymentOption->setPayment($Payment);
        $PaymentOption->setDeliveryId($Delivery->getId());
        $PaymentOption->setPaymentId($Payment->getId());
        $this->app['orm.em']->persist($PaymentOption);
        $this->app['orm.em']->flush();

        $Delivery->addPaymentOption($PaymentOption);
        $this->app['orm.em']->persist($Delivery);
        $this->app['orm.em']->flush();

        return $Delivery;
    }

    /**
     * test routing delivery
     */
    public function testRouting()
    {
        $this->client->request('GET', $this->app->url('admin_setting_shop_delivery'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Routing new test
     */
    public function testRoutingNew()
    {
        $this->client->request('GET', $this->app->url('admin_setting_shop_delivery_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Delivery new test
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
            $this->app->url('admin_setting_shop_delivery_new'),
            array(
                'delivery' => $formData,
            )
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
        $this->client->request('GET', $this->app->url('admin_setting_shop_delivery_edit', array('id' => $Delivery->getId())));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Delivery edit test
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
            $this->app->url('admin_setting_shop_delivery_edit', array('id' => $Delivery->getId())),
            array(
                'delivery' => $formData
            )
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
            $this->app->url('admin_setting_shop_delivery_delete', array('id' => $pid))
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->actual = $this->app['orm.em']->find(Delivery::class, $pid);
    }

    /**
     * test delete fail
     */
    public function testDeleteFail()
    {
        $pid = 9999;
        try {
            $this->client->request(
                'DELETE',
                $this->app->url('admin_setting_shop_delivery_delete', array('id' => $pid))
            );
            $this->fail();
        } catch (NotFoundHttpException $e) {
        }
    }

    public function testMoveSortNo()
    {
        $DeliveryOne = $this->createDelivery();
        $oldSortNo = $DeliveryOne->getSortNo();
        $DeliveryTwo = $this->createDelivery();
        $newSortNo = $DeliveryTwo->getSortNo();

        $request = array(
            $DeliveryOne->getId() => $newSortNo,
            $DeliveryTwo->getId() => $oldSortNo
        );

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop_delivery_sort_no_move'),
            $request,
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = $newSortNo;
        $this->actual = $DeliveryOne->getSortNo();
        $this->verify();
    }

    public function createFormData()
    {
        $faker = $this->getFaker();

        $deliveryFree = array();
        // 47 93 ?
        for ($i = 48; $i <= 93; $i++) {
            $tmpFee = $faker->randomNumber(5);
            if (mt_rand(0, 1)) {
                $tmpFee = number_format($tmpFee);
            }
            $deliveryFree[$i] = array('fee' => $tmpFee);
        }

        $form = array(
            '_token' => 'dummy',
            'name' => $faker->word,
            'service_name' => $faker->word,
            'description' => $faker->word,
            'confirm_url' => $faker->url,
            'sale_type' => rand(1, 2),
            'payments' => array('1'),
            'delivery_times' => array(
                array('delivery_time' => 'AM'),
                array('delivery_time' => 'PM'),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => $faker->word),
                array('delivery_time' => null),
            ),
            'free_all' => $faker->randomNumber(5),
            'delivery_fees' => $deliveryFree
        );

        return $form;
    }

    public function dataSubmitProvider()
    {
        return array(
            array(false, false),
            array(true, true),
            // To do implement
        );
    }
    //    TO DO : implement
}
