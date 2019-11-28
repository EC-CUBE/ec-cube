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
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeeFreeByShippingPreprocessor;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeePreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class DeliveryFeeFreeByShippingProcessorTest extends EccubeTestCase
{
    /** @var OrderItemType */
    private $ProductType;

    private $DeliveryFeeType;

    /** @var BaseInfoRepository */
    private $baseInfoRepository;

    public function setUp()
    {
        parent::setUp();
        $this->ProductType = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        $this->DeliveryFeeType = $this->entityManager->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $this->baseInfoRepository = $this->entityManager->getRepository(\Eccube\Entity\BaseInfo::class);
    }

    /**
     * 送料無料条件が設定されていない場合
     */
    public function testWithoutDeliveryFreeSettings()
    {
        $this->newBaseInfo(0, 0);
        $processor = new DeliveryFeeFreeByShippingPreprocessor($this->baseInfoRepository);

        $Order = new Order();
        $Shipping = $this->newShipping(1);

        $Order->addOrderItem($this->newProductOrderItem(1000, 10, $Shipping));
        $DeliveryFee = $this->newDeliveryFeeItem(1000, $Shipping);

        $processor->process($Order, new PurchaseContext());

        self::assertEquals(1000, $DeliveryFee->getTotalPrice());
    }

    /**
     * 送料無料条件(金額)が設定されている場合
     *
     * @dataProvider deliveryFreeAmountProvider
     *
     * @param $amount int 受注金額
     * @param $expectedFee int 期待する送料
     */
    public function testWithDeliveryFreeAmount($amount, $expectedFee)
    {
        $this->newBaseInfo(1000, 0);
        $processor = new DeliveryFeeFreeByShippingPreprocessor($this->baseInfoRepository);

        $Shipping = $this->newShipping(1);
        $Order = new Order();
        $Shipping->setOrder($Order);
        $Order->addShipping($Shipping);
        $DeliveryFee = $this->newDeliveryFeeItem(1000, $Shipping);
        $Order->addOrderItem($DeliveryFee);
        $Order->addOrderItem($this->newProductOrderItem($amount, 1, $Shipping));

        $processor->process($Order, new PurchaseContext());

        self::assertEquals($expectedFee, $DeliveryFee->getTotalPrice());
    }

    public function deliveryFreeAmountProvider()
    {
        return [
            [1, 1000],
            [999, 1000],
            [1000, 0],
            [99999, 0],
        ];
    }

    /**
     * 送料無料条件(数量)が設定されている場合
     *
     * @dataProvider deliveryFreeQuantityProvider
     *
     * @param $quantity int 数量
     * @param $expectedFee int 期待する送料
     */
    public function testWithDeliveryFreeQuantity($quantity, $expectedFee)
    {
        $this->newBaseInfo(0, 10);
        $processor = new DeliveryFeeFreeByShippingPreprocessor($this->baseInfoRepository);

        $Shipping = $this->newShipping(1);
        $Order = new Order();
        $Shipping->setOrder($Order);
        $Order->addShipping($Shipping);
        $DeliveryFee = $this->newDeliveryFeeItem(1000, $Shipping);
        $Order->addOrderItem($DeliveryFee);
        $Order->addOrderItem($this->newProductOrderItem(1000, $quantity, $Shipping));

        $processor->process($Order, new PurchaseContext());

        self::assertEquals($expectedFee, $DeliveryFee->getTotalPrice());
    }

    public function deliveryFreeQuantityProvider()
    {
        return [
            [1, 1000],
            [9, 1000],
            [10, 0],
            [100, 0],
        ];
    }

    /**
     * 複数配送で送料無料条件(金額)が設定されている場合
     */
    public function testMultipleShippingWithDeliveryFreeAmount()
    {
        $this->newBaseInfo(1000, 0);
        $processor = new DeliveryFeeFreeByShippingPreprocessor($this->baseInfoRepository);
        $Shipping1 = $this->newShipping(1);
        $Shipping2 = $this->newShipping(2);

        $Order = new Order();

        $Shipping1->setOrder($Order);
        $Shipping2->setOrder($Order);
        $Order->addShipping($Shipping1);
        $Order->addShipping($Shipping2);

        $Order->addItem($this->newProductOrderItem(1000, 1, $Shipping1));
        $Shipping1DeliveryFee = $this->newDeliveryFeeItem(1000, $Shipping1);
        $Order->addItem($Shipping1DeliveryFee);

        $Order->addItem($this->newProductOrderItem(999, 1, $Shipping2));
        $Shipping2DeliveryFee = $this->newDeliveryFeeItem(1000, $Shipping2);
        $Order->addItem($Shipping2DeliveryFee);

        $processor->process($Order, new PurchaseContext());

        self::assertEquals(0, $Shipping1DeliveryFee->getTotalPrice());
        self::assertEquals(1000, $Shipping2DeliveryFee->getTotalPrice());
    }

    /**
     * 複数配送で送料無料条件(数量)が設定されている場合
     */
    public function testMultipleShippingWithDeliveryFreeQuantity()
    {
        $this->newBaseInfo(0, 5);
        $processor = new DeliveryFeeFreeByShippingPreprocessor($this->baseInfoRepository);
        $Shipping1 = $this->newShipping(1);
        $Shipping2 = $this->newShipping(2);

        $Order = new Order();

        $Shipping1->setOrder($Order);
        $Shipping2->setOrder($Order);
        $Order->addShipping($Shipping1);
        $Order->addShipping($Shipping2);

        $Order->addItem($this->newProductOrderItem(1000, 1, $Shipping1));
        $Shipping1DeliveryFee = $this->newDeliveryFeeItem(1000, $Shipping1);
        $Order->addItem($Shipping1DeliveryFee);

        $Order->addItem($this->newProductOrderItem(999, 5, $Shipping2));
        $Shipping2DeliveryFee = $this->newDeliveryFeeItem(1000, $Shipping2);
        $Order->addItem($Shipping2DeliveryFee);

        $processor->process($Order, new PurchaseContext());

        self::assertEquals(1000, $Shipping1DeliveryFee->getTotalPrice());
        self::assertEquals(0, $Shipping2DeliveryFee->getTotalPrice());
    }

    private function newBaseInfo($deliveryFeeAmount, $deliveryFeeQuantity)
    {
        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $BaseInfo->setDeliveryFreeAmount($deliveryFeeAmount);
        $BaseInfo->setDeliveryFreeQuantity($deliveryFeeQuantity);
        $this->entityManager->flush();

        return $BaseInfo;
    }

    private function newShipping($id)
    {
        $Shipping = new Shipping();
        $rc = new \ReflectionClass(Shipping::class);
        $prop = $rc->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($Shipping, $id);
        $Shipping->setName01("name_${id}");

        return $Shipping;
    }

    private function newProductOrderItem($price, $quantity, Shipping $Shipping)
    {
        $OrderItem = new OrderItem();
        $OrderItem->setOrderItemType($this->ProductType);
        $OrderItem->setPrice($price);
        $OrderItem->setQuantity($quantity);
        $OrderItem->setShipping($Shipping);
        $Shipping->addOrderItem($OrderItem);

        return $OrderItem;
    }

    private function newDeliveryFeeItem($fee, Shipping $Shipping)
    {
        $OrderItem = new OrderItem();
        $OrderItem->setOrderItemType($this->DeliveryFeeType);
        $OrderItem->setPrice($fee);
        $OrderItem->setQuantity(1);
        $OrderItem->setShipping($Shipping);
        $OrderItem->setProcessorName(DeliveryFeePreprocessor::class);
        $Shipping->addOrderItem($OrderItem);

        return $OrderItem;
    }
}
