<?php

namespace Eccube\Tests\Service;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Shipping;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ShoppingServiceTest extends AbstractServiceTestCase
{

    protected $Customer;
    protected $CartService;
    protected $ProductType1;
    protected $ProductType2;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->app['security']->setToken(
            new UsernamePasswordToken(
                $this->Customer, null, 'Customer', $this->Customer->getRoles()
            )
        );
        $this->CartService = $this->app['eccube.service.cart'];
        $this->CartService->setProductQuantity(1, 1)
            ->save();

        $this->ProductType1 = $this->app['eccube.repository.master.product_type']->find(1);
        $this->ProductType2 = $this->app['eccube.repository.master.product_type']->find(2);
    }

    public function testCreateOrder()
    {
        $Order = $this->app['eccube.service.shopping']->createOrder($this->Customer);

        $this->expected = $this->Customer->getName01();
        $this->actual = $Order->getName01();
        $this->verify();
    }

    public function testGetOrder()
    {
        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $Order = $this->app['eccube.service.shopping']->getOrder();

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetOrderWithMultiple()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $Order = $this->app['eccube.service.shopping']->getOrder();

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetOrderWithNonMember()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        $NonMember = $this->createNonMember();
        $this->app['security']->setToken(
            new UsernamePasswordToken(
                $NonMember, null, 'Customer', array('IS_AUTHENTICATED_ANONYMOUSLY')
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
        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $this->app['orm.em']->flush();

        $OrderNew = $this->app['eccube.repository.order_status']->find($this->app['config']['order_new']);
        $Order = $this->app['eccube.service.shopping']->getOrder($OrderNew);
        $this->assertNull($Order);
    }

    public function testGetOrderWithStatus()
    {
        $NewOrder = $this->app['eccube.service.shopping']->createOrder($this->Customer);
        $OrderProcessing = $this->app['eccube.repository.order_status']->find($this->app['config']['order_processing']);
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
        $Deliveries = $this->app['eccube.service.shopping']->getDeliveries($this->ProductType1);

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
            array($this->ProductType1, $this->ProductType2));

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
     * @link https://github.com/EC-CUBE/ec-cube/issues/1162
     */
    public function testGetDeliveriesMultipleShipping()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        // ProductType 1 と 2 で, 共通する支払い方法を削除しておく
        $PaymentOption = $this
            ->app['orm.em']
            ->getRepository('\Eccube\Entity\PaymentOption')
            ->findOneBy(
                array(
                    'delivery_id' => 1,
                    'payment_id' => 3
                )
            );
        $this->assertNotNull($PaymentOption);
        $this->app['orm.em']->remove($PaymentOption);
        $this->app['orm.em']->flush();

        $Deliveries = $this->app['eccube.service.shopping']->getDeliveries(
            array($this->ProductType1, $this->ProductType2));

        $this->expected = 0;
        $this->actual = count($Deliveries);
        $this->verify();
    }

    public function testIsOrderProduct()
    {
        $Order = $this->createOrder($this->Customer);
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_SHOW);

        // 商品を購入可能な状態に設定
        foreach ($Order->getOrderDetails() as $Detail) {
            $Detail->getProduct()->setStatus($Disp);
            $Detail->getProductClass()->setSaleLimit(100);
            $Detail->setQuantity(2);
            $Detail->getProductClass()->setStockUnlimited(Constant::ENABLED);
        }
        $this->app['orm.em']->flush();

        $this->expected = true;
        $this->actual = $this->app['eccube.service.shopping']->isOrderProduct(
            $this->app['orm.em'],
            $Order
        );

        $this->verify();
    }

    public function testIsOrderProductWithHide()
    {
        $Order = $this->createOrder($this->Customer);
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);

        // 商品を非表示に設定
        foreach ($Order->getOrderDetails() as $Detail) {
            $Detail->getProduct()->setStatus($Disp);
            break;
        }
        $this->app['orm.em']->flush();

        $this->expected = false;
        $this->actual = $this->app['eccube.service.shopping']->isOrderProduct(
            $this->app['orm.em'],
            $Order
        );
        $this->verify();
    }

    public function testIsOrderProductWithSaleLimit()
    {
        $Order = $this->createOrder($this->Customer);
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_SHOW);
        foreach ($Order->getOrderDetails() as $Detail) {
            $Detail->getProduct()->setStatus($Disp);
        }

        // 販売制限1, 注文数量2 に設定
        foreach ($Order->getOrderDetails() as $Detail) {
            $Detail->getProductClass()->setSaleLimit(1);
            $Detail->setQuantity(2);
            break;
        }
        $this->app['orm.em']->flush();

        $this->expected = false;
        $this->actual = $this->app['eccube.service.shopping']->isOrderProduct(
            $this->app['orm.em'],
            $Order
        );
        $this->verify();
    }

    public function testIsOrderProductWithStock()
    {
        $Order = $this->createOrder($this->Customer);
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_SHOW);
        foreach ($Order->getOrderDetails() as $Detail) {
            $Detail->getProduct()->setStatus($Disp);
            $Detail->getProductClass()->setStockUnlimited(Constant::DISABLED);
            $productStock = $this->app['orm.em']->getRepository('Eccube\Entity\ProductStock')->find(
                $Detail->getProductClass()->getProductStock()->getId()
            );
            $productStock->setStock(1);
        }

        // 在庫1, 注文数量2 に設定
        foreach ($Order->getOrderDetails() as $Detail) {
            $Detail->setQuantity(2);
            break;
        }
        $this->app['orm.em']->flush();

        $this->expected = false;
        $this->actual = $this->app['eccube.service.shopping']->isOrderProduct(
            $this->app['orm.em'],
            $Order
        );
        $this->verify();
    }

    public function testSetOrderUpdate()
    {
        $Order = $this->createOrder($this->Customer);
        $data = array(
            'shippings' => $Order->getShippings(),
            'message' => 'testtest'
        );

        // $this->app['eccube.service.shopping']->setOrderUpdate($Order, $data);
        $this->app['eccube.service.shopping']->setFormData($Order, $data);
        $this->app['eccube.service.shopping']->setOrderUpdateData($Order);

        $this->expected = $this->app['config']['order_new'];
        $this->actual = $Order->getOrderStatus()->getId();
        $this->verify();

        $this->expected = 'testtest';
        $this->actual = $Order->getMessage();
        $this->verify();

        $this->assertNotNull($Order->getOrderDate());
    }

    public function testSetStockUpdate()
    {
        $Order = $this->createOrder($this->Customer);
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_SHOW);
        foreach ($Order->getOrderDetails() as $Detail) {
            $Detail->getProduct()->setStatus($Disp);
            $Detail->getProductClass()->setStockUnlimited(Constant::DISABLED);
            $productStock = $this->app['orm.em']->getRepository('Eccube\Entity\ProductStock')->find(
                $Detail->getProductClass()->getProductStock()->getId()
            );
            $productStock->setStock(5);
        }

        // 在庫5, 注文数量2 に設定
        foreach ($Order->getOrderDetails() as $Detail) {
            $Detail->setQuantity(2);
        }
        $this->app['orm.em']->flush();

        $this->app['eccube.service.shopping']->setStockUpdate(
            $this->app['orm.em'],
            $Order
        );
        $this->app['orm.em']->flush();

        foreach ($Order->getOrderDetails() as $Detail) {
            // ProductClass を取得し直して, 在庫を比較
            $ProductClass = $this->app['eccube.repository.product_class']->find($Detail->getProductClass()->getId());

            $this->expected = 3;
            $this->actual = $ProductClass->getStock();
            $this->verify();
        }
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
        $data = array();
        foreach ($Payments as $Payment) {
            $data[] = array('id' => $Payment->getId());
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

    public function testGetFormDeliveryDates()
    {
        $DeliveryDate = $this->app['eccube.repository.delivery_date']->find(1);
        $Order = $this->createOrder($this->Customer);
        foreach ($Order->getOrderDetails() as $Detail) {
            $Detail->getProductClass()->setDeliveryDate($DeliveryDate);
        }
        $this->app['orm.em']->flush();

        $DeliveryDates = $this->app['eccube.service.shopping']->getFormDeliveryDates($Order);

        $this->expected = $this->app['config']['deliv_date_end_max'];
        $this->actual = count($DeliveryDates);
        $this->verify();

        $dates = array();
        $today = new \DateTime();
        for ($i = 0; $i < $this->app['config']['deliv_date_end_max']; $i++) {
            $dates[$today->format('Y/m/d')] = $today->format('Y/m/d');
            $today->add(new \DateInterval('P1D'));
        }

        $this->expected = $dates;
        $this->actual = $DeliveryDates;
        $this->verify();
    }

    /**
     * #1732 のテストケース
     * @link https://github.com/EC-CUBE/ec-cube/issues/1732
     */
    public function testGetFormDeliveryDatesWithStockPending()
    {
        $DeliveryDate1 = $this->app['eccube.repository.delivery_date']->find(1);
        $DeliveryDate9 = $this->app['eccube.repository.delivery_date']->find(9);
        $Order = $this->createOrder($this->Customer);
        $i = 0;
        foreach ($Order->getOrderDetails() as $Detail) {
            if ($i === 0) {
                // 1件のみ「お取り寄せ」に設定する
                $Detail->getProductClass()->setDeliveryDate($DeliveryDate9);
            } else {
                $Detail->getProductClass()->setDeliveryDate($DeliveryDate1);
            }

            $i++;
        }
        $this->app['orm.em']->flush();

        $DeliveryDates = $this->app['eccube.service.shopping']->getFormDeliveryDates($Order);

        $this->expected = 0;
        $this->actual = count($DeliveryDates);
        $this->verify('お取り寄せを含む場合はお届け日選択不可');
    }

    /**
     * #1238 のテストケース
     * @link https://github.com/EC-CUBE/ec-cube/issues/1238
     */
    public function testGetFormPayments()
    {
        $Delivery = $this->app['eccube.fixture.generator']->createDelivery();
        $Order = $this->app['eccube.fixture.generator']->createOrder($this->Customer, array(), $Delivery);
        $Order->setSubTotal(2500);
        $this->app['orm.em']->flush($Order);

        $Payment1 = $this->createPayment($Delivery, 'スキップされる支払い方法', 0, 1000, 2000);
        $Payment2 = $this->createPayment($Delivery, '支払い方法2', 0, 2001, 3000);
        $Payment3 = $this->createPayment($Delivery, '支払い方法3', 0);
        $Payment4 = $this->createPayment($Delivery, '支払い方法4', 0);
        $Payment5 = $this->createPayment($Delivery, '支払い方法5', 0);
        $Payment6 = $this->createPayment($Delivery, '支払い方法6', 0);

        $Payments = $this->app['eccube.service.shopping']->getFormPayments(array($Delivery), $Order);

        $this->expected = 5;
        $this->actual = count($Payments);
        $this->verify();
    }

    public function testGetFormPaymentsWithMultiple()
    {
        // 複数配送対応としておく
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionMultipleShipping(Constant::ENABLED);

        $Delivery = $this->app['eccube.fixture.generator']->createDelivery();
        $Order = $this->app['eccube.fixture.generator']->createOrder($this->Customer, array(), $Delivery);
        $Order->setSubTotal(2500);
        $this->app['orm.em']->flush($Order);

        $Payment1 = $this->createPayment($Delivery, 'スキップされる支払い方法', 0, 1000, 2000);
        $Payment2 = $this->createPayment($Delivery, '支払い方法2', 0, 2001, 3000);
        $Payment3 = $this->createPayment($Delivery, '支払い方法3', 0);
        $Payment4 = $this->createPayment($Delivery, '支払い方法4', 0);
        $Payment5 = $this->createPayment($Delivery, '支払い方法5', 0);
        $Payment6 = $this->createPayment($Delivery, '支払い方法6', 0);

        $Payments = $this->app['eccube.service.shopping']->getFormPayments(array($Delivery), $Order);

        $this->expected = 5;
        $this->actual = count($Payments);
        $this->verify();
    }

    /**
     * #1739のテストケース
     * @link https://github.com/EC-CUBE/ec-cube/issues/1739
     */
    public function testGetNewOrderDetailForTaxRate()
    {
        $DefaultTaxRule = $this->app['eccube.repository.tax_rule']->find(1);
        $DefaultTaxRule->setApplyDate(new \DateTime('-2 day'));
        $this->app['orm.em']->flush();

        // 個別税率設定を有効化
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionProductTaxRule(Constant::ENABLED);
        // 個別税率が設定された商品企画を準備
        $Product = $this->createProduct('テスト商品', 1);
        $ProductClassList = $Product->getProductClasses();
        $ProductClass = $ProductClassList[0];
        $CalcRule = $this->app['orm.em']
            ->getRepository('Eccube\Entity\Master\Taxrule')
            ->find(1);
        $TaxRule = new \Eccube\Entity\TaxRule();
        $TaxRule->setProductClass($ProductClass)
            ->setCreator($Product->getCreator())
            ->setProduct($Product)
            ->setCalcRule($CalcRule)
            ->setTaxRate(10)
            ->setTaxAdjust(0)
            ->setApplyDate(new \DateTime('-1 days')) // nowだとタイミングによってはテストが失敗する
            ->setDelFlg(Constant::DISABLED);
        $ProductClass->setTaxRule($TaxRule)
            ->setTaxRate($TaxRule->getTaxRate());

        $this->app['orm.em']->persist($TaxRule);
        $this->app['orm.em']->flush();

        // テスト用に税率設定のキャッシュをクリア
        $this->app['eccube.repository.tax_rule']->clearCache();

        // ShoppingServiceにテスト用のEntityManagerを設定
        $ShoppingService = $this->app['eccube.service.shopping'];
        $RefrectionClass = new \ReflectionClass(get_class($ShoppingService));
        $Property = $RefrectionClass->getProperty('em');
        $Property->setAccessible(true);
        $Property->setValue($ShoppingService, $this->app['orm.em']);

        $OrderDetail = $this->app['eccube.service.shopping']->getNewOrderDetail($Product, $ProductClass, 1);

        $this->expected = $TaxRule->getId();
        $this->actual = $OrderDetail->getTaxRule();
        $this->verify('受注詳細の税率が正しく設定されている');
    }
}
