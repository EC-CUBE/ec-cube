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

namespace Eccube\Tests\Web\Admin\Order;

class EditControllerTest extends AbstractEditControllerTestCase
{
    protected $Customer;
    protected $Order;
    protected $Product;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
     //   $this->Product = $this->createProduct();
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        // 複数配送を無効に
        $BaseInfo->setOptionMultipleShipping(0);
        $this->app['orm.em']->flush($BaseInfo);
    }

    public function testRoutingAdminOrderNew()
    {
        $this->client->request('GET', $this->app->url('admin_order_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminOrderNewPost()
    {
        $Product = $this->createProduct();
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_new'),
            array(
                'order' => $this->createFormData($this->Customer, $Product, 1),
                'mode' => 'register'
            )
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));
    }

    public function testRoutingAdminOrderEdit()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $crawler = $this->client->request('GET', $this->app->url('admin_order_edit', array('id' => $Order->getId())));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminOrderEditPost()
    {
        $Customer = $this->createCustomer();
        $Product = $this->createProduct();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $Product, 1);
        $this->client->request(
            'POST',
            $this->app->url('admin_order_edit', array('id' => $Order->getId())),
            array(
                'order' => $formData,
                'mode' => 'register'
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_edit', array('id' => $Order->getId()))));

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());
        $this->expected = $formData['name']['name01'];
        $this->actual = $EditedOrder->getName01();
        $this->verify();

        // 顧客の購入回数と購入金額確認
        $this->expected =  $EditedOrder->getTotalPrice();
        $this->actual = $EditedOrder->getCustomer()->getBuyTotal();
        $this->verify();
        $this->expected = 1;
        $this->actual = $EditedOrder->getCustomer()->getBuyTimes();
        $this->verify();
    }

    public function testSearchCustomer()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_search_customer'),
            array(
                'search_word' => $this->Customer->getId()
            ),
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            )
        );
        $Result = json_decode($this->client->getResponse()->getContent(), true);

        $this->expected = $this->Customer->getName01().$this->Customer->getName02().'('.$this->Customer->getKana01().$this->Customer->getKana02().')';
        $this->actual = $Result[0]['name'];
        $this->verify();
    }

    public function testOrderCustomerInfo()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Product = $this->createProduct();

        $formData = $this->createFormData($Customer, $Product);
        $this->client->request(
            'POST',
            $this->app->url('admin_order_edit', array('id' => $Order->getId())),
            array(
                'order' => $formData,
                'mode' => 'register'
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_edit', array('id' => $Order->getId()))));

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());

        // 顧客の購入回数と購入金額確認
        $totalPrice = $EditedOrder->getTotalPrice();
        $this->expected = $totalPrice ;
        $this->actual = $EditedOrder->getCustomer()->getBuyTotal();
        $this->verify();
        $this->expected = 1;
        $this->actual = $EditedOrder->getCustomer()->getBuyTimes();
        $this->verify();

        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $Product);
        $this->client->request(
            'POST',
            $this->app->url('admin_order_edit', array('id' => $Order->getId())),
            array(
                'order' => $formData,
                'mode' => 'register'
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_edit', array('id' => $Order->getId()))));

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());

        // 顧客の購入回数と購入金額確認
        $this->expected =  $totalPrice + $EditedOrder->getTotalPrice();
        $this->actual = $EditedOrder->getCustomer()->getBuyTotal();
        $this->verify();
        $this->expected = 2;
        $this->actual = $EditedOrder->getCustomer()->getBuyTimes();
        $this->verify();
    }

    public function testSearchCustomerHtml()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_search_customer'),
            array(
                'search_word' => $this->Customer->getId()
            ),
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            )
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testSearchCustomerById()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_search_customer_by_id'),
            array(
                'id' => $this->Customer->getId()
            ),
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            )
        );
        $Result = json_decode($this->client->getResponse()->getContent(), true);

        $this->expected = $this->Customer->getName01();
        $this->actual = $Result['name01'];
        $this->verify();
    }

    public function testSearchProduct()
    {
        $Product = $this->createProduct();
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_search_product'),
            array(
                'id' => $Product->getId()
            ),
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            )
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 管理画面から購入処理中で受注登録し, フロントを参照するテスト
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1452
     */
    public function testOrderProcessingToFrontConfirm()
    {
        $Customer = $this->createCustomer();
        $Product = $this->createProduct();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $Product, 1);
        $formData['OrderStatus'] = 8; // 購入処理中で受注を登録する
        // 管理画面から受注登録
        $this->client->request(
            'POST',
            $this->app->url('admin_order_edit', array('id' => $Order->getId())),
            array(
                'order' => $formData,
                'mode' => 'register'
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_edit', array('id' => $Order->getId()))));

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());
        $this->expected = $formData['OrderStatus'];
        $this->actual = $EditedOrder->getOrderStatus()->getId();
        $this->verify();

        // フロント側から, product_class_id = 1 をカート投入
        $client = $this->createClient();
        $crawler = $client->request('POST', '/cart/add', array('product_class_id' => 1));
        $this->app['eccube.service.cart']->lock();

        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);
        $email = $faker->safeEmail;

        $clientFormData = array(
            'name' => array(
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ),
            'kana' => array(
                'kana01' => $faker->lastKanaName,
                'kana02' => $faker->firstKanaName,
            ),
            'company_name' => $faker->company,
            'zip' => array(
                'zip01' => $faker->postcode1(),
                'zip02' => $faker->postcode2(),
            ),
            'address' => array(
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ),
            'tel' => array(
                'tel01' => $tel[0],
                'tel02' => $tel[1],
                'tel03' => $tel[2],
            ),
            'email' => array(
                'first' => $email,
                'second' => $email,
            ),
            '_token' => 'dummy'
        );

        $client->request(
            'POST',
            $this->app->path('shopping_nonmember'),
            array('nonmember' => $clientFormData)
        );
        $this->app['eccube.service.cart']->lock();

        $crawler = $client->request('GET', $this->app->path('shopping'));
        $this->expected = 'ご注文内容のご確認';
        $this->actual = $crawler->filter('h1.page-heading')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'ディナーフォーク';
        $this->actual = $crawler->filter('dt.item_name')->last()->text();

        $OrderDetails = $EditedOrder->getOrderDetails();
        foreach ($OrderDetails as $OrderDetail) {
            if ($this->actual == $OrderDetail->getProduct()->getName()) {
                $this->fail('#1452 の不具合');
            }
        }

        $this->verify('カートに投入した商品が表示される');
    }

    /**
     * 受注編集時に、dtb_order.taxの値が正しく保存されているかどうかのテスト
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1606
     */
    public function testOrderProcessingWithTax()
    {

        $Customer = $this->createCustomer();
        $Product = $this->createProduct();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $Product, 1);
        // 管理画面から受注登録
        $this->client->request(
            'POST', $this->app->url('admin_order_edit', array('id' => $Order->getId())), array(
                'order' => $formData,
                'mode' => 'register'
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_edit', array('id' => $Order->getId()))));

        $EditedOrder = $this->app['eccube.repository.order']->find($Order->getId());
        $formDataForEdit = $this->createFormDataForEdit($EditedOrder);

        //税金計算
        $totalTax = 0;
        foreach ($formDataForEdit['OrderDetails'] as $indx => $orderDetail) {
            //商品数変更3個追加
            $formDataForEdit['OrderDetails'][$indx]['quantity'] = $orderDetail['quantity'] + 3;
            $tax = (int)$this->app['eccube.service.tax_rule']->calcTax($orderDetail['price'], $orderDetail['tax_rate'], $orderDetail['tax_rule']);
            $totalTax += $tax * $formDataForEdit['OrderDetails'][$indx]['quantity'];
        }

        // Multi用項目を削除
        foreach($formDataForEdit['Shippings'] as $key => $node){
            if(isset($node['ShipmentItems'])){
                unset($formDataForEdit['Shippings'][$key]['ShipmentItems']);
            }
        }

        // 管理画面で受注編集する
        $this->client->request(
            'POST', $this->app->url('admin_order_edit', array('id' => $Order->getId())), array(
                'order' => $formDataForEdit,
                'mode' => 'register'
            )
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_edit', array('id' => $Order->getId()))));
        $EditedOrderafterEdit = $this->app['eccube.repository.order']->find($Order->getId());

        //確認する「トータル税金」
        $this->expected = $totalTax;
        $this->actual = $EditedOrderafterEdit->getTax();
        $this->verify();
    }

    /**
     * 受注登録時に会員情報が正しく保存されているかどうかのテスト
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1682
     */
    public function testOrderProcessingWithCustomer()
    {
        $Product = $this->createProduct();
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_new'),
            array(
                'order' => $this->createFormData($this->Customer, $Product, 1),
                'mode' => 'register'
            )
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        $savedOderId = preg_replace('/.*\/admin\/order\/(\d+)\/edit/', '$1', $url);
        $SavedOrder = $this->app['eccube.repository.order']->find($savedOderId);

        $this->expected = $this->Customer->getSex();
        $this->actual = $SavedOrder->getSex();
        $this->verify('会員の性別が保存されている');

        $this->expected = $this->Customer->getJob();
        $this->actual = $SavedOrder->getJob();
        $this->verify('会員の職業が保存されている');

        $this->expected = $this->Customer->getBirth();
        $this->actual = $SavedOrder->getBirth();
        $this->verify('会員の誕生日が保存されている');
    }

    /**
     * 受注登録時に在庫が正しく更新されるかのテスト
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/2084
     */
    public function testNewOrderStock()
    {

        $Product = $this->createProduct('在庫', 1);

        $ProductClass = $Product->getProductClasses();

        $stock = $ProductClass[0]->getProductStock()->getStock();

        $formData = $this->createFormData($this->Customer, $Product);

        $quantity = 20;

        $formData['OrderDetails']['0']['quantity'] = $quantity;

        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_new'),
            array(
                'order' => $formData,
                'mode' => 'register'
            )
        );

        $ProductStock = $this->app['eccube.repository.product_stock']->find($ProductClass[0]->getProductStock()->getId());

        $this->expected = $ProductStock->getStock();
        $this->actual = $stock - $quantity;

        $this->verify();

    }


    /**
     * 受注編集時に在庫が正しく更新されるかのテスト(数量変更なし)
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/2084
     */
    public function testUpdateOrderStock()
    {

        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);

        $Product = $this->createProduct('在庫', 1);
        $ProductClass = $Product->getProductClasses();
        $stock = $ProductClass[0]->getProductStock()->getStock();
        $formData = $this->createFormData($Customer, $Product);

        $OrderDetail = $Order->getOrderDetails();

        $quantity = $OrderDetail[0]->getQuantity();

        $formData['OrderDetails']['0']['quantity'] = $quantity;

        $this->client->request(
            'POST',
            $this->app->url('admin_order_edit', array('id' => $Order->getId())),
            array(
                'order' => $formData,
                'mode' => 'register'
            )
        );

        $ProductStock = $this->app['eccube.repository.product_stock']->find($ProductClass[0]->getProductStock()->getId());

        $this->expected = $ProductStock->getStock();
        $this->actual = $stock;

        $this->verify();

    }

    /**
     * 受注編集時に在庫が正しく更新されるかのテスト(数量変更あり)
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/2084
     */
    public function testUpdateOrderStockAdd()
    {

        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);

        $Product = $this->createProduct('在庫', 1);
        $ProductClass = $Product->getProductClasses();
        $stock = $ProductClass[0]->getProductStock()->getStock();
        $formData = $this->createFormData($Customer, $Product);

        $OrderDetail = $Order->getOrderDetails();

        $quantity = $OrderDetail[0]->getQuantity();

        $quantity = $quantity + 5;

        $formData['OrderDetails']['0']['quantity'] = $quantity;

        $this->client->request(
            'POST',
            $this->app->url('admin_order_edit', array('id' => $Order->getId())),
            array(
                'order' => $formData,
                'mode' => 'register'
            )
        );

        $ProductStock = $this->app['eccube.repository.product_stock']->find($ProductClass[0]->getProductStock()->getId());

        $this->expected = $ProductStock->getStock();
        $this->actual = $stock - 5;

        $this->verify();

    }

    /**
     * 受注編集時に在庫が正しく更新されるかのテスト(数量変更あり)
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/2084
     */
    public function testUpdateOrderStockRemove()
    {

        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);

        $Product = $this->createProduct('在庫', 1);
        $ProductClass = $Product->getProductClasses();
        $stock = $ProductClass[0]->getProductStock()->getStock();
        $formData = $this->createFormData($Customer, $Product);

        $OrderDetail = $Order->getOrderDetails();

        $quantity = $OrderDetail[0]->getQuantity();

        $quantity = $quantity - 3;

        $formData['OrderDetails']['0']['quantity'] = $quantity;

        $this->client->request(
            'POST',
            $this->app->url('admin_order_edit', array('id' => $Order->getId())),
            array(
                'order' => $formData,
                'mode' => 'register'
            )
        );

        $ProductStock = $this->app['eccube.repository.product_stock']->find($ProductClass[0]->getProductStock()->getId());

        $this->expected = $ProductStock->getStock();
        $this->actual = $stock + 3;

        $this->verify();

    }

    /**
     * 受注編集時に商品追加後、在庫が正しく更新されるかのテスト
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/2084
     */
    public function testUpdateOrderStockOrderAdd()
    {

        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);

        $Product = $this->createProduct('在庫', 2);
        $ProductClasses = $Product->getProductClasses();
        $stock = $ProductClasses[1]->getProductStock()->getStock();
        $formData = $this->createFormData($Customer, $Product);

        $quantity = 10;

        $OrderDetails = array(
            'Product' => $Product->getId(),
            'ProductClass' => $ProductClasses[1]->getId(),
            'price' => $ProductClasses[1]->getPrice02(),
            'quantity' => $quantity,
            'tax_rate' => 8,
            'tax_rule' => 1,
            'product_name' => $Product->getName(),
            'product_code' => $ProductClasses[1]->getCode(),
        );
        $formData['OrderDetails'][] = $OrderDetails;

        $OrderDetail = $Order->getOrderDetails();
        $formData['OrderDetails']['0']['quantity'] = $OrderDetail[0]->getQuantity();

        $this->client->request(
            'POST',
            $this->app->url('admin_order_edit', array('id' => $Order->getId())),
            array(
                'order' => $formData,
                'mode' => 'register'
            )
        );

        $ProductStock = $this->app['eccube.repository.product_stock']->find($ProductClasses[1]->getProductStock()->getId());

        $this->expected = $ProductStock->getStock();
        $this->actual = $stock - $quantity;

        $this->verify();

    }


    /**
     * 受注編集時の商品追加時に、在庫がなければ追加できないテスト
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/2084
     */
    public function testSearchProductStock()
    {

        $Product = $this->createProduct('在庫', 1);

        $ProductClasses = $Product->getProductClasses();

        $ProductStock = $this->app['eccube.repository.product_stock']->find($ProductClasses[0]->getProductStock()->getId());

        $ProductClasses[0]->setStock(0);
        $ProductStock->setStock(0);

        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_search_product'),
            array(
                'id' => $Product->getId()
            ),
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            )
        );

        $this->expected = $crawler->filter('button')->getNode(0)->firstChild->data;

        $this->actual = 'ただいま品切れ中です';

        $this->verify();

    }
}
