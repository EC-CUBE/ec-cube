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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Order;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeeFreePreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class DeliveryFeeFreeProcessorTest extends EccubeTestCase
{
    /**
     * @var DeliveryFeeFreePreprocessor
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

    /** @var BaseInfoRepository */
    protected $baseInfoRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->baseInfoRepository = $this->entityManager->getRepository(\Eccube\Entity\BaseInfo::class);
        $this->processor = new DeliveryFeeFreePreprocessor($this->baseInfoRepository);
        $this->Order = $this->createOrder($this->createCustomer());
    }

    public function testNewInstance()
    {
        self::assertInstanceOf(DeliveryFeeFreePreprocessor::class, $this->processor);
    }

    /**
     * 送料無料条件に合致しない場合.
     */
    public function testProcess()
    {
        $this->processor->process($this->Order, new PurchaseContext());

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

        $this->processor->process($this->Order, new PurchaseContext());

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

        $this->processor->process($this->Order, new PurchaseContext());

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
