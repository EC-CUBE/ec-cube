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

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeeFreeProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class DeliveryFeeFreeProcessorTest extends EccubeTestCase
{
    /**
     * @var DeliveryFeeFreeProcessor
     */
    protected $processor;

    /**
     * @var Order
     */
    protected $Order;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->processor = new DeliveryFeeFreeProcessor($this->BaseInfo);
        $this->Order = $this->createOrder($this->createCustomer());
    }

    public function testNewInstance()
    {
        self::assertInstanceOf(DeliveryFeeFreeProcessor::class, $this->processor);
    }

    /**
     * 送料無料条件に合致しない場合.
     */
    public function testProcess()
    {
        $result = $this->processor->process($this->Order, new PurchaseContext());
        self::assertInstanceOf(ProcessResult::class, $result);
        self::assertFalse($result->isError());

        $items = $this->getDeliveryFeeItems($this->Order);
        foreach ($items as $item) {
            self::assertEquals(1, $item->getQuantity());
        }
    }

    /**
     * 送料無料条件(金額)に合致する場合, 送料明細の個数は0になる.
     */
    public function testProcessWithAmount()
    {
        /** @var BaseInfo $BaseInfo */
        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $BaseInfo->setDeliveryFreeAmount(1); // 1円以上で送料無料

        $result = $this->processor->process($this->Order, new PurchaseContext());
        self::assertInstanceOf(ProcessResult::class, $result);
        self::assertFalse($result->isError());

        $items = $this->getDeliveryFeeItems($this->Order);
        foreach ($items as $item) {
            self::assertEquals(0, $item->getQuantity());
        }
    }

    /**
     * 送料無料条件(個数)に合致する場合, 送料明細の個数は0になる.
     */
    public function testProcessWithQuantity()
    {
        /** @var BaseInfo $BaseInfo */
        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $BaseInfo->setDeliveryFreeQuantity(1); // 1個以上で送料無料

        $result = $this->processor->process($this->Order, new PurchaseContext());
        self::assertInstanceOf(ProcessResult::class, $result);
        self::assertFalse($result->isError());

        $items = $this->getDeliveryFeeItems($this->Order);
        foreach ($items as $item) {
            self::assertEquals(0, $item->getQuantity());
        }
    }

    private function getDeliveryFeeItems(Order $Order)
    {
        $deliveryFeeItems = [];
        foreach ($Order->getOrderItems() as $item) {
            if ($item->isDeliveryFee()) {
                $deliveryFeeItems[] = $item;
            }
        }

        return $deliveryFeeItems;
    }
}
