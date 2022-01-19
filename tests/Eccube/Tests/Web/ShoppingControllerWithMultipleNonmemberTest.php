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

use Eccube\Entity\Order;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\OrderRepository;

/**
 * 非会員複数配送指定のテストケース.
 *
 * @author Kentaro Ohkouchi
 */
class ShoppingControllerWithMultipleNonmemberTest extends AbstractShoppingControllerTestCase
{
    /** @var BaseInfoRepository */
    private $baseInfoRepository;

    /** @var OrderRepository */
    private $orderRepository;

    public function setUp()
    {
        parent::setUp();
        $this->baseInfoRepository = $this->entityManager->getRepository(\Eccube\Entity\BaseInfo::class);
        $this->orderRepository = $this->entityManager->getRepository(\Eccube\Entity\Order::class);
    }

    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    /**
     * 非会員情報入力→購入確認画面→複数配送設定→お届け先追加→複数配送設定→購入確認画面→完了画面
     */
    public function testCompleteWithNonmember()
    {
        $this->scenarioCartIn();
        $this->scenarioCartIn(); // 2個カート投入

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();
        $this->expected = 'ご注文手続き';
        $this->actual = $crawler->filter('div.ec-pageHeader h1')->text();
        $this->verify();

        // 複数配送画面
        $crawler = $this->client->request('GET', $this->generateUrl('shopping_shipping_multiple'));

        // お届け先情報入力画面
        $crawler = $this->client->request('GET', $this->generateUrl('shopping_shipping_multiple_edit'));

        $form = $this->createShippingFormData();

        // お届け先追加
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple_edit'),
            ['shopping_shipping' => $form]
        );

        $crawler = $this->client->request('GET', $this->generateUrl('shopping_shipping_multiple'));

        // 配送先1, 配送先2の情報を返す
        $shippings = $crawler->filter('#form_shipping_multiple_0_shipping_0_customer_address > option')->each(
            function ($node, $i) {
                return [
                    'customer_address' => $node->attr('value'),
                    'quantity' => 1,
                ];
            }
        );

