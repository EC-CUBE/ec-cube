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

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Common\Constant;
use Eccube\Entity\BaseInfo;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\CartService;
use Eccube\Service\TaxRuleService;

class EditControllerTest extends AbstractEditControllerTestCase
{
    protected $Customer;
    protected $Order;
    protected $Product;
    protected $cartService;
    protected $orderRepository;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Product = $this->createProduct();
        $this->customerRepository = $this->container->get(CustomerRepository::class);
        $this->orderRepository = $this->container->get(OrderRepository::class);
        $this->cartService = $this->container->get(CartService::class);
        $BaseInfo = $this->container->get(BaseInfo::class);
        $this->entityManager->flush($BaseInfo);
    }

    public function testRoutingAdminOrderNew()
    {
        $this->client->request('GET', $this->generateUrl('admin_order_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminOrderNewPost()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_new'),
            [
                'order' => $this->createFormData($this->Customer, $this->Product),
                'mode' => 'register',
            ]
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));
    }

    public function testRoutingAdminOrderEdit()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $crawler = $this->client->request('GET', $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminOrderEditPost()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $this->Product);
        $this->client->request(
            'POST',
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $this->expected = $formData['name']['name01'];
        $this->actual = $EditedOrder->getName01();
        $this->verify();

        // TODO
        // 顧客の購入回数と購入金額確認
        // $this->expected =  $EditedOrder->getPaymentTotal();
        // $this->actual = $EditedOrder->getCustomer()->getBuyTotal();
        // $this->verify();
        // $this->expected = 1;
        // $this->actual = $EditedOrder->getCustomer()->getBuyTimes();
        // $this->verify();
    }

    public function testNotUpdateLastBuyDate()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $this->Product);
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));
        $EditedCustomer = $this->customerRepository->find($Customer->getId());
        $this->expected = $Customer->getLastBuyDate();
        $this->actual = $EditedCustomer->getLastBuyDate();
        $this->verify();
    }

    public function testSearchCustomer()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_search_customer'),
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

    public function testOrderCustomerInfo()
    {
        $this->markTestSkipped('顧客の購入回数と購入金額の実装が完了するまでスキップ');
        $this->markTestIncomplete('EditController is not implemented.');
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);

        $formData = $this->createFormData($Customer, $this->Product);
        $this->client->request(
            'POST',
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());

        // 顧客の購入回数と購入金額確認
        $totalPrice = $EditedOrder->getTotalPrice();
        $this->expected = $totalPrice;
        $this->actual = $EditedOrder->getCustomer()->getBuyTotal();
        $this->verify();
        $this->expected = 1;
        $this->actual = $EditedOrder->getCustomer()->getBuyTimes();
        $this->verify();

        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $this->Product);
        $this->client->request(
            'POST',
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());

        // 顧客の購入回数と購入金額確認
        $this->expected = $totalPrice + $EditedOrder->getTotalPrice();
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
            $this->generateUrl('admin_order_search_customer'),
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
            $this->generateUrl('admin_order_search_customer_by_id'),
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
            $this->generateUrl('admin_order_search_product'),
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
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $this->Product);
        $formData['OrderStatus'] = 8; // 購入処理中で受注を登録する
        // 管理画面から受注登録
        $this->client->request(
            'POST',
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $this->expected = $formData['OrderStatus'];
        $this->actual = $EditedOrder->getOrderStatus()->getId();
        $this->verify();

        $this->markTestSkipped('フロントとのセッション管理の実装が完了するまでスキップ');
        // フロント側から, product_class_id = 1 をカート投入
        $client = $this->createClient();
        $client->request(
            'PUT',
            $this->generateUrl(
                'cart_handle_item',
                [
                    'operation' => 'up',
                    'productClassId' => 1,
                ]
            ),
            [Constant::TOKEN_NAME => '_dummy']
        );
        $this->container->get(CartService::class)->lock();

        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);
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
            'zip' => [
                'zip01' => $faker->postcode1(),
                'zip02' => $faker->postcode2(),
            ],
            'address' => [
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ],
            'tel' => [
                'tel01' => $tel[0],
                'tel02' => $tel[1],
                'tel03' => $tel[2],
            ],
            'email' => [
                'first' => $email,
                'second' => $email,
            ],
            '_token' => 'dummy',
        ];

        $client->request(
            'POST',
            $this->generateUrl('shopping_nonmember'),
            ['nonmember' => $clientFormData]
        );
        $this->cartService->lock();

        $crawler = $client->request('GET', $this->generateUrl('shopping'));
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
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $formData = $this->createFormData($Customer, $this->Product);
        // 管理画面から受注登録
        $this->client->request(
            'POST', $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]), [
            'order' => $formData,
            'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $formDataForEdit = $this->createFormDataForEdit($EditedOrder);

        //税金計算
        $totalTax = 0;
        foreach ($formDataForEdit['OrderItems'] as $indx => $orderItem) {
            //商品数変更3個追加
            $formDataForEdit['OrderItems'][$indx]['quantity'] = $orderItem['quantity'] + 3;
            $tax = (int) $this->container->get(TaxRuleService::class)->calcTax($orderItem['price'], $orderItem['tax_rate'], $orderItem['tax_rule']);
            $totalTax += $tax * $formDataForEdit['OrderItems'][$indx]['quantity'];
        }

        // 管理画面で受注編集する
        $this->client->request(
            'POST', $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]), [
            'order' => $formDataForEdit,
            'mode' => 'register',
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));
        $EditedOrderafterEdit = $this->orderRepository->find($Order->getId());

        //確認する「トータル税金」
        $this->expected = $totalTax;
        $this->actual = $EditedOrderafterEdit->getTax();
        $this->verify();
    }

    /**
     * 受注登録時に会員情報が正しく保存されているかどうかのテスト
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1682
     */
    public function testOrderProcessingWithCustomer()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_new'),
            [
                'order' => $this->createFormData($this->Customer, $this->Product),
                'mode' => 'register',
            ]
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        $savedOderId = preg_replace('/.*\/admin\/order\/(\d+)\/edit/', '$1', $url);
        $SavedOrder = $this->orderRepository->find($savedOderId);

        $this->assertNotNull($SavedOrder);
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
