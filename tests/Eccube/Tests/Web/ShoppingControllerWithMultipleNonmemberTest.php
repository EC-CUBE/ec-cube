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
                    'deliveryTime' => 1
                ),
                // 配送先2
                array(
                    'delivery' => 1,
                    'deliveryTime' => 1
                )
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
        $client->request('POST', '/cart/add', array('product_class_id' => 10, 'quantity' => 1));

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
        $client->request('POST', '/cart/add', array('product_class_id' => 10, 'quantity' => 2));

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

        $client->request('POST', '/cart/add', array('product_class_id' => 10, 'quantity' => 2));
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

        $client->request('POST', '/cart/add', array('product_class_id' => 10, 'quantity' => 2));
        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => 1));
        $client->request('POST', '/cart/add', array('product_class_id' => 2, 'quantity' => 1));
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

        $client->request('POST', '/cart/add', array('product_class_id' => 10, 'quantity' => 2));
        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => 1));
        $client->request('POST', '/cart/add', array('product_class_id' => 2, 'quantity' => 1));
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

        $client->request('POST', '/cart/add', array('product_class_id' => 10, 'quantity' => 2));
        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => 1));
        $client->request('POST', '/cart/add', array('product_class_id' => 2, 'quantity' => 3));
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

        $this->assertContains('数量の数が異なっています', $crawler->filter('div#multiple_list_box__body')->html());
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

        $client->request('POST', '/cart/add', array('product_class_id' => 10, 'quantity' => 2));
        $client->request('POST', '/cart/add', array('product_class_id' => 1, 'quantity' => 1));
        $client->request('POST', '/cart/add', array('product_class_id' => 2, 'quantity' => 3));
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
}
