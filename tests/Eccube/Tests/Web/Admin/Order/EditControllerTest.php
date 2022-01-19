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

use Eccube\Common\Constant;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\Job;
use Eccube\Entity\Master\Sex;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\TaxRule;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\CartService;
use Eccube\Service\TaxRuleService;

class EditControllerTest extends AbstractEditControllerTestCase
{
    /**
     * @var Customer
     */
    protected $Customer;

    /**
     * @var Order
     */
    protected $Order;

    /**
     * @var Product
     */
    protected $Product;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Product = $this->createProduct();
        $this->customerRepository = $this->entityManager->getRepository(\Eccube\Entity\Customer::class);
        $this->orderRepository = $this->entityManager->getRepository(\Eccube\Entity\Order::class);
        $this->cartService = self::$container->get(CartService::class);
        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->entityManager->flush($BaseInfo);
    }

    public function testRoutingAdminOrderNew()
    {
        $this->client->request('GET', $this->generateUrl('admin_order_new'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminOrderNewPost()
    {
        $formData = $this->createFormData($this->Customer, $this->Product);
        unset($formData['OrderStatus']);
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_new'),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        // pre_order_id がセットされているか確認
        /** @var Order[] $Orders */
        $Orders = $this->orderRepository->findBy([], ['create_date' => 'DESC']);
        $this->assertNotNull($Orders[0]->getPreOrderId());
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
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

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
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

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

    public function testOrderCustomerInfo()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

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
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

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
            $this->generateUrl('admin_order_search_customer_html'),
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
        $formData['OrderStatus'] = OrderStatus::PROCESSING; // 購入処理中で受注を登録する
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

        $this->markTestIncomplete('フロントとのセッション管理の実装が完了するまでスキップ');
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
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

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
            $tax = self::$container->get(TaxRuleService::class)->getTax($orderItem['price']);
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
        $formData = $this->createFormData($this->Customer, $this->Product);
        unset($formData['OrderStatus']);
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_new'),
            [
                'order' => $formData,
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

    public function testMailNoRFC()
    {
        $formData = $this->createFormData($this->Customer, $this->Product);
        // RFCに準拠していないメールアドレスを設定
        $formData['email'] = 'aa..@example.com';

        unset($formData['OrderStatus']);
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_new'),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        $savedOderId = preg_replace('/.*\/admin\/order\/(\d+)\/edit/', '$1', $url);
        $SavedOrder = $this->orderRepository->find($savedOderId);

        $this->assertNotNull($SavedOrder);
        $this->expected = $SavedOrder->getEmail();
        $this->actual = $formData['email'];
        $this->verify();
    }

    /**
     * お届け時間の指定を「指定なし」に変更できるかのテスト
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/4143
     */
    public function testUpdateShippingDeliveryTimeToNoneSpecified()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($this->Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $this->entityManager->flush($Order);

        $formData = $this->createFormData($this->Customer, $this->Product);
        // まずお届け時間に何か指定する(便宜上、最初に取得できたものを利用)
        $Delivery = $this->entityManager->getRepository(\Eccube\Entity\Delivery::class)->find($formData['Shipping']['Delivery']);
        $DeliveryTime = $Delivery->getDeliveryTimes()[0];
        $delivery_time_id = $DeliveryTime->getId();
        $delivery_time = $DeliveryTime->getDeliveryTime();
        $formData['Shipping']['DeliveryTime'] = $delivery_time_id;

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrder = $this->orderRepository->find($Order->getId());
        $EditedShipping = $EditedOrder->getShippings()[0];

        $this->expected = $delivery_time_id;
        $this->actual = $EditedShipping->getTimeId();
        $this->verify();
        $this->expected = $delivery_time;
        $this->actual = $EditedShipping->getShippingDeliveryTime();
        $this->verify();

        $formDataForEdit = $this->createFormDataForEdit($EditedOrder);
        // 「指定なし」に変更
        $formDataForEdit['Shipping']['DeliveryTime'] = null;

        // 管理画面で受注編集する
        $this->client->request(
            'POST', $this->generateUrl('admin_order_edit', ['id' => $Order->getId()]), [
            'order' => $formDataForEdit,
            'mode' => 'register',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $Order->getId()])));

        $EditedOrderafterEdit = $this->orderRepository->find($Order->getId());
        $EditedShippingafterEdit = $EditedOrderafterEdit->getShippings()[0];

        $this->expected = null;
        $this->actual = $EditedShippingafterEdit->getTimeId();
        $this->verify();
        $this->expected = null;
        $this->actual = $EditedShippingafterEdit->getShippingDeliveryTime();
        $this->verify();
    }

    /**
     * 受注管理で税率を変更できる
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/4269
     */
    public function testChangeOrderItemTaxRate()
    {
        /** @var RoundingType $RoundingType */
        $RoundingType = $this->entityManager->find(RoundingType::class, RoundingType::ROUND);
        /** @var Product $Product */
        $Product = $this->createProduct($this->Customer, 1);
        $this->entityManager->persist($Product);

        /** @var ProductClass $ProductClass */
        $ProductClass = $this->Product->getProductClasses()[0];
        $ProductClass->setPrice02(1000);
        $this->entityManager->persist($ProductClass);

        $TaxRule = new TaxRule();
        $TaxRule->setTaxRate(8)
            ->setTaxAdjust(0)
            ->setRoundingType($RoundingType)
            ->setProduct($Product)
            ->setProductClass($ProductClass)
            ->setApplyDate(new \DateTime('yesterday'));
        $this->entityManager->persist($TaxRule);

        $this->entityManager->flush();

        $formData = $this->createFormData($this->Customer, $this->Product);
        unset($formData['OrderStatus']);

        // 商品の税率を10%に変更
        $formData['OrderItems'][0]['tax_rate'] = '10';

        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_new'),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );

        $url = $crawler->filter('a')->text();
        $this->assertTrue($this->client->getResponse()->isRedirect($url));

        // 税率が10%で登録されている
        /** @var Order $Order */
        $Order = $this->orderRepository->findBy([], ['create_date' => 'DESC'])[0];
        self::assertEquals(10, $Order->getProductOrderItems()[0]->getTaxRate());
        self::assertEquals(100, $Order->getProductOrderItems()[0]->getTax());
    }

    public function testRoutingAdminOrderEditPostWithCustomerInfo()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));
        $Order->setSex(null);
        $Order->setJob(null);
        $Order->setBirth(null);

        $this->entityManager->flush($Order);

        $Customer->setSex($this->entityManager->find(Sex::class, 1));
        $Customer->setJob($this->entityManager->find(Job::class, 1));
        $Customer->setBirth(new \DateTime());
        $this->entityManager->flush($Customer);

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
        $this->assertNull($EditedOrder->getSex());
        $this->assertNull($EditedOrder->getJob());
        $this->assertNull($EditedOrder->getBirth());
    }
}
