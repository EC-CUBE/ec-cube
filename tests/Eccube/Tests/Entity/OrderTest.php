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

namespace Eccube\Tests\Entity;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Shipping;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\SaleTypeRepository;
use Eccube\Repository\TaxRuleRepository;
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
        $TaxRule = $this->container->get(TaxRuleRepository::class)->getByRule();
        $this->rate = $TaxRule->getTaxRate();
    }

    public function testConstructor()
    {
        $OrderStatus = $this->container->get(OrderStatusRepository::class)->find(OrderStatus::PROCESSING);
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
        $this->expected = [$this->container->get(SaleTypeRepository::class)->find(1)];
        $this->actual = $this->Order->getSaleTypes();
        $this->verify();
    }

    public function testGetTotalPrice()
    {
        $faker = $this->getFaker();
        /** @var Order $Order */
        $Order = $this->container->get(Generator::class)->createOrder(
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

            $OrderItem = new OrderItem();
            $OrderItem->setShipping($Shipping)
                ->setOrder($Order)
                ->setProduct($Product)
                ->setProductName('name')
                ->setPriceIncTax('1000')
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
}
