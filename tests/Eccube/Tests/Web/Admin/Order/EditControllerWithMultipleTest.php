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

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;

/**
 * 複数配送設定用 EditController のテストケース.
 *
 * @author Kentaro Ohkouchi
 */
class EditControllerWithMultipleTest extends AbstractEditControllerTestCase
{
    protected $Customer;
    protected $Order;
    protected $Product;

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Product = $this->createProduct();

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $this->app['orm.em']->flush($BaseInfo);
    }

    public function testRoutingAdminOrderNew()
    {
        $this->markTestIncomplete('新しい配送管理の実装が完了するまでスキップ');

        $this->client->request('GET', $this->app->url('admin_order_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminOrderNewPost()
    {
        $this->markTestIncomplete('新しい配送管理の実装が完了するまでスキップ');

        $Shippings = [];
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_new'),
            [
                'order' => $this->createFormDataForMultiple($this->Customer, $Shippings),
                'mode' => 'register',
            ]
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));
    }

    public function testRoutingAdminOrderEdit()
    {
        $this->markTestIncomplete('新しい配送管理の実装が完了するまでスキップ');

        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $crawler = $this->client->request('GET', $this->app->url('admin_order_edit', ['id' => $Order->getId()]));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminOrderEditPost()
    {
        $this->markTestIncomplete('新しい配送管理の実装が完了するまでスキップ');

        $Shippings = [];
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormDataForMultiple($Customer, $Shippings);
        $this->client->request(
            'POST',
            $this->app->url('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());
        $this->expected = $formData['name']['name01'];
        $this->actual = $EditedOrder->getName01();
        $this->verify();
    }

    public function testSearchCustomer()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_search_customer'),
            [
                'search_word' => $this->Customer->getId(),
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $Result = json_decode($this->client->getResponse()->getContent(), true);

        $this->expected = $this->Customer->getName01().$this->Customer->getName02().'('.$this->Customer->getKana01().$this->Customer->getKana02().')';
        $this->actual = $Result[0]['name'];
        $this->verify();
    }

    public function testSearchCustomerHtml()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_search_customer'),
            [
                'search_word' => $this->Customer->getId(),
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testSearchCustomerById()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_search_customer_by_id'),
            [
                'id' => $this->Customer->getId(),
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $Result = json_decode($this->client->getResponse()->getContent(), true);

        $this->expected = $this->Customer->getName01();
        $this->actual = $Result['name01'];
        $this->verify();
    }

    public function testSearchProduct()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_search_product'),
            [
                'id' => $this->Product->getId(),
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 管理画面から購入処理中で受注登録し, フロントを参照するテスト
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1452
     */
    public function testOrderProcessingToFrontConfirm()
    {
        $this->markTestIncomplete('新しい配送管理の実装が完了するまでスキップ');

        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);

        $Shippings = [];
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());

        $formData = $this->createFormDataForMultiple($Customer, $Shippings);
        $formData['OrderStatus'] = OrderStatus::PROCESSING; // 購入処理中で受注を登録する
        // 管理画面から受注登録
        $this->client->request(
            'POST',
            $this->app->url('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());
        $this->expected = $formData['OrderStatus'];
        $this->actual = $EditedOrder->getOrderStatus()->getId();
        $this->verify();

        // フロント側から, product_class_id = 1 をカート投入
        $client = $this->createClient();
        $crawler = $client->request('POST', '/cart/add', ['product_class_id' => 1]);
        $this->app['eccube.service.cart']->lock();

        $faker = $this->getFaker();
        $email = $faker->safeEmail;

        $clientFormData = [
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
            'email' => [
                'first' => $email,
                'second' => $email,
            ],
            '_token' => 'dummy',
        ];

        $client->request(
            'POST',
            $this->app->path('shopping_nonmember'),
            ['nonmember' => $clientFormData]
        );
        $this->app['eccube.service.cart']->lock();

        $crawler = $client->request('GET', $this->app->path('shopping'));
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'ディナーフォーク';
        $this->actual = $crawler->filter('dt.item_name')->last()->text();

        $OrderItems = $EditedOrder->getOrderItems();
        foreach ($OrderItems as $OrderItem) {
            if (is_object($OrderItem->getProduct())
                && $this->actual == $OrderItem->getProduct()->getName()) {
                $this->fail('#1452 の不具合');
            }
        }

        $this->verify('カートに投入した商品が表示される');
    }

    /**
     * 受注編集時に、dtb_order.taxの値が正しく保存されているかどうかのテスト
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1606
     */
    public function testOrderProcessingWithTax()
    {
        $this->markTestIncomplete('新しい配送管理の実装が完了するまでスキップ');

        $Shippings = [];
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());

        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormDataForMultiple($Customer, $Shippings);
        // 管理画面から受注登録
        $this->client->request(
            'POST', $this->app->url('admin_order_edit', ['id' => $Order->getId()]), [
            'order' => $formData,
            'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());

        $formDataForEdit = $this->createFormDataForEdit($EditedOrder);

        //税金計算
        $totalTax = 0;
        $addQuantity = 2;
        foreach ($formDataForEdit['OrderItems'] as $indx => $orderItem) {
            //商品数追加
            $formDataForEdit['OrderItems'][$indx]['quantity'] = $orderItem['quantity'] + $addQuantity * count($Shippings);
            $tax = (int) $this->app['eccube.service.tax_rule']->calcTax($orderItem['price'], $orderItem['tax_rate'], $orderItem['tax_rule']);
            $totalTax += $tax * $formDataForEdit['OrderItems'][$indx]['quantity'];
        }

        foreach ($formDataForEdit['Shippings'] as &$shipping) {
            foreach ($shipping['orderItems'] as &$orderItem) {
                $orderItem['quantity'] += $addQuantity;
            }
        }

        // 管理画面で受注編集する
        $this->client->request(
            'POST', $this->app->url('admin_order_edit', ['id' => $Order->getId()]), [
            'order' => $formDataForEdit,
            'mode' => 'register',
            ]
        );
        $EditedOrderafterEdit = $this->app['eccube.repository.order']->find($Order->getId());

        //確認する「トータル税金」
        $this->expected = $totalTax;
        $this->actual = $EditedOrderafterEdit->getTax();
        $this->verify();
    }

    public function testOrderEditWithShippingItemDelete()
    {
        $this->markTestIncomplete('新しい配送管理の実装が完了するまでスキップ');

        $Shippings = [];
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormDataForMultiple($Customer, $Shippings);
        $Shippings = $Order->getShippings();

        $this->client->request(
            'POST', $this->app->url('admin_order_edit', ['id' => $Order->getId()]), [
            'order' => $formData,
            'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());
        $this->expected = $formData['name']['name01'];
        $this->actual = $EditedOrder->getName01();
        $this->verify();
        $newFormData = parent::createFormDataForEdit($EditedOrder);

        $productClassExpected = [];
        foreach ($newFormData['Shippings'] as $idx => $Shippings) {
            $stopFlag = true;
            foreach ($Shippings['OrderItems'] as $subIsx => $OrderItem) {
                if ($stopFlag === true) {
                    $stopFlag = false;
                    $newFormData['Shippings'][$idx]['OrderItems'][$subIsx]['quantity'] = 0;
                    continue;
                }
                $productClassExpected[$idx][$OrderItem['ProductClass']] = $OrderItem['ProductClass'];
            }
        }
        $this->client->request(
            'POST', $this->app->url('admin_order_edit', ['id' => $Order->getId()]), [
            'order' => $newFormData,
            'mode' => 'register',
            ]
        );

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());
        $newFormData = parent::createFormDataForEdit($EditedOrder);

        $productClassActual = [];
        foreach ($newFormData['Shippings'] as $idx => $Shippings) {
            foreach ($Shippings['OrderItems'] as $subIsx => $OrderItem) {
                $productClassActual[$idx][$OrderItem['ProductClass']] = $OrderItem['ProductClass'];
            }
        }

        $this->expected = $productClassExpected;
        $this->actual = $productClassActual;
        $this->verify();
    }

    public function testOrderEditWithShippingDelete()
    {
        $this->markTestIncomplete('新しい配送管理の実装が完了するまでスキップ');

        $Shippings = [];
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());
        $Shippings[] = $this->createShipping($this->Product->getProductClasses()->toArray());
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormDataForMultiple($Customer, $Shippings);
        $Shippings = $Order->getShippings();

        $this->client->request(
            'POST', $this->app->url('admin_order_edit', ['id' => $Order->getId()]), [
            'order' => $formData,
            'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());
        $this->expected = $formData['name']['name01'];
        $this->actual = $EditedOrder->getName01();
        $this->verify();
        $newFormData = parent::createFormDataForEdit($EditedOrder);

        $productClassExpected = [];
        $stopFlag = true;
        foreach ($newFormData['Shippings'] as $idx => $Shippings) {
            if ($stopFlag === true) {
                $stopFlag = false;
                unset($newFormData['Shippings'][$idx]);
                continue;
            }
            $productClassExpected[] = $Shippings;
        }
        $this->client->request(
            'POST', $this->app->url('admin_order_edit', ['id' => $Order->getId()]), [
            'order' => $newFormData,
            'mode' => 'register',
            ]
        );

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());
        $newFormData = parent::createFormDataForEdit($EditedOrder);

        $productClassActual = [];
        foreach ($newFormData['Shippings'] as $idx => $Shippings) {
            $productClassActual[] = $Shippings;
        }

        $this->expected = $productClassExpected;
        $this->actual = $productClassActual;
        $this->verify();
    }

    /**
     * 複数配送用受注編集用フォーム作成.
     *
     * createFormData() との違いは、 $Shipping[N]['OrderItems'] がフォームに追加されている.
     * OrderItems は、 $Shippings[N]['OrderItems] から生成される.
     *
     * @param Customer $Customer
     * @param array $Shippings お届け先情報の配列
     *
     * @return array
     */
    public function createFormDataForMultiple(Customer $Customer, array $Shippings)
    {
        $formData = parent::createFormData($Customer, null);
        $formData['Shippings'] = $Shippings;
        $OrderItems = [];
        foreach ($Shippings as $Shipping) {
            foreach ($Shipping['OrderItems'] as $Item) {
                if (empty($OrderItems[$Item['ProductClass']])) {
                    $OrderItems[$Item['ProductClass']] = [
                        'Product' => $Item['Product'],
                        'ProductClass' => $Item['ProductClass'],
                        'price' => $Item['price'],
                        'quantity' => $Item['quantity'],
                        'tax_rate' => 8, // XXX ハードコーディング
                        'tax_rule' => 1,
                        'product_name' => $Item['product_name'],
                        'product_code' => $Item['product_code'],
                    ];
                } else {
                    $OrderItems[$Item['ProductClass']]['quantity'] += $Item['quantity'];
                }
            }
        }
        $formData['OrderItems'] = array_values($OrderItems);

        return $formData;
    }

    /**
     * 複数配送用受注編集用フォーム作成.
     *
     * 引数に渡した商品規格のお届け商品明細が生成される.
     *
     * @param array $ProductClasses 商品規格の配列.
     *
     * @return array
     */
    public function createShipping(array $ProductClasses)
    {
        $faker = $this->getFaker();
        $delivery_date = $faker->dateTimeBetween('now', '+ 5 days');

        $ShippingItems = [];
        foreach ($ProductClasses as $ProductClass) {
            $ShippingItems[] = [
                'Product' => $ProductClass->getProduct()->getId(),
                'ProductClass' => $ProductClass->getId(),
                'price' => $ProductClass->getPrice02(),
                'quantity' => $faker->numberBetween(1, 9),
                'product_name' => $ProductClass->getProduct()->getName(),
                'product_code' => $ProductClass->getCode(),
            ];
        }
        $Shipping =
            [
                'OrderItems' => $ShippingItems,
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
                    'pref' => $faker->numberBetween(1, 47),
                    'addr01' => $faker->city,
                    'addr02' => $faker->streetAddress,
                ],
                'phone_number' => $faker->phoneNumber,
                'Delivery' => '1',
                'DeliveryTime' => '1',
                'shipping_delivery_date' => [
                    'year' => $delivery_date->format('Y'),
                    'month' => $delivery_date->format('n'),
                    'day' => $delivery_date->format('j'),
                ],
            ];

        return $Shipping;
    }
}
