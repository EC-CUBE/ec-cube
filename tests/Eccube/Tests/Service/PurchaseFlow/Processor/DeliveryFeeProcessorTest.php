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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeePreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Fixture\Generator;

class DeliveryFeeProcessorTest extends EccubeTestCase
{
    /** @var BaseInfoRepository */
    protected $BaseInfoRepository;
    /**
     * @var Product
     */
    protected $Product;

    /**
     * @var ProductClass
     */
    protected $ProductClass;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->BaseInfoRepository = $this->container->get(BaseInfoRepository::class);
        $this->Product = $this->createProduct('テスト商品', 1);
        $this->ProductClass = $this->Product->getProductClasses()[0];
    }

    public function testProcess()
    {
        $processor = $this->container->get(DeliveryFeePreprocessor::class);
        $Order = $this->createOrder($this->createCustomer());
        /*
         * @var OrderItem
         */
        foreach ($Order->getOrderItems() as $OrderItem) {
            if ($OrderItem->isDeliveryFee()) {
                $Order->getOrderItems()->removeElement($OrderItem);
            }
        }
        $processor->process($Order, new PurchaseContext());
        self::assertNotEmpty($this->getDeliveryFees($Order));
    }

    /**
     * すでに送料がある場合は送料を追加しない.
     */
    public function testProcessWithDeliveryFee()
    {
        $processor = $this->container->get(DeliveryFeePreprocessor::class);
        $Order = $this->createOrder($this->createCustomer());
        /*
         * @var OrderItem
         */
        foreach ($Order->getOrderItems() as $OrderItem) {
            if ($OrderItem->isDeliveryFee()) {
                $Order->getOrderItems()->removeElement($OrderItem);
            }
        }

        $DeliveryFee = new OrderItem();
        $OrderItemType = new OrderItemType();
        $OrderItemType->setId(OrderItemType::DELIVERY_FEE);
        $DeliveryFee->setOrderItemType($OrderItemType);
        $Order->addItem($DeliveryFee);

        $processor->process($Order, new PurchaseContext());

        $DeliveryFeeList = $this->getDeliveryFees($Order);
        self::assertCount(1, $DeliveryFeeList);
        self::assertSame($DeliveryFee, array_shift($DeliveryFeeList));
    }

    public function testProcessWithDeliveryFeePerProduct()
    {
        $BaseInfo = $this->BaseInfoRepository->get();
        $BaseInfo->setOptionProductDeliveryFee(true);
        $this->entityManager->persist($BaseInfo);
        $this->entityManager->flush($BaseInfo);
        $deliveryFee = 10000;
        $this->ProductClass->setDeliveryFee($deliveryFee);
        $this->entityManager->persist($this->ProductClass);
        $this->entityManager->flush($this->ProductClass);

        $processor = $this->container->get(DeliveryFeePreprocessor::class);
        /** @var Order $Order */
        $Order = $this->container->get(Generator::class)->createOrder($this->createCustomer(), [$this->ProductClass]);

        $quantity = 0;
        foreach ($Order->getOrderItems() as $orderItem) {
            if (!$orderItem->isProduct()) {
                continue;
            }
            $quantity += $orderItem->getQuantity();
        }

        /** @var OrderItem $DeliveryFee */
        $DeliveryFee = current($this->getDeliveryFees($Order));
        $deliveryOriginal = $DeliveryFee->getTotalPrice();

        foreach ($Order->getOrderItems() as $OrderItem) {
            if ($OrderItem->isDeliveryFee()) {
                $Order->getOrderItems()->removeElement($OrderItem);
            }
        }

        $processor->process($Order, new PurchaseContext());

        /** @var OrderItem $DeliveryFee */
        $DeliveryFee = current($this->getDeliveryFees($Order));
        $this->assertEquals($deliveryFee * $quantity + $deliveryOriginal, $DeliveryFee->getTotalPrice());
    }

    private function getDeliveryFees(Order $Order)
    {
        return array_filter($Order->getOrderItems()->toArray(), function ($OrderItem) {
            return $OrderItem->isDeliveryFee();
        });
    }
}