        // 複数配送設定
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => [
                      'shipping_multiple' => [0 => [
                                // 配送先1, 配送先2 の 情報を渡す
                                'shipping' => $shippings,
                            ],
                      ],
                      '_token' => 'dummy',
                  ],
            ]
        );

        // 確認画面
        $crawler = $this->scenarioComplete(
            null,
            $this->generateUrl('shopping_confirm'),
            [
                // 配送先1
                [
                    'Delivery' => 1,
                    'DeliveryTime' => 1,
                ],
                // 配送先2
                [
                    'Delivery' => 1,
                    'DeliveryTime' => 1,
                ],
            ]
        );

        $this->scenarioCheckout();

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_complete')));

        $BaseInfo = $this->baseInfoRepository->get();
        $Messages = $this->getMailCollector(false)->getMessages();
        $Message = $Messages[0];

        $this->expected = '['.$BaseInfo->getShopName().'] ご注文ありがとうございます';
        $this->actual = $Message->getSubject();
        $this->verify();

        $body = $Message->getBody();
        $this->assertRegexp('/◎お届け先2/u', $body, '複数配送のため, お届け先2が存在する');
    }

    public function createNonmemberFormData()
    {
        $faker = $this->getFaker();
        $email = $faker->safeEmail;
        $form = parent::createShippingFormData();
        $form['email'] = [
            'first' => $email,
            'second' => $email,
        ];

        return $form;
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithOneAddressOneItem()
    {
        $this->scenarioCartIn();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        $Order = $this->getLastOrder();

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = 1;
        $this->verify();
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithOneAddressOneItemTwoQuantities()
    {
        $this->scenarioCartIn(null, 2);
        $this->scenarioCartIn(null, 2);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        $Order = $Order = $this->getLastOrder();

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = 1;
        $this->verify();
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithOneAddressTwoItemsTwoQuantities()
    {
        // Product test 1 with type 1
        $Product1 = $this->createProduct();
        $ProductClass1 = $Product1->getProductClasses()->first();
        $ProductClass1->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        $this->entityManager->persist($ProductClass1);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->flush();

        // Item of product 1
        $this->scenarioCartIn(null, $ProductClass1->getId());

        // Item of product 2
        $this->scenarioCartIn(null, $ProductClass2->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        // process order id
        $Order = $this->getLastOrder();

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = 1;
        $this->verify();
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithTwoAddressesTwoItemsThreeQuantities()
    {
        // Product test 1 with type 1
        $Product1 = $this->createProduct();
        $ProductClass1 = $Product1->getProductClasses()->first();
        $ProductClass1->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        $this->entityManager->persist($ProductClass1);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->flush();

        // Item of product 1
        $this->scenarioCartIn(null, $ProductClass1->getId());
        $this->scenarioCartIn(null, $ProductClass1->getId());

        // Item of product 2
        $this->scenarioCartIn(null, $ProductClass2->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        unset($formData['email']);

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple_edit'),
            ['shopping_shipping' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_shipping_multiple')));
        ++$addressNumber;

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                    ],
                ],
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        $Order = $this->getLastOrder();

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = $addressNumber;
        $this->verify();
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithTwoAddressesTwoItemsEachTwoQuantities()
    {
        // Product test 1 with type 1
        $Product1 = $this->createProduct();
        $ProductClass1 = $Product1->getProductClasses()->first();
        $ProductClass1->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        $this->entityManager->persist($ProductClass1);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->flush();

        // Item of product 1
        $this->scenarioCartIn(null, $ProductClass1->getId());
        $this->scenarioCartIn(null, $ProductClass1->getId());

        // Item of product 2
        $this->scenarioCartIn(null, $ProductClass2->getId());
        $this->scenarioCartIn(null, $ProductClass2->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        // お届け先設定画面への遷移前チェック
        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        unset($formData['email']);

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple_edit'),
            ['shopping_shipping' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_shipping_multiple')));
        ++$addressNumber;

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                    ],
                ],
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        $Order = $this->getLastOrder();

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = $addressNumber;
        $this->verify();
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithOneAddressThreeItems()
    {
        // Product test 1 with type 1
        $Product1 = $this->createProduct();
        $ProductClass = $Product1->getProductClasses()->first();
        $ProductClass->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        // Product test 3
        $Product3 = $this->createProduct();
        $ProductClass3 = $Product3->getProductClasses()->first();
        $ProductClass3->setStock(111);

        $this->entityManager->persist($ProductClass);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->persist($ProductClass3);
        $this->entityManager->flush();

        // Item of product 1
        $this->scenarioCartIn(null, $ProductClass->getId());
        $this->scenarioCartIn(null, $ProductClass->getId());

        // Item of product 2
        $this->scenarioCartIn(null, $ProductClass2->getId());
        $this->scenarioCartIn(null, $ProductClass2->getId());

        // Item of product 3
        $this->scenarioCartIn(null, $ProductClass3->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        // お届け先設定画面への遷移前チェック
        $addressNumber = 1;

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        // process order id
        $Order = $this->getLastOrder();

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = $addressNumber;
        $this->verify();
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithTwoAddressesThreeItems()
    {
        // Product test 1 with type 1
        $Product1 = $this->createProduct();
        $ProductClass1 = $Product1->getProductClasses()->first();
        $ProductClass1->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        // Product test 3
        $Product3 = $this->createProduct();
        $ProductClass3 = $Product3->getProductClasses()->first();
        $ProductClass3->setStock(111);

        $this->entityManager->persist($ProductClass1);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->persist($ProductClass3);
        $this->entityManager->flush();

        // Item of product 1
        $this->scenarioCartIn(null, $ProductClass1->getId());
        $this->scenarioCartIn(null, $ProductClass1->getId());

        // Item of product 2
        $this->scenarioCartIn(null, $ProductClass2->getId());
        $this->scenarioCartIn(null, $ProductClass2->getId());

        // Item of product 3
        $this->scenarioCartIn(null, $ProductClass3->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        unset($formData['email']);

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple_edit'),
            ['shopping_shipping' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_shipping_multiple')));
        ++$addressNumber;

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                    ],
                ],
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                    ],
                ],
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        // process order id
        $Order = $this->getLastOrder();

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = $addressNumber;
        $this->verify();
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithThreeAddressesThreeItems()
    {
        // Product test 1 with type 1
        $Product1 = $this->createProduct();
        $ProductClass1 = $Product1->getProductClasses()->first();
        $ProductClass1->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        // Product test 3
        $Product3 = $this->createProduct();
        $ProductClass3 = $Product3->getProductClasses()->first();
        $ProductClass3->setStock(111);

        $this->entityManager->persist($ProductClass1);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->persist($ProductClass3);
        $this->entityManager->flush();

        // Item of product 1
        $this->scenarioCartIn(null, $ProductClass1->getId());
        $this->scenarioCartIn(null, $ProductClass1->getId());

        // Item of product 2
        $this->scenarioCartIn(null, $ProductClass2->getId());
        $this->scenarioCartIn(null, $ProductClass2->getId());

        // Item of product 3
        $this->scenarioCartIn(null, $ProductClass3->getId());
        $this->scenarioCartIn(null, $ProductClass3->getId());
        $this->scenarioCartIn(null, $ProductClass3->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        unset($formData['email']);

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple_edit'),
            ['shopping_shipping' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_shipping_multiple')));
        ++$addressNumber;

        // Address 3
        $formData = $this->createNonmemberFormData();
        unset($formData['email']);

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple_edit'),
            ['shopping_shipping' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_shipping_multiple')));
        ++$addressNumber;

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                    ],
                ],
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                    ],
                ],
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 2,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        // process order id
        $Order = $this->getLastOrder();

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = $addressNumber;
        $this->verify();
    }

    /**
     * Test add multi shipping
     */
    public function testAddMultiShippingCartUnlock()
    {
        $this->markTestIncomplete('カートのアンロック対応');

        $this->client->request('POST', '/cart/add', ['product_class_id' => 10, 'quantity' => 2]);
        $this->client->request('POST', '/cart/add', ['product_class_id' => 1, 'quantity' => 1]);
        $this->client->request('POST', '/cart/add', ['product_class_id' => 2, 'quantity' => 1]);

        $this->scenarioCartIn();

        // unlock cart
        $this->app['eccube.service.cart']->unlock();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('cart')));
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithoutCart()
    {
        $this->markTestIncomplete('カートのクリア対応');

        $this->scenarioCartIn();
        $this->scenarioCartIn();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        // clear cart
        $cartService = $this->app['eccube.service.cart'];
        $cartService->clear();

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('cart')));
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingShippingUnlock()
    {
        $this->markTestIncomplete('カートのアンロック対応');

        $client = $this->client;

        $client->request('POST', '/cart/add', ['product_class_id' => 1, 'quantity' => 1]);
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        // unlock when shipping
        $this->app['eccube.service.cart']->unlock();

        $client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('cart')));
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithQuantityNotEqual()
    {
        $this->scenarioCartIn();
        $this->scenarioCartIn();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 3, // not equal
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        $crawler = $this->client->request('GET', $this->generateUrl('shopping'));
        $shipping = $crawler->filter('#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div.ec-orderDelivery__item > ul')->last()->text();
        $this->assertContains('× 3', $shipping);
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithShippingEarlier()
    {
        $this->scenarioCartIn();
        $this->scenarioCartIn();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        unset($formData['email']);

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple_edit'),
            ['shopping_shipping' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_shipping_multiple')));
        ++$addressNumber;

        // before shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 2,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        // after shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        $Order = $this->getLastOrder();

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = $addressNumber;
        $this->verify();
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithThreeAddressesThreeItemsOnScreen()
    {
        // Product test 1 with type 1
        $Product1 = $this->createProduct();
        $ProductClass1 = $Product1->getProductClasses()->first();
        $ProductClass1->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        // Product test 3
        $Product3 = $this->createProduct();
        $ProductClass3 = $Product3->getProductClasses()->first();
        $ProductClass3->setStock(111);

        $this->entityManager->persist($ProductClass1);
        $this->entityManager->persist($ProductClass2);
        $this->entityManager->persist($ProductClass3);
        $this->entityManager->flush();

        // Item of product 1
        $this->scenarioCartIn(null, $ProductClass1->getId());
        $this->scenarioCartIn(null, $ProductClass1->getId());

        // Item of product 2
        $this->scenarioCartIn(null, $ProductClass2->getId());
        $this->scenarioCartIn(null, $ProductClass2->getId());

        // Item of product 3
        $this->scenarioCartIn(null, $ProductClass3->getId());
        $this->scenarioCartIn(null, $ProductClass3->getId());
        $this->scenarioCartIn(null, $ProductClass3->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        unset($formData['email']);

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple_edit'),
            ['shopping_shipping' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_shipping_multiple')));
        ++$addressNumber;

        // Address 3
        $formData = $this->createNonmemberFormData();
        unset($formData['email']);

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple_edit'),
            ['shopping_shipping' => $formData]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_shipping_multiple')));
        ++$addressNumber;

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                    ],
                ],
                [
                    'shipping' => [
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
                [
                    'shipping' => [
                        [
                            'customer_address' => 2,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 1,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        // process order id
        $Order = $this->getLastOrder();

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = $addressNumber;
        $this->verify();

        $crawler = $this->scenarioConfirm();

        // shipping number on the screen
        $lastShipping = $crawler->filter('#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery div.ec-orderDelivery__title')->last()->text();
        $this->assertContains("(${addressNumber})", $lastShipping);
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingExceedNAddress()
    {
        // Max address need to test
        $maxAddress = 25;

        $this->client->request('POST', $this->generateUrl('product_add_cart', ['id' => '1']), [
            'ProductClass' => '1',
            'quantity' => $maxAddress,
            '_token' => 'dummy',
        ]);
        $this->scenarioCartIn();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $crawler = $this->scenarioConfirm();

        // Address
        $formData = $this->createNonmemberFormData();
        unset($formData['email']);

        for ($i = 0; $i < $maxAddress; $i++) {
            $formData['address']['addr02'] = "addr02_${i}";
            $crawler = $this->client->request(
                'POST',
                $this->generateUrl('shopping_shipping_multiple_edit'),
                ['shopping_shipping' => $formData]
            );
        }

        $crawler = $this->client->request('GET', $this->generateUrl('shopping_shipping_multiple'));

        $shipping = $crawler->filter('#form_shipping_multiple_0_shipping_0_customer_address > option')->each(
            function ($node, $i) {
                return [
                    'customer_address' => $node->attr('value'),
                    'quantity' => 1,
                ];
            }
        );

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => $shipping,
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        $Order = $this->getLastOrder();
        $this->entityManager->refresh($Order);

        ++$maxAddress;
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);
        $this->expected = $maxAddress;
        $this->verify();

        $crawler = $this->scenarioConfirm();

        // shipping number on the screen
        $lastShipping = $crawler->filter('#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery div.ec-orderDelivery__title')->last()->text();
        $this->assertContains((string) $maxAddress, $lastShipping);
    }

    /**
     * Test multi shipping with nonmember
     * Give:
     * - Product A x 3
     * - Address x 1
     *
     * When:
     * - Shipment item:
     *  + Product A x1
     *  + Product A x1
     *  + Product A x1
     * - Delivery: 1 (for product type 1)
     *
     * Then:
     * - Number of Shipping: 1
     * - Shipment item: Product A x 3
     * - Delivery 1: サンプル業者
     * - Mail content: ◎お届け先
     */
    public function testAddMultiShippingThreeItemsOfOneProduct()
    {
        $Product = $this->createProduct();
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStock(111);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush();

        // Three items of product
        $this->scenarioCartIn(null, $ProductClass->getId());
        $this->scenarioCartIn(null, $ProductClass->getId());
        $this->scenarioCartIn(null, $ProductClass->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($formData);

        $this->scenarioConfirm();

        // add multi shipping
        $multiForm = [
            '_token' => 'dummy',
            'shipping_multiple' => [
                [
                    'shipping' => [
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                        [
                            'customer_address' => 0,
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('shopping_shipping_multiple'),
            ['form' => $multiForm]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        $Order = $this->getLastOrder();

        // one shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);
        $this->expected = 1;
        $this->verify();

        // 確認画面
        $crawler = $this->scenarioConfirm();

        // item number on the screen
        $shipping = $crawler->filter('#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div.ec-orderDelivery__item > ul')->text();
        $this->assertContains('× 3', $shipping);

        $deliver = $crawler->filter('#shopping_order_Shippings_0_Delivery > option')->each(
            function ($node, $i) {
                return $node->text();
            }
        );

        $this->expected = 'サンプル業者';
        $this->actual = $deliver;
        $this->assertTrue(in_array($this->expected, $this->actual));

        // 完了画面
        $this->scenarioComplete(
            null,
            $this->generateUrl('shopping_confirm'),
            [
                // 配送先1
                [
                    'Delivery' => 1,
                    'DeliveryTime' => 1,
                ],
            ]
        );

        $this->scenarioCheckout();

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_complete')));

        $BaseInfo = $this->baseInfoRepository->get();
        $Messages = $this->getMailCollector(false)->getMessages();
        $Message = $Messages[0];

        $this->expected = '['.$BaseInfo->getShopName().'] ご注文ありがとうございます';
        $this->actual = $Message->getSubject();
        $this->verify();

        $body = $Message->getBody();
        $this->assertRegexp('/◎お届け先/u', $body, '複数配送のため, お届け先1が存在する');
    }

    /**
     * @return Order
     */
    private function getLastOrder()
    {
        return $this->orderRepository->findOneBy([], ['id' => 'desc']);
    }
}
