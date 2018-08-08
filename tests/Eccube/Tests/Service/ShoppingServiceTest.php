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

namespace Eccube\Tests\Service;

use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\Taxrule;
use Eccube\Entity\Shipping;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ShoppingServiceTest extends AbstractServiceTestCase
{
    protected $Customer;
    protected $CartService;
    protected $SaleType1;
    protected $SaleType2;

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->app['security.token_storage']->setToken(
            new UsernamePasswordToken(
                $this->Customer, null, 'Customer', $this->Customer->getRoles()
            )
        );
        $this->CartService = $this->app['eccube.service.cart'];
        $this->CartService->clear();
        $this->CartService->addProduct(1, 1);
        $this->CartService->save();

        $this->SaleType1 = $this->app['eccube.repository.master.sale_type']->find(1);
        $this->SaleType2 = $this->app['eccube.repository.master.sale_type']->find(2);
    }

    public function testCreateOrder()
    {
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $Order = $this->app['eccube.service.shopping']->createOrder($this->Customer);

        $this->expected = $this->Customer->getName01();
        $this->actual = $Order->getName01();
        $this->verify();
    }

    public function testGetOrder()
    {
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $Order = $this->app['eccube.service.shopping']->getOrder();

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetOrderWithMultiple()
    {
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $BaseInfo = $this->app['eccube.repository.base_info']->get();

        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $Order = $this->app['eccube.service.shopping']->getOrder();

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetOrderWithNonMember()
    {
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $BaseInfo = $this->app['eccube.repository.base_info']->get();

        $NonMember = $this->createNonMember();
        $this->app['security.token_storage']->setToken(
            new UsernamePasswordToken(
                $NonMember, null, 'Customer', ['IS_AUTHENTICATED_ANONYMOUSLY']
            )
        );

        $NewOrder = $this->app['eccube.service.shopping']->createOrder($NonMember);
        $Order = $this->app['eccube.service.shopping']->getOrder();

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetOrderWithStatusAndNull()
    {
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $this->app['orm.em']->flush();

        $OrderNew = $this->app['eccube.repository.order_status']->find(OrderStatus::NEW);
        $Order = $this->app['eccube.service.shopping']->getOrder($OrderNew);
        $this->assertNull($Order);
    }

    public function testGetOrderWithStatus()
    {
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $OrderProcessing = $this->app['eccube.repository.order_status']->find(OrderStatus::PROCESSING);
        $Order = $this->app['eccube.service.shopping']->getOrder($OrderProcessing);

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetNonMemberIsNull()
    {
        $Customer = $this->app['eccube.service.shopping']->getNonMember('eccube.front.shopping.nonmember');

        $this->assertNull($Customer);
    }

    public function testGetNonMember()
    {
        $email = 'test@example.net';
        $NonMember = $this->createNonMember($email);
        $Customer = $this->app['eccube.service.shopping']->getNonMember('eccube.front.shopping.nonmember');

        $this->expected = $email;
        $this->actual = $Customer->getEmail();
        $this->verify('セッションのメールアドレスが一致するか');

        $this->expected = $NonMember->getPref()->getId();
        $this->actual = $Customer->getPref()->getId();
        $this->verify('都道府県IDが一致するか');
    }

    public function testGetDeliveries()
    {
        $Deliveries = $this->app['eccube.service.shopping']->getDeliveries($this->SaleType1);

        $this->expected = 1;
        $this->actual = count($Deliveries);
        $this->verify();

        $this->expected = 1;
        $this->actual = $Deliveries[0]->getId();
        $this->verify();
    }

    public function testGetDeliveriesMultiple()
    {
        $Deliveries = $this->app['eccube.service.shopping']->getDeliveries(
            [$this->SaleType1, $this->SaleType2]);

        $this->expected = 2;
        $this->actual = count($Deliveries);
        $this->verify();
    }

    public function testCopyToShippingFromCustomerWithNull()
    {
        $Shipping = new Shipping();
        $Shipping->copyProperties($this->Customer);

        $this->expected = $Shipping;
        $this->actual = $this->app['eccube.service.shopping']->copyToShippingFromCustomer($Shipping, null);
        $this->verify();
    }

    public function testGetAmount()
    {
        $NewOrder = $this->createOrder($this->Customer);
        $Order = $this->app['eccube.service.shopping']->getAmount($NewOrder);

        $this->expected = $NewOrder->getTotal();
        $this->actual = $Order->getTotal();
        $this->verify();
    }

    public function testSetDeliveryFreeAmount()
    {
        // 送料無料条件を 0 円に設定
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setDeliveryFreeAmount(0);

        $Order = $this->createOrder($this->Customer);
        $Order->setDeliveryFeeTotal(100); // 送料 100 円に設定しておく
        $this->assertNotEquals(0, $Order->getDeliveryFeeTotal());

        // 送料 0 円に設定される
        $this->app['eccube.service.shopping']->setDeliveryFreeAmount($Order);

        $this->expected = 0;
        $this->actual = $Order->getDeliveryFeeTotal();
        $this->verify();
    }

    public function testSetDeliveryFreeQuantity()
    {
        self::markTestIncomplete('PurchaseFlowで集計は実行するためスキップ');

        // 送料無料条件を 0 個に設定
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setDeliveryFreeQuantity(0);

        $Order = $this->createOrder($this->Customer);
        $Order->setDeliveryFeeTotal(100); // 送料 100 円に設定しておく
        $this->assertNotEquals(0, $Order->getDeliveryFeeTotal());

        // 送料 0 円に設定される
        $this->app['eccube.service.shopping']->setDeliveryFreeQuantity($Order);

        $this->expected = 0;
        $this->actual = $Order->getDeliveryFeeTotal();
        $this->verify();
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/issues/1162
     */
    public function testGetDeliveriesMultipleShipping()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();

        // SaleType 1 と 2 で, 共通する支払い方法を削除しておく
        $PaymentOption = $this
            ->app['orm.em']
            ->getRepository('\Eccube\Entity\PaymentOption')
            ->findOneBy(
                [
                    'delivery_id' => 1,
                    'payment_id' => 3,
                ]
            );
        $this->assertNotNull($PaymentOption);
        $this->app['orm.em']->remove($PaymentOption);
        $this->app['orm.em']->flush();

        $Deliveries = $this->app['eccube.service.shopping']->getDeliveries(
            [$this->SaleType1, $this->SaleType2]);

        $this->expected = 0;
        $this->actual = count($Deliveries);
        $this->verify();
    }

    public function testSetOrderUpdate()
    {
        $Order = $this->createOrder($this->Customer);
        $data = [
            'shippings' => $Order->getShippings(),
            'message' => 'testtest',
        ];

        // $this->app['eccube.service.shopping']->setOrderUpdate($Order, $data);
        $this->app['eccube.service.shopping']->setFormData($Order, $data);
        $this->app['eccube.service.shopping']->setOrderUpdateData($Order);

        $this->expected = OrderStatus::NEW;
        $this->actual = $Order->getOrderStatus()->getId();
        $this->verify();

        $this->expected = 'testtest';
        $this->actual = $Order->getMessage();
        $this->verify();

        $this->assertNotNull($Order->getOrderDate());
    }

    public function testSetCustomerUpdate()
    {
        $Order = $this->createOrder($this->Customer);

        $this->app['eccube.service.shopping']->setCustomerUpdate(
            $Order,
            $this->Customer
        );

        $this->assertNotNull($this->Customer->getFirstBuyDate());
        $this->assertNotNull($this->Customer->getLastBuyDate());
        $this->assertNotNull($this->Customer->getBuyTimes());
        $this->assertNotNull($this->Customer->getBuyTotal());
    }

    public function testGetPayments()
    {
        $Payments = $this->app['eccube.repository.payment']->findAll();
        $data = [];
        foreach ($Payments as $Payment) {
            $data[] = ['id' => $Payment->getId()];
        }

        // TODO 境界値チェック
        $Pays = $this->app['eccube.service.shopping']->getPayments(
            $data,
            100000
        );

        $this->expected = count($Payments);
        $this->actual = count($Pays);
        $this->verify();
    }

    public function testGetFormDeliveryDurations()
    {
        $DeliveryDuration = $this->app['eccube.repository.delivery_duration']->find(1);
        $Order = $this->createOrder($this->Customer);
        foreach ($Order->getOrderItems() as $Item) {
            if (!$Item->isProduct()) {
                continue;
            }
            $Item->getProductClass()->setDeliveryDuration($DeliveryDuration);
        }
        $this->app['orm.em']->flush();

        $DeliveryDurations = $this->app['eccube.service.shopping']->getFormDeliveryDurations($Order);

        $this->expected = $this->app['config']['eccube_deliv_date_end_max'];
        $this->actual = count($DeliveryDurations);
        $this->verify();

        $dates = [];
        $today = new \DateTime();
        for ($i = 0; $i < $this->app['config']['eccube_deliv_date_end_max']; $i++) {
            $dates[$today->format('Y/m/d')] = $today->format('Y/m/d');
            $today->add(new \DateInterval('P1D'));
        }

        $this->expected = $dates;
        $this->actual = $DeliveryDurations;
        $this->verify();
    }

    /**
     * #1732 のテストケース
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1732
     */
    public function testGetFormDeliveryDurationsWithStockPending()
    {
        $DeliveryDuration1 = $this->app['eccube.repository.delivery_duration']->find(1);
        $DeliveryDuration9 = $this->app['eccube.repository.delivery_duration']->find(9);
        $Order = $this->createOrder($this->Customer);
        $i = 0;
        foreach ($Order->getOrderItems() as $Item) {
            if (!$Item->isProduct()) {
                continue;
            }
            if ($i === 0) {
                // 1件のみ「お取り寄せ」に設定する
                $Item->getProductClass()->setDeliveryDuration($DeliveryDuration9);
            } else {
                $Item->getProductClass()->setDeliveryDuration($DeliveryDuration1);
            }

            $i++;
        }
        $this->app['orm.em']->flush();

        $DeliveryDurations = $this->app['eccube.service.shopping']->getFormDeliveryDurations($Order);

        $this->expected = 0;
        $this->actual = count($DeliveryDurations);
        $this->verify('お取り寄せを含む場合はお届け日選択不可');
    }

    /**
     * #1238 のテストケース
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1238
     */
    public function testGetFormPayments()
    {
        self::markTestIncomplete('orderHelperで実行するためスキップ');

        $Delivery = $this->app['eccube.fixture.generator']->createDelivery();
        $Order = $this->app['eccube.fixture.generator']->createOrder($this->Customer, [], $Delivery);
        $Order->setSubTotal(2500);
        $this->app['orm.em']->flush($Order);

        $Payment1 = $this->createPayment($Delivery, 'スキップされる支払い方法', 0, 1000, 2000);
        $Payment2 = $this->createPayment($Delivery, '支払い方法2', 0, 2001, 3000);
        $Payment3 = $this->createPayment($Delivery, '支払い方法3', 0);
        $Payment4 = $this->createPayment($Delivery, '支払い方法4', 0);
        $Payment5 = $this->createPayment($Delivery, '支払い方法5', 0);
        $Payment6 = $this->createPayment($Delivery, '支払い方法6', 0);

        $Payments = $this->app['eccube.service.shopping']->getFormPayments([$Delivery], $Order);

        $this->expected = 5;
        $this->actual = count($Payments);
        $this->verify();
    }

    public function testGetFormPaymentsWithMultiple()
    {
        self::markTestIncomplete('orderHelperで実行するためスキップ');

        $BaseInfo = $this->app['eccube.repository.base_info']->get();

        $Delivery = $this->app['eccube.fixture.generator']->createDelivery();
        $Order = $this->app['eccube.fixture.generator']->createOrder($this->Customer, [], $Delivery);
        $Order->setSubTotal(2500);
        $this->app['orm.em']->flush($Order);

        $Payment1 = $this->createPayment($Delivery, 'スキップされる支払い方法', 0, 1000, 2000);
        $Payment2 = $this->createPayment($Delivery, '支払い方法2', 0, 2001, 3000);
        $Payment3 = $this->createPayment($Delivery, '支払い方法3', 0);
        $Payment4 = $this->createPayment($Delivery, '支払い方法4', 0);
        $Payment5 = $this->createPayment($Delivery, '支払い方法5', 0);
        $Payment6 = $this->createPayment($Delivery, '支払い方法6', 0);

        $Payments = $this->app['eccube.service.shopping']->getFormPayments([$Delivery], $Order);

        $this->expected = 5;
        $this->actual = count($Payments);
        $this->verify();
    }

    /**
     * #2005のテストケース
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/2005
     */
    public function testOrderItemForTaxRate()
    {
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $Product = $this->app['eccube.repository.product']->find(1);
        $ProductClasses = $Product->getProductClasses();

        foreach ($ProductClasses as $ProductClass) {
            $ProductClass->setPrice02(649);
        }
        $this->app['orm.em']->flush($Product);

        $this->CartService->setProductQuantity($Product->getId(), 1)->save();

        $Order = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule();

        $TaxRule->setTaxRate(Taxrule::FLOOR);
        $this->app['orm.em']->flush($TaxRule);

        // 受注明細で設定された金額
        foreach ($Order->getOrderItems() as $OrderItem) {
            $this->expected = ($OrderItem->getPrice() + $this->app['eccube.service.tax_rule']->calcTax($OrderItem->getPrice(), $OrderItem->getTaxRate(), $OrderItem->getTaxRule())) * $OrderItem->getQuantity();

            $this->actual = ($OrderItem->getPrice() + $this->app['eccube.service.tax_rule']->calcTax($OrderItem->getPrice(), $OrderItem->getTaxRate(), $TaxRule->getRoundingType()->getId())) * $OrderItem->getQuantity();

            $this->verify();
        }
    }
}
