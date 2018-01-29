<?php

namespace Eccube\Tests\Service;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Shipping;
use Eccube\Repository\DeliveryDurationRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\SaleTypeRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\CartService;
use Eccube\Service\ShoppingService;
use Eccube\Service\TaxRuleService;
use Eccube\Tests\Fixture\Generator;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ShoppingServiceTest extends AbstractServiceTestCase
{
    /**
     * @var  Customer
     */
    protected $Customer;

    /**
     * @var  CartService
     */
    protected $CartService;

    /**
     * @var  SaleType
     */
    protected $SaleType1;

    /**
     * @var  SaleType
     */
    protected $SaleType2;

    /**
     * @var  ShoppingService
     */
    protected $shoppingService;

    /**
     * @var  BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var  TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var  OrderStatusRepository
     */
    protected $orderStatusRepo;

    /**
     * @var  DeliveryDurationRepository
     */
    protected $deliveryDurationRepo;

    /**
     * @var  Generator
     */
    protected $generator;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->tokenStorage = $this->container->get('security.token_storage');
        $this->tokenStorage->setToken(
            new UsernamePasswordToken(
                $this->Customer, null, 'Customer', $this->Customer->getRoles()
            )
        );
        $this->CartService = $this->container->get(CartService::class);
        $this->CartService->clear();
        $this->CartService->addProduct(1, 1);
        $this->CartService->save();
        $this->SaleType1 = $this->container->get(SaleTypeRepository::class)->find(1);
        $this->SaleType2 = $this->container->get(SaleTypeRepository::class)->find(2);
        $this->shoppingService = $this->container->get(ShoppingService::class);
        $this->BaseInfo = $this->container->get(BaseInfo::class);
        $this->orderStatusRepo = $this->container->get(OrderStatusRepository::class);
        $this->deliveryDurationRepo = $this->container->get(DeliveryDurationRepository::class);
        $this->generator = $this->container->get(Generator::class);
    }

    public function testCreateOrder()
    {
        $this->markTestSkipped('addShipping was deprecated');
        $Order = $this->shoppingService->createOrder($this->Customer);

        $this->expected = $this->Customer->getName01();
        $this->actual = $Order->getName01();
        $this->verify();
    }

    public function testGetOrder()
    {
        $this->markTestSkipped('addShipping was deprecated');
        $NewOrder = $this->shoppingService->createOrder($this->Customer);
        $Order = $this->shoppingService->getOrder();

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetOrderWithMultiple()
    {
        $this->markTestSkipped('addShipping was deprecated');
        // 複数配送対応としておく
        $this->BaseInfo->setOptionMultipleShipping(true);

        $NewOrder = $this->shoppingService->createOrder($this->Customer);
        $Order = $this->shoppingService->getOrder();

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetOrderWithNonMember()
    {
        $this->markTestSkipped('addShipping was deprecated');
        // 複数配送対応としておく
        $this->BaseInfo->setOptionMultipleShipping(true);

        $NonMember = $this->createNonMember();
        $this->tokenStorage->setToken(
            new UsernamePasswordToken(
                $NonMember, null, 'Customer', array('IS_AUTHENTICATED_ANONYMOUSLY')
            )
        );

        $NewOrder = $this->shoppingService->createOrder($NonMember);
        $Order = $this->shoppingService->getOrder();

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();
    }

    public function testGetOrderWithStatusAndNull()
    {
        $this->markTestSkipped('addShipping was deprecated');
        $this->shoppingService->createOrder($this->Customer);
        $this->entityManager->flush();

        $OrderNew = $this->orderStatusRepo->find(OrderStatus::NEW);
        $Order = $this->shoppingService->getOrder($OrderNew);
        $this->assertNull($Order);
    }

    public function testGetOrderWithStatus()
    {
        $this->markTestSkipped('addShipping was deprecated');
        $NewOrder = $this->shoppingService->createOrder($this->Customer);
        $OrderProcessing = $this->orderStatusRepo->find(OrderStatus::PROCESSING);
        $Order = $this->shoppingService->getOrder($OrderProcessing);

        $this->expected = $NewOrder->getPreOrderId();
        $this->actual = $Order->getPreOrderId();
        $this->verify();

    }

    public function testGetNonMemberIsNull()
    {
        $Customer = $this->shoppingService->getNonMember('eccube.front.shopping.nonmember');

        $this->assertNull($Customer);
    }

    public function testGetNonMember()
    {
        $email = 'test@example.net';
        $NonMember = $this->createNonMember($email);
        $Customer = $this->shoppingService->getNonMember('eccube.front.shopping.nonmember');

        $this->expected = $email;
        $this->actual = $Customer->getEmail();
        $this->verify('セッションのメールアドレスが一致するか');

        $this->expected = $NonMember->getPref()->getId();
        $this->actual = $Customer->getPref()->getId();
        $this->verify('都道府県IDが一致するか');
    }

    public function testGetDeliveries()
    {
        $Deliveries = $this->shoppingService->getDeliveries($this->SaleType1);

        $this->expected = 1;
        $this->actual = count($Deliveries);
        $this->verify();

        $this->expected = 1;
        $this->actual = $Deliveries[0]->getId();
        $this->verify();
    }

    public function testGetDeliveriesMultiple()
    {
        $Deliveries = $this->shoppingService->getDeliveries(array($this->SaleType1, $this->SaleType2));

        $this->expected = 2;
        $this->actual = count($Deliveries);
        $this->verify();
    }

    public function testCopyToShippingFromCustomerWithNull()
    {
        $Shipping = new Shipping();
        $Shipping->copyProperties($this->Customer);

        $this->expected = $Shipping;
        $this->actual = $this->shoppingService->copyToShippingFromCustomer($Shipping, null);
        $this->verify();
    }

    public function testGetAmount()
    {
        $NewOrder = $this->createOrder($this->Customer);
        $Order = $this->shoppingService->getAmount($NewOrder);

        $this->expected = $NewOrder->getTotal();
        $this->actual = $Order->getTotal();
        $this->verify();
    }

    public function testSetDeliveryFreeAmount()
    {
        // 送料無料条件を 0 円に設定
        $this->BaseInfo->setDeliveryFreeAmount(0);

        $Order = $this->createOrder($this->Customer);
        $Order->setDeliveryFeeTotal(100); // 送料 100 円に設定しておく
        $this->assertNotEquals(0, $Order->getDeliveryFeeTotal());

        // 送料 0 円に設定される
        $this->shoppingService->setDeliveryFreeAmount($Order);

        $this->expected = 0;
        $this->actual = $Order->getDeliveryFeeTotal();
        $this->verify();
    }

    public function testSetDeliveryFreeQuantity()
    {
        // 送料無料条件を 0 個に設定
        $this->BaseInfo->setDeliveryFreeQuantity(0);

        $Order = $this->createOrder($this->Customer);
        $Order->setDeliveryFeeTotal(100); // 送料 100 円に設定しておく
        $this->assertNotEquals(0, $Order->getDeliveryFeeTotal());

        // 送料 0 円に設定される
        $this->shoppingService->setDeliveryFreeQuantity($Order);

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
        $this->BaseInfo->setOptionMultipleShipping(true);

        // SaleType 1 と 2 で, 共通する支払い方法を削除しておく
        $PaymentOption = $this
            ->entityManager
            ->getRepository('\Eccube\Entity\PaymentOption')
            ->findOneBy(
                array(
                    'delivery_id' => 1,
                    'payment_id' => 3
                )
            );
        $this->assertNotNull($PaymentOption);
        $this->entityManager->remove($PaymentOption);
        $this->entityManager->flush();

        $Deliveries = $this->shoppingService->getDeliveries(array($this->SaleType1, $this->SaleType2));

        $this->expected = 0;
        $this->actual = count($Deliveries);
        $this->verify();
    }

    public function testSetOrderUpdate()
    {
        $Order = $this->createOrder($this->Customer);
        $data = array(
            'shippings' => $Order->getShippings(),
            'message' => 'testtest'
        );

        // $this->shoppingService->setOrderUpdate($Order, $data);
        $this->shoppingService->setFormData($Order, $data);
        $this->shoppingService->setOrderUpdateData($Order);

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

        $this->shoppingService->setCustomerUpdate(
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
        $Payments = $this->container->get(PaymentRepository::class)->findAll();
        $data = array();
        foreach ($Payments as $Payment) {
            $data[] = array('id' => $Payment->getId());
        }

        // TODO 境界値チェック
        $Pays = $this->shoppingService->getPayments(
            $data,
            100000
        );

        $this->expected = count($Payments);
        $this->actual = count($Pays);
        $this->verify();
    }

    public function testGetFormDeliveryDurations()
    {
        $DeliveryDuration = $this->deliveryDurationRepo->find(1);
        $Order = $this->createOrder($this->Customer);
        foreach ($Order->getOrderItems() as $Item) {
            if (!$Item->isProduct()) {
                continue;
            }
            $Item->getProductClass()->setDeliveryDuration($DeliveryDuration);
        }
        $this->entityManager->flush();

        $DeliveryDurations = $this->shoppingService->getFormDeliveryDurations($Order);

        $this->expected = $this->eccubeConfig['deliv_date_end_max'];
        $this->actual = count($DeliveryDurations);
        $this->verify();

        $dates = array();
        $today = new \DateTime();
        for ($i = 0; $i < $this->eccubeConfig['deliv_date_end_max']; $i++) {
            $dates[$today->format('Y/m/d')] = $today->format('Y/m/d');
            $today->add(new \DateInterval('P1D'));
        }

        $this->expected = $dates;
        $this->actual = $DeliveryDurations;
        $this->verify();
    }

    /**
     * #1732 のテストケース
     * @link https://github.com/EC-CUBE/ec-cube/issues/1732
     */
    public function testGetFormDeliveryDurationsWithStockPending()
    {
        $DeliveryDuration1 = $this->deliveryDurationRepo->find(1);
        $DeliveryDuration9 = $this->deliveryDurationRepo->find(9);
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
        $this->entityManager->flush();

        $DeliveryDurations = $this->shoppingService->getFormDeliveryDurations($Order);

        $this->expected = 0;
        $this->actual = count($DeliveryDurations);
        $this->verify('お取り寄せを含む場合はお届け日選択不可');
    }

    /**
     * #1238 のテストケース
     * @link https://github.com/EC-CUBE/ec-cube/issues/1238
     */
    public function testGetFormPayments()
    {
        $Delivery = $this->generator->createDelivery();
        $Order = $this->generator->createOrder($this->Customer, array(), $Delivery);
        $Order->setSubTotal(2500);
        $this->entityManager->flush();

        $this->createPayment($Delivery, 'スキップされる支払い方法', 0, 1000, 2000);
        $this->createPayment($Delivery, '支払い方法2', 0, 2001, 3000);
        $this->createPayment($Delivery, '支払い方法3', 0);
        $this->createPayment($Delivery, '支払い方法4', 0);
        $this->createPayment($Delivery, '支払い方法5', 0);
        $this->createPayment($Delivery, '支払い方法6', 0);

        $Payments = $this->shoppingService->getFormPayments(array($Delivery), $Order);

        $this->expected = 5;
        $this->actual = count($Payments);
        $this->verify();
    }

    public function testGetFormPaymentsWithMultiple()
    {
        // 複数配送対応としておく
        $this->BaseInfo->setOptionMultipleShipping(true);

        $Delivery = $this->generator->createDelivery();
        $Order = $this->generator->createOrder($this->Customer, array(), $Delivery);
        $Order->setSubTotal(2500);
        $this->entityManager->flush();

        $this->createPayment($Delivery, 'スキップされる支払い方法', 0, 1000, 2000);
        $this->createPayment($Delivery, '支払い方法2', 0, 2001, 3000);
        $this->createPayment($Delivery, '支払い方法3', 0);
        $this->createPayment($Delivery, '支払い方法4', 0);
        $this->createPayment($Delivery, '支払い方法5', 0);
        $this->createPayment($Delivery, '支払い方法6', 0);

        $Payments = $this->shoppingService->getFormPayments(array($Delivery), $Order);

        $this->expected = 5;
        $this->actual = count($Payments);
        $this->verify();
    }

    /**
     * #2005のテストケース
     * @link https://github.com/EC-CUBE/ec-cube/issues/2005
     */
    public function testOrderItemForTaxRate()
    {
        $this->markTestSkipped('addShipping was deprecated');
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses();

        foreach ($ProductClasses as $ProductClass) {
            $ProductClass->setPrice02(649);
        }
        $this->entityManager->flush();

        $this->CartService->addProduct($Product->getProductClasses()->first()->getId(), 1);
        $this->CartService->save();
        $this->CartService->lock();

        $Order = $this->shoppingService->createOrder($this->Customer);
        $TaxRule = $this->container->get(TaxRuleRepository::class)->getByRule();

        $TaxRule->setTaxRate(10);
        $this->entityManager->flush();

        // 受注明細で設定された金額
        foreach ($Order->getOrderItems() as $OrderItem) {

            $this->expected = ($OrderItem->getPrice() + $this->container->get(TaxRuleService::class)->calcTax($OrderItem->getPrice(), $OrderItem->getTaxRate(), $OrderItem->getTaxRule())) * $OrderItem->getQuantity();

            $this->actual = ($OrderItem->getPrice() + $this->container->get(TaxRuleService::class)->calcTax($OrderItem->getPrice(), $OrderItem->getTaxRate(), $TaxRule->getRoundingType()->getId())) * $OrderItem->getQuantity();

            $this->verify();
        }

    }
}
