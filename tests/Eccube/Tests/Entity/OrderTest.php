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

namespace Eccube\Tests\Entity;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Shipping;
use Eccube\Entity\TaxRule;
use Eccube\Service\TaxRuleService;
use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Fixture\Generator;

/**
 * AbstractEntity test cases.
 *
 * @author Kentaro Ohkouchi
 */
class OrderTest extends EccubeTestCase
{
    /** @var Customer */
    protected $Customer;
    /** @var Order */
    protected $Order;
    /** @var TaxRule */
    protected $TaxRule;
    /** @var int */
    protected $rate;
    /** @var TaxRuleService */
    protected $taxRuleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $this->TaxRule = $this->entityManager->getRepository(TaxRule::class)->getByRule();
        $this->rate = $this->TaxRule->getTaxRate();
        $this->taxRuleService = static::getContainer()->get(TaxRuleService::class);
    }

    public function testConstructor()
    {
        $OrderStatus = $this->entityManager->getRepository(\Eccube\Entity\Master\OrderStatus::class)->find(OrderStatus::PROCESSING);
        $Order = new Order($OrderStatus);

        $this->expected = 0;

        $this->actual = $Order->getDiscount();
        $this->verify();

        $this->actual = $Order->getSubTotal();
        $this->verify();

        $this->actual = $Order->getTotal();
        $this->verify();

        $this->actual = $Order->getPaymentTotal();
        $this->verify();

        $this->actual = $Order->getCharge();
        $this->verify();

        $this->actual = $Order->getTax();
        $this->verify();

        $this->actual = $Order->getDeliveryFeeTotal();
        $this->verify();

        $this->assertSame($OrderStatus, $Order->getOrderStatus());
    }

    public function testConstructor2()
    {
        $Order = new Order();

        $this->expected = 0;

        $this->actual = $Order->getDiscount();
        $this->verify();

        $this->actual = $Order->getSubTotal();
        $this->verify();

        $this->actual = $Order->getTotal();
        $this->verify();

        $this->actual = $Order->getPaymentTotal();
        $this->verify();

        $this->actual = $Order->getCharge();
        $this->verify();

        $this->actual = $Order->getTax();
        $this->verify();

        $this->actual = $Order->getDeliveryFeeTotal();
        $this->verify();

        $this->assertNull($Order->getOrderStatus());
    }

    public function testGetSaleTypes()
    {
        $this->expected = [$this->entityManager->getRepository(\Eccube\Entity\Master\SaleType::class)->find(1)];
        $this->actual = $this->Order->getSaleTypes();
        $this->verify();
    }

    public function testGetTotalPrice()
    {
        $faker = $this->getFaker();
        /** @var Order $Order */
        $Order = static::getContainer()->get(Generator::class)->createOrder(
            $this->Customer,
            [],
            null,
            $faker->randomNumber(5),
            $faker->randomNumber(5)
        );
        $this->expected = $Order->getSubTotal() + $Order->getCharge() + $Order->getDeliveryFeeTotal() - $Order->getDiscount();
        $this->actual = $Order->getTotalPrice();
        $this->verify();
    }

    public function testGetMergedProductOrderItems()
    {
        $quantity = '5';    // 配送先あたりの商品の個数
        $times = '2';       // 複数配送の配送先数
        // テストデータの準備
        $Product = new Product();
        $ProductClass = new ProductClass();
        $Order = new Order();
        $ItemProduct = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        foreach (range(1, $times) as $i) {
            $Shipping = new Shipping();
            $Shipping->setOrder($Order);
            $Order->addShipping($Shipping);
            $OrderItem = new OrderItem();
            $OrderItem->setShipping($Shipping)
                ->setOrder($Order)
                ->setProduct($Product)
                ->setProductName('name')
                ->setPrice('1000')
                ->setTax('0')
                ->setQuantity($quantity)
                ->setProductClass($ProductClass)
                ->setClassCategoryName1('name1')
                ->setClassCategoryName2('name2')
                ->setOrderItemType($ItemProduct)
            ;
            $Shipping->addOrderItem($OrderItem);
            $Order->addOrderItem($OrderItem);
        }
        // 実行
        $OrderItems = $Order->getMergedProductOrderItems();
        // 2個目の明細が1個にまとめられているか
        $this->expected = 1;
        $this->actual = count($OrderItems);
        $this->verify();
        // まとめられた明細の商品の個数が全配送先の合計になっているか
        $OrderItem = $OrderItems[0];
        $this->expected = $quantity * $times;
        $this->actual = $OrderItem->getQuantity();
        $this->verify();
    }

    public function testGetTaxableItems()
    {
        $Order = $this->createTestOrder();
        self::assertCount(7, $Order->getTaxableItems());
        /** @var OrderItem $Item */
        foreach ($Order->getTaxableItems() as $Item) {
            self::assertSame(TaxType::TAXATION, $Item->getTaxType()->getId());
        }
    }

    public function testGetTaxableTotal()
    {
        $Order = $this->createTestOrder();
        self::assertEquals(790187, $Order->getTaxableTotal());
    }

    public function testGetTaxableTotalByTaxRate()
    {
        $Order = $this->createTestOrder();
        self::assertEquals([10 => 724431, 8 => 65756], $Order->getTaxableTotalByTaxRate());
    }

    public function testGetTaxableDiscountItems()
    {
        $Order = $this->createTestOrder();
        self::assertCount(2, $Order->getTaxableDiscountItems());
    }

    public function testGetTaxableDiscount()
    {
        $Order = $this->createTestOrder();
        self::assertEquals(-94694, $Order->getTaxableDiscount());
    }

    public function testGetTaxFreeDiscountItems()
    {
        $Order = $this->createTestOrder();
        self::assertCount(2, $Order->getTaxFreeDiscountItems());
        /** @var OrderItem $Item */
        foreach ($Order->getTaxFreeDiscountItems() as $Item) {
            self::assertNotSame(TaxType::TAXATION, $Item->getTaxType()->getId());
        }
    }

    public function testGetTaxFreeDiscount()
    {
        $Order = $this->createTestOrder();

        self::assertSame(-7159, $Order->getTaxFreeDiscount());
    }

    public function testGetTotalByTaxRate()
    {
        $Order = $this->createTestOrder();

        self::assertSame(65160.0, $Order->getTotalByTaxRate()[8], '8%対象値引き後合計');
        self::assertSame(717868.0, $Order->getTotalByTaxRate()[10], '10%対象値引き後合計');
    }

    public function testGetTaxByTaxRate()
    {
        $Order = $this->createTestOrder();

        self::assertSame(4827.0, $Order->getTaxByTaxRate()[8], '8%対象値引き後消費税額');
        self::assertSame(65261.0, $Order->getTaxByTaxRate()[10], '10%対象値引き後消費税額');
    }

    protected function createTestOrder()
    {
        $Taxation = $this->entityManager->find(TaxType::class, TaxType::TAXATION);
        $NonTaxable = $this->entityManager->find(TaxType::class, TaxType::NON_TAXABLE);
        $TaxExempt = $this->entityManager->find(TaxType::class, TaxType::TAX_EXEMPT);

        $ProductItem = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        $DeliveryFee = $this->entityManager->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $Charge = $this->entityManager->find(OrderItemType::class, OrderItemType::CHARGE);
        $DiscountItem = $this->entityManager->find(OrderItemType::class, OrderItemType::DISCOUNT);

        $TaxIncluded = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $TaxExcluded = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);

        $RoundingType = $this->TaxRule->getRoundingType();

        // 税率ごとに金額を集計する
        $data = [
            [$Taxation, 10, 71141, round(71141 * (10/100)), 5, $ProductItem, $TaxExcluded, $RoundingType],    // 商品明細
            [$Taxation, 10, 92778, round(92778 * (10/100)), 4, $ProductItem, $TaxExcluded, $RoundingType],    // 商品明細
            [$Taxation, 8, 15221, round(15221 * (8/100)), 5, $ProductItem, $TaxExcluded, $RoundingType],      // 商品明細
            [$Taxation, 10, -71141, round(-71141 * (10/100)), 1, $DiscountItem, $TaxExcluded, $RoundingType],  // 課税値引き
            [$Taxation, 8, -15221, round(-15221 * (8/100)), 1, $DiscountItem, $TaxExcluded, $RoundingType],    // 課税値引き
            [$Taxation, 10, 1000, round(1000 * (10/100)), 1, $DeliveryFee, $TaxIncluded, $RoundingType],    // 送料
            [$Taxation, 10, 2187, round(1000 * (10/100)), 1, $Charge, $TaxIncluded, $RoundingType],    // 手数料
            [$NonTaxable, 0, -7000, 0, 1, $DiscountItem, $TaxIncluded, $RoundingType],    // 不課税明細
            [$TaxExempt, 0, -159, 0, 1, $DiscountItem, $TaxIncluded, $RoundingType],     // 非課税明細
        ];

        $Order = new Order();
        foreach ($data as $row) {
            $OrderItem = new OrderItem();
            $OrderItem->setTaxType($row[0]);
            $OrderItem->setTaxRate($row[1]);
            $OrderItem->setPrice($row[2]);
            $OrderItem->setTax($row[3]);
            $OrderItem->setQuantity($row[4]);
            $OrderItem->setOrderItemType($row[5]);
            $OrderItem->setTaxDisplayType($row[6]);
            $OrderItem->setRoundingType($row[7]);

            $Order->addOrderItem($OrderItem);
        }

        return $Order;
    }
}
