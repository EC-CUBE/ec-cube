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

namespace Eccube\Tests\Web;

use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Util\StringUtil;

/**
 * ShoppingController 用 WebTest の抽象クラス.
 *
 * ShoppingController の WebTest をする場合に汎用的に使用する.
 *
 * @author Kentaro Ohkouchi
 */
abstract class AbstractShoppingControllerTestCase extends AbstractWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function createShippingFormData()
    {
        $faker = $this->getFaker();

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

    protected function scenarioCartIn(Customer $Customer = null, $product_class_id = 2)
    {
        if ($Customer) {
            $this->loginTo($Customer);
        }

        $this->client->request(
            'PUT',
            $this->generateUrl(
                'cart_handle_item',
                [
                    'operation' => 'up',
                    'productClassId' => $product_class_id,
                ]
            ),
            [Constant::TOKEN_NAME => '_dummy']
        );

        $ProductClass = $this->entityManager->getRepository(\Eccube\Entity\ProductClass::class)->find($product_class_id);
        if ($Customer) {
            $this->loginTo($Customer);
            $cart_key = $Customer->getId().'_'.$ProductClass->getSaleType()->getId();
        } else {
            $cart_key = StringUtil::random(32).'_'.$ProductClass->getSaleType()->getId();
        }

        return $this->client->request(
            'GET',
            $this->generateUrl('cart_buystep', ['cart_key' => $cart_key])
        );
    }

    protected function scenarioInput($formData)
    {
        $formData[Constant::TOKEN_NAME] = '_dummy';
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('shopping_nonmember'),
            ['nonmember' => $formData]
        );

        return $crawler;
    }

    protected function scenarioConfirm(Customer $Customer = null)
    {
        if ($Customer) {
            $this->loginTo($Customer);
        }
        $crawler = $this->client->request('GET', $this->generateUrl('shopping'));

        return $crawler;
    }

    protected function scenarioRedirectTo(Customer $Cusotmer, $parameters)
    {
        if ($Cusotmer) {
            $this->loginTo($Cusotmer);
        }

        return $this->client->request(
            'POST',
            $this->generateUrl('shopping_redirect_to'),
            $parameters
        );
    }

    protected function scenarioComplete(Customer $Customer = null, $confirm_url, array $shippings = [], $doComplete = false)
    {
        if ($Customer) {
            $this->loginTo($Customer);
        }

        $faker = $this->getFaker();
        if (count($shippings) < 1) {
            $shippings = [
                [
                    'Delivery' => 1,
                    'DeliveryTime' => 1,
                ],
            ];
        }

        $this->client->enableProfiler();

        if ($doComplete) {
            $parameters = [
                '_shopping_order' => [
                    '_token' => 'dummy',
                ],
            ];
        } else {
            $parameters = [
                '_shopping_order' => [
                    'Shippings' => $shippings,
                    'Payment' => 3,
                    'message' => $faker->realText(),
                    '_token' => 'dummy',
                ],
            ];
            if ($Customer) {
                $parameters['_shopping_order']['use_point'] = 0;
            }
        }

        $crawler = $this->client->request(
            'POST',
            $confirm_url,
            $parameters
        );

        return $crawler;
    }

    protected function scenarioCheckout(Customer $Customer = null)
    {
        if ($Customer) {
            $this->loginTo($Customer);
        }

        $this->client->enableProfiler();

        $parameters = [
            '_shopping_order' => [
                '_token' => 'dummy',
            ],
        ];

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('shopping_checkout'),
            $parameters
        );

        return $crawler;
    }
}
