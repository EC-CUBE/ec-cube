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

use Eccube\Entity\Order;

class EditControllerTest extends AbstractEditControllerTestCase
{
    protected $Customer;
    protected $Order;
    protected $Product;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Product = $this->createProduct();
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
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_new'),
            array(
                'order' => $this->createFormData($this->Customer, $this->Product),
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
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $this->Product);
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
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_search_product'),
            array(
                'id' => $this->Product->getId()
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
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $this->Product);
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
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $this->Product);
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
            $tax = (int) $this->app['eccube.service.tax_rule']->calcTax($orderDetail['price'], $orderDetail['tax_rate'], $orderDetail['tax_rule']);
            $totalTax += $tax * $formDataForEdit['OrderDetails'][$indx]['quantity'];
        }

        // 管理画面で受注編集する
        $this->client->request(
            'POST', $this->app->url('admin_order_edit', array('id' => $Order->getId())), array(
            'order' => $formDataForEdit,
            'mode' => 'register'
            )
        );
        $EditedOrderafterEdit = $this->app['eccube.repository.order']->find($Order->getId());

        //確認する「トータル税金」
        $this->expected = $totalTax;
        $this->actual = $EditedOrderafterEdit->getTax();
        $this->verify();
    }

    /**
     * 受注登録時に会員情報が正しく保存されているかどうかのテスト
     * @link https://github.com/EC-CUBE/ec-cube/issues/1682
     */
    public function testOrderProcessingWithCustomer()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_new'),
            array(
                'order' => $this->createFormData($this->Customer, $this->Product),
                'mode' => 'register'
            )
        );

        $url = $crawler->filter('a')->text();

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
}
