<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
namespace Eccube\Tests\Web;

/**
 * 非会員複数配送指定のテストケース.
 *
 * @author Kentaro Ohkouchi
 */
class ShoppingControllerWithMultipleNonmemberTest extends AbstractShoppingControllerTestCase
{

    public function setUp()
    {
        parent::setUp();

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        // 複数配送を有効に
        $BaseInfo->setOptionMultipleShipping(1);
        $this->app['orm.em']->flush($BaseInfo);
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
        $faker = $this->getFaker();
        $client = $this->createClient();
        $this->scenarioCartIn($client);
        $this->scenarioCartIn($client); // 2個カート投入

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        // 複数配送画面
        $crawler = $client->request('GET', $this->app->path('shopping_shipping_multiple'));

        // お届け先情報入力画面
        $crawler = $client->request('GET', $this->app->path('shopping_shipping_multiple_edit'));

        $form = $this->createShippingFormData();
        $form['fax'] = array(
            'fax01' => $form['tel']['tel01'],
            'fax02' => $form['tel']['tel02'],
            'fax03' => $form['tel']['tel03'],
        );

        // お届け先追加
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $form)
        );

        $crawler = $client->request('GET', $this->app->path('shopping_shipping_multiple'));

        // 配送先1, 配送先2の情報を返す
        $shippings = $crawler->filter('#form_shipping_multiple_0_shipping_0_customer_address > option')->each(
            function ($node, $i) {
                return array(
                    'customer_address' => $node->attr('value'),
                    'quantity' => 1
                );
            }
        );

        // 複数配送設定
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_shipping_multiple'),
            array('form' =>
                  array(
                      'shipping_multiple' =>
                      array(0 =>
                            array(
                                // 配送先1, 配送先2 の 情報を渡す
                                'shipping' => $shippings
                            )
                      ),
                      '_token' => 'dummy'
                  )
            )
        );

        // 確認画面
        $crawler = $this->scenarioConfirm($client);

        // 完了画面
        $crawler = $this->scenarioComplete(
            $client,
            $this->app->path('shopping_confirm'),
            array(
                // 配送先1
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1,
                ),
                // 配送先2
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1,
                ),
            )
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_complete')));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $this->expected = '[' . $BaseInfo->getShopName() . '] ご注文ありがとうございます';
        $this->actual = $Message->subject;
        $this->verify();

        $body = $this->parseMailCatcherSource($Message);
        $this->assertRegexp('/◎お届け先2/', $body, '複数配送のため, お届け先2が存在する');
    }

    public function createNonmemberFormData()
    {
        $faker = $this->getFaker();
        $email = $faker->safeEmail;
        $form = parent::createShippingFormData();
        $form['email'] = array(
            'first' => $email,
            'second' => $email
        );
        return $form;
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithOneAddressOneItem()
    {
        $client = $this->client;

        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);
        $addressNumber = 1;

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = $addressNumber;
        $this->verify();
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithOneAddressOneItemTwoQuantities()
    {
        $client = $this->client;
        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => 1));

        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);
        $addressNumber = 1;

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = $addressNumber;
        $this->verify();
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithOneAddressTwoItemsTwoQuantities()
    {
        $client = $this->client;

        // Product test 1 with type 1
        $Product = $this->createProduct();
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->persist($ProductClass2);
        $this->app['orm.em']->flush();

        // Item of product 1
        $this->scenarioCartIn($client, $ProductClass->getId());

        // Item of product 2
        $this->scenarioCartIn($client, $ProductClass2->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);
        $addressNumber = 1;

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = $addressNumber;
        $this->verify();
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithTwoAddressesTwoItemsThreeQuantities()
    {
        $client = $this->client;

        // Product test 1 with type 1
        $Product = $this->createProduct();
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->persist($ProductClass2);
        $this->app['orm.em']->flush();

        // Item of product 1
        $this->scenarioCartIn($client, $ProductClass->getId());
        $this->scenarioCartIn($client, $ProductClass->getId());

        // Item of product 2
        $this->scenarioCartIn($client, $ProductClass2->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);
        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 111,
            'fax02' => 111,
            'fax03' => 111,
        );
        unset($formData['email']);

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $formData)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_shipping_multiple')));
        $addressNumber += 1;

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

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
        $client = $this->client;

        // Product test 1 with type 1
        $Product = $this->createProduct();
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->persist($ProductClass2);
        $this->app['orm.em']->flush();

        // Item of product 1
        $this->scenarioCartIn($client, $ProductClass->getId());
        $this->scenarioCartIn($client, $ProductClass->getId());

        // Item of product 2
        $this->scenarioCartIn($client, $ProductClass2->getId());
        $this->scenarioCartIn($client, $ProductClass2->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);
        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 111,
            'fax02' => 111,
            'fax03' => 111,
        );
        unset($formData['email']);

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $formData)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_shipping_multiple')));
        $addressNumber += 1;

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

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
        $client = $this->client;

        // Product test 1 with type 1
        $Product = $this->createProduct();
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        // Product test 3
        $Product3 = $this->createProduct();
        $ProductClass3 = $Product3->getProductClasses()->first();
        $ProductClass3->setStock(111);

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->persist($ProductClass2);
        $this->app['orm.em']->persist($ProductClass3);
        $this->app['orm.em']->flush();

        // Item of product 1
        $this->scenarioCartIn($client, $ProductClass->getId());
        $this->scenarioCartIn($client, $ProductClass->getId());

        // Item of product 2
        $this->scenarioCartIn($client, $ProductClass2->getId());
        $this->scenarioCartIn($client, $ProductClass2->getId());

        // Item of product 3
        $this->scenarioCartIn($client, $ProductClass3->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);
        $addressNumber = 1;

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    // item 1
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
                // item 2
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
                // item 3
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

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
        $client = $this->client;

        // Product test 1 with type 1
        $Product = $this->createProduct();
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        // Product test 3
        $Product3 = $this->createProduct();
        $ProductClass3 = $Product3->getProductClasses()->first();
        $ProductClass3->setStock(111);

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->persist($ProductClass2);
        $this->app['orm.em']->persist($ProductClass3);
        $this->app['orm.em']->flush();

        // Item of product 1
        $this->scenarioCartIn($client, $ProductClass->getId());
        $this->scenarioCartIn($client, $ProductClass->getId());

        // Item of product 2
        $this->scenarioCartIn($client, $ProductClass2->getId());
        $this->scenarioCartIn($client, $ProductClass2->getId());

        // Item of product 3
        $this->scenarioCartIn($client, $ProductClass3->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);
        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 111,
            'fax02' => 111,
            'fax03' => 111,
        );
        unset($formData['email']);

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $formData)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_shipping_multiple')));
        $addressNumber += 1;

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

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
        $client = $this->client;

        // Product test 1 with type 1
        $Product = $this->createProduct();
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        // Product test 3
        $Product3 = $this->createProduct();
        $ProductClass3 = $Product3->getProductClasses()->first();
        $ProductClass3->setStock(111);

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->persist($ProductClass2);
        $this->app['orm.em']->persist($ProductClass3);
        $this->app['orm.em']->flush();

        // Item of product 1
        $this->scenarioCartIn($client, $ProductClass->getId());
        $this->scenarioCartIn($client, $ProductClass->getId());

        // Item of product 2
        $this->scenarioCartIn($client, $ProductClass2->getId());
        $this->scenarioCartIn($client, $ProductClass2->getId());

        // Item of product 3
        $this->scenarioCartIn($client, $ProductClass3->getId());
        $this->scenarioCartIn($client, $ProductClass3->getId());
        $this->scenarioCartIn($client, $ProductClass3->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);
        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 111,
            'fax02' => 111,
            'fax03' => 111,
        );
        unset($formData['email']);

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $formData)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_shipping_multiple')));
        $addressNumber += 1;

        // Address 3
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 333,
            'fax02' => 333,
            'fax03' => 333,
        );
        unset($formData['email']);

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $formData)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_shipping_multiple')));
        $addressNumber += 1;

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 2,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

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
        $client = $this->client;

        $client->request('POST', '/cart/add', array('product_class_id' => 10, 'quantity' => 2));
        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => 1));
        $client->request('POST', '/cart/add', array('product_class_id' => 2, 'quantity' => 1));

        $this->scenarioCartIn($client);

        // unlock cart
        $this->app['eccube.service.cart']->unlock();

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('cart')));
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithoutCart()
    {
        $client = $this->client;

        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => 1));
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        // clear cart
        $cartService = $this->app['eccube.service.cart'];
        $cartService->clear();

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('cart')));
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingShippingUnlock()
    {
        $client = $this->client;

        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => 1));
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        // unlock when shipping
        $this->app['eccube.service.cart']->unlock();

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('cart')));
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithQuantityNotEqual()
    {
        $client = $this->client;

        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => 1));
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 3, // not equal
                        ),
                    ),
                ),
            ),
        );

        $crawler = $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertContains('数量の合計が、カゴの中の数量と異なっています', $crawler->filter('div#multiple_list_box__body')->html());
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingWithShippingEarlier()
    {
        $client = $this->client;

        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => 1));
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);
        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 111,
            'fax02' => 111,
            'fax03' => 111,
        );
        unset($formData['email']);

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $formData)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_shipping_multiple')));
        $addressNumber += 1;

        // before shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 2,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // after shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

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
        $client = $this->client;

        // Product test 1 with type 1
        $Product = $this->createProduct();
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStock(111);

        // Product test 2
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setStock(111);

        // Product test 3
        $Product3 = $this->createProduct();
        $ProductClass3 = $Product3->getProductClasses()->first();
        $ProductClass3->setStock(111);

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->persist($ProductClass2);
        $this->app['orm.em']->persist($ProductClass3);
        $this->app['orm.em']->flush();

        // Item of product 1
        $this->scenarioCartIn($client, $ProductClass->getId());
        $this->scenarioCartIn($client, $ProductClass->getId());

        // Item of product 2
        $this->scenarioCartIn($client, $ProductClass2->getId());
        $this->scenarioCartIn($client, $ProductClass2->getId());

        // Item of product 3
        $this->scenarioCartIn($client, $ProductClass3->getId());
        $this->scenarioCartIn($client, $ProductClass3->getId());
        $this->scenarioCartIn($client, $ProductClass3->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);
        $addressNumber = 1;

        // Address 2
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 111,
            'fax02' => 111,
            'fax03' => 111,
        );
        unset($formData['email']);

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $formData)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_shipping_multiple')));
        $addressNumber += 1;

        // Address 3
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 333,
            'fax02' => 333,
            'fax03' => 333,
        );
        unset($formData['email']);

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $formData)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_shipping_multiple')));
        $addressNumber += 1;

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 2,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

        // One shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);

        $this->expected = $addressNumber;
        $this->verify();

        $crawler = $this->scenarioConfirm($client);

        // shipping number on the screen
        $lastShipping = $crawler->filter('.is-edit h3')->last()->text();
        $this->assertContains((string)$addressNumber, $lastShipping);
    }

    /**
     * Test multi shipping with nonmember
     */
    public function testAddMultiShippingExceedNAddress()
    {
        // Max address need to test
        $maxAddress = 25;

        $client = $this->client;

        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => $maxAddress));
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $crawler = $this->scenarioConfirm($client);

        // お届け先設定画面への遷移前チェック
        $shipping_edit_change_url = $crawler->filter('a.btn-shipping-edit')->attr('href');
        $this->scenarioComplete($client, $shipping_edit_change_url);

        // Address
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 111,
            'fax02' => 111,
            'fax03' => 111,
        );
        unset($formData['email']);

        for ($i = 0; $i < $maxAddress; $i++) {
            $client->request(
                'POST',
                $this->app->url('shopping_shipping_multiple_edit'),
                array('shopping_shipping' => $formData)
            );
        }

        $crawler = $client->request('GET', $this->app->path('shopping_shipping_multiple'));

        $shipping = $crawler->filter('#form_shipping_multiple_0_shipping_0_customer_address > option')->each(
            function ($node, $i) {
                return array(
                    'customer_address' => $node->attr('value'),
                    'quantity' => 1
                );
            }
        );

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => $shipping
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

        $maxAddress += 1;
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);
        $this->expected = $maxAddress;
        $this->verify();

        $crawler = $this->scenarioConfirm($client);

        // shipping number on the screen
        $lastShipping = $crawler->filter('.is-edit h3')->last()->text();
        $this->assertContains((string)$maxAddress, $lastShipping);
    }

    /**
     * Test multi shipping with nonmember when there are two types of products.
     *
     * Give:
     * - Product type A x 1
     * - Product type B x 2
     * - Address x 1
     *
     * When:
     * - Shipment item:
     *  + Product type A x1 - address 1
     *  + Product type B x2 - address 1
     * - Delivery: 1 (for product type A)
     * - Delivery: 2 (for product type B)
     *
     * Then:
     * - Number of Shipping: 2
     *  + Product type B x 1 - address 1
     *  + Product type A x 2 - address 1
     * - Mail content: ◎お届け先2
     */
    public function testAddMultiShippingWithProductTypeOfOneShippingAreNotSame()
    {
        $client = $this->client;

        $Product = $this->createProduct();
        $ProductType = $this->app['eccube.repository.master.product_type']->find(2);
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setProductType($ProductType)->setStock(111);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        $client->request('POST', '/cart/add', array('product_class_id' => $ProductClass->getId(), 'quantity' => 2));
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $this->scenarioConfirm($client);

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                // 配送先1 Product 2 type B
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 2,
                        ),
                    ),
                ),
                // 配送先2 Product 1 type A
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

        // total delivery fee
        $this->actual = $Order->getDeliveryFeeTotal();
        $this->expected = 1000;
        $this->verify();

        // two shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);
        $this->expected = 2;
        $this->verify();

        $this->scenarioConfirm($client);

        // 完了画面
        $this->scenarioComplete(
            $client,
            $this->app->path('shopping_confirm'),
            array(
                // 配送先1 Product 2 type B
                array(
                    'delivery' => 2,
                ),
                // 配送先2 Product 1 type A
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1,
                )
            ),
            3
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_complete')));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $this->expected = '[' . $BaseInfo->getShopName() . '] ご注文ありがとうございます';
        $this->actual = $Message->subject;
        $this->verify();

        $body = $this->parseMailCatcherSource($Message);
        $this->assertRegexp('/◎お届け先2/', $body, '複数配送のため, お届け先2が存在する');
    }

    /**
     * Test multi shipping with nonmember when there are two types of products.
     *
     * Give:
     * - Product 1 type A x 1
     * - Product 2 type B x 2
     * - Product 3 type B x 2
     * - Address x 2
     *
     * When:
     * - Shipment item:
     *  + Product 1 type A x1 - address 1
     *  + Product 2 type B x2 - address 2
     *  + Product 3 type B x2 - address 1
     * - Delivery: 1 (for product 1 type A)
     * - Delivery: 2 (for product 2 type B)
     * - Delivery: 2 (for product 3 type B)
     *
     * Then:
     * - Number of Shipping: 3
     *  + Product 2 type B x 2 - address 2
     *  + Product 3 type B x 2 - address 1
     *  + Product 1 type A x 1 - address 1
     * - Mail content: ◎お届け先3
     */
    public function testAddMultiShippingWithManyProductTypeOfOneShippingAreNotSame()
    {
        $client = $this->client;

        $Product = $this->createProduct();
        $ProductType = $this->app['eccube.repository.master.product_type']->find(2);
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setProductType($ProductType)->setStock(111);
        $this->app['orm.em']->persist($ProductClass);
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductClass2->setProductType($ProductType)->setStock(111);
        $this->app['orm.em']->persist($ProductClass2);
        $this->app['orm.em']->flush();

        $client->request('POST', '/cart/add', array('product_class_id' => $ProductClass->getId(), 'quantity' => 2));
        $client->request('POST', '/cart/add', array('product_class_id' => $ProductClass2->getId(), 'quantity' => 2));
        $this->scenarioCartIn($client);

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $this->scenarioConfirm($client);

        // Address 2
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 111,
            'fax02' => 111,
            'fax03' => 111,
        );
        unset($formData['email']);

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $formData)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_shipping_multiple')));

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 2,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 1,
                            'quantity' => 2,
                        ),
                    ),
                ),
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

        // total delivery fee
        $this->actual = $Order->getDeliveryFeeTotal();
        $this->expected = 1000;
        $this->verify();

        // two shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);
        $this->expected = 3;
        $this->verify();

        $this->scenarioConfirm($client);

        // 完了画面
        $this->scenarioComplete(
            $client,
            $this->app->path('shopping_confirm'),
            array(
                // 配送先1
                array(
                    'delivery' => 2,
                ),
                // 配送先2
                array(
                    'delivery' => 2,
                ),
                // 配送先3
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1,
                ),
            ),
            3
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_complete')));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $this->expected = '[' . $BaseInfo->getShopName() . '] ご注文ありがとうございます';
        $this->actual = $Message->subject;
        $this->verify();

        $body = $this->parseMailCatcherSource($Message);
        $this->assertRegexp('/◎お届け先3/', $body, '複数配送のため, お届け先3が存在する');
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
        $client = $this->client;

        $Product = $this->createProduct();
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStock(111);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush();

        // Three items of product
        $this->scenarioCartIn($client, $ProductClass->getId());
        $this->scenarioCartIn($client, $ProductClass->getId());
        $this->scenarioCartIn($client, $ProductClass->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $this->scenarioConfirm($client);

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

        // one shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);
        $this->expected = 1;
        $this->verify();

        // total delivery fee
        $this->actual = $Order->getDeliveryFeeTotal();
        $this->expected = 1000;
        $this->verify();

        // 確認画面
        $crawler = $this->scenarioConfirm($client);

        // item number on the screen
        $shipping = $crawler->filter('.is-edit .cart_item')->text();
        $this->assertContains('× 3', $shipping);

        $deliver = $crawler->filter('#shopping_shippings_0_delivery > option')->each(
            function ($node, $i) {
                return $node->text();
            }
        );

        $this->expected = 'サンプル業者';
        $this->actual = $deliver;
        $this->assertTrue(in_array($this->expected, $this->actual));

        // 完了画面
        $this->scenarioComplete(
            $client,
            $this->app->path('shopping_confirm'),
            array(
                // 配送先1
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1,
                ),
            )
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_complete')));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $this->expected = '[' . $BaseInfo->getShopName() . '] ご注文ありがとうございます';
        $this->actual = $Message->subject;
        $this->verify();

        $body = $this->parseMailCatcherSource($Message);
        $this->assertRegexp('/◎お届け先/', $body, '複数配送のため, お届け先1が存在する');
    }

    /**
     * Test multi shipping with nonmember
     *
     * Give:
     * - Product type A x 3
     * - Product type B x 3
     * - Address x 2
     *
     * When:
     * - Shipment item:
     *  + Product type A x1 - address 1
     *  + Product type A x1 - address 2
     *  + Product type A x1 - address 1
     *  + Product type B x1 - address 1
     *  + Product type B x1 - address 2
     *  + Product type B x1 - address 1
     * - Delivery: 1 - product type A - address 1
     * - Delivery: 1 - product type A - address 2
     * - Delivery: 2 - product type B - address 1
     * - Delivery: 2 - product type B - address 2
     *
     * Then:
     * - Number of Shipping: 4
     *  + Shipping 1: Product type B x2 - address 1
     *  + Shipping 2: Product type A x2 - address 1
     *  + Shipping 3: Product type B x1 - address 2
     *  + Shipping 4: Product type A x1 - address 2
     * - Delivery 3: サンプル宅配
     * - Mail content: ◎お届け先4
     */
    public function testAddMultiShippingThreeItemsOfTwoProductHasTwoTypeWithTwoAddress()
    {
        $client = $this->client;

        // Product 1 with type A
        $Product = $this->createProduct();
        $ProductClass = $Product->getProductClasses()->first();
        $ProductClass->setStock(111);

        // Product 2 with type B
        $Product2 = $this->createProduct();
        $ProductClass2 = $Product2->getProductClasses()->first();
        $ProductType = $this->app['eccube.repository.master.product_type']->find(2);
        $ProductClass2->setProductType($ProductType)->setStock(111);

        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->persist($ProductClass2);
        $this->app['orm.em']->flush();

        // Three items of product 1
        $this->scenarioCartIn($client, $ProductClass->getId());
        $this->scenarioCartIn($client, $ProductClass->getId());
        $this->scenarioCartIn($client, $ProductClass->getId());

        // Three item of product 2
        $this->scenarioCartIn($client, $ProductClass2->getId());
        $this->scenarioCartIn($client, $ProductClass2->getId());
        $this->scenarioCartIn($client, $ProductClass2->getId());

        $formData = $this->createNonmemberFormData();
        $this->scenarioInput($client, $formData);

        $this->scenarioConfirm($client);

        // Address 2
        $formData = $this->createNonmemberFormData();
        $formData['fax'] = array(
            'fax01' => 111,
            'fax02' => 111,
            'fax03' => 111,
        );
        unset($formData['email']);

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple_edit'),
            array('shopping_shipping' => $formData)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_shipping_multiple')));

        // add multi shipping
        $multiForm = array(
            '_token' => 'dummy',
            'shipping_multiple' => array(
                // three item of product 1
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
                // three item of product 2
                array(
                    'shipping' => array(
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 1,
                            'quantity' => 1,
                        ),
                        array(
                            'customer_address' => 0,
                            'quantity' => 1,
                        ),
                    ),
                ),
            ),
        );

        $client->request(
            'POST',
            $this->app->url('shopping_shipping_multiple'),
            array('form' => $multiForm)
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping')));

        // process order id
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId));

        // four shipping
        $Shipping = $Order->getShippings();
        $this->actual = count($Shipping);
        $this->expected = 4;
        $this->verify();

        // total delivery fee
        $this->actual = $Order->getDeliveryFeeTotal();
        $this->expected = 2000;
        $this->verify();

        // 確認画面
        $crawler = $this->scenarioConfirm($client);

        // item number on the screen
        $shipping = $crawler->filter('.is-edit .cart_item')->first()->text();
        $this->assertContains('× 2', $shipping);

        $deliver = $crawler->filter('#shopping_shippings_3_delivery > option')->each(
            function ($node, $i) {
                return $node->text();
            }
        );

        $this->expected = 'サンプル業者';
        $this->actual = $deliver;
        $this->assertTrue(in_array($this->expected, $this->actual));

        // 完了画面
        $this->scenarioComplete(
            $client,
            $this->app->url('shopping_confirm'),
            array(
                // Product type 2 with address 1 (two item)
                array(
                    'delivery' => 2,
                ),
                // Product type 1 with address 1 (two item)
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1,
                ),
                // Product type 2 with address 2 (one item)
                array(
                    'delivery' => 2,
                ),
                // Product type 1 with address 2 (one item)
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1,
                ),
            ),
            3
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('shopping_complete')));

        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $Messages = $this->getMailCatcherMessages();
        $Message = $this->getMailCatcherMessage($Messages[0]->id);

        $this->expected = '[' . $BaseInfo->getShopName() . '] ご注文ありがとうございます';
        $this->actual = $Message->subject;
        $this->verify();

        $body = $this->parseMailCatcherSource($Message);
        $this->assertRegexp('/◎お届け先4/', $body, '複数配送のため, お届け先4が存在する');
    }
}
