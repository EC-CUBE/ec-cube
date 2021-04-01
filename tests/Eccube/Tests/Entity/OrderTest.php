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
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Shipping;
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
    protected $rate;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $TaxRule = $this->entityManager->getRepository(\Eccube\Entity\TaxRule::class)->getByRule();
        $this->rate = $TaxRule->getTaxRate();
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
        $Order = self::$container->get(Generator::class)->createOrder(
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
        self::assertCount(6, $Order->getTaxableItems());
        /** @var OrderItem $Item */
        foreach ($Order->getTaxableItems() as $Item) {
            self::assertSame(TaxType::TAXATION, $Item->getTaxType()->getId());
        }
    }

    public function testGetTaxableTotal()
    {
        $Order = $this->createTestOrder();
        self::assertSame(436, $Order->getTaxableTotal());
    }

    public function testGetTaxableTotalByTaxRate()
    {
        $Order = $this->createTestOrder();
        self::assertArraySubset([10 => 220, 8 => 216], $Order->getTaxableTotalByTaxRate());
    }

    public function testGetTaxableDiscountItems()
    {
        $Order = $this->createTestOrder();
        self::assertCount(2, $Order->getTaxableDiscountItems());
    }

    public function testGetTaxableDiscount()
    {
        $Order = $this->createTestOrder();
        self::assertSame(-218, $Order->getTaxableDiscount());
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

    protected function createTestOrder()
    {
        $Taxation = $this->entityManager->find(TaxType::class, TaxType::TAXATION);
        $NonTaxable = $this->entityManager->find(TaxType::class, TaxType::NON_TAXABLE);
        $TaxExempt = $this->entityManager->find(TaxType::class, TaxType::TAX_EXEMPT);

        $ProductItem = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        $DiscountItem = $this->entityManager->find(OrderItemType::class, OrderItemType::DISCOUNT);

        // 非課税・不課税を覗いて、税率ごとに金額を集計する
        $data = [
            [$Taxation, 10, 100, 10, 1, $ProductItem],    // 商品明細
            [$Taxation, 10, 200, 20, 1, $ProductItem],    // 商品明細
            [$Taxation, 8, 100, 8, 1, $ProductItem],      // 商品明細
            [$Taxation, 8, 200, 16, 1, $ProductItem],     // 商品明細
            [$Taxation, 10, -100, -10, 1, $DiscountItem],  // 課税値引き
            [$Taxation, 8, -100, -8, 1, $DiscountItem],    // 課税値引き
            [$NonTaxable, 0, -10, 0, 1, $DiscountItem],    // 不課税明細、 集計対象外
            [$TaxExempt, 0, -10, 0, 1, $DiscountItem],     // 非課税明細、集計対象外
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
            $Order->addOrderItem($OrderItem);
        }

        return $Order;
    }
}
