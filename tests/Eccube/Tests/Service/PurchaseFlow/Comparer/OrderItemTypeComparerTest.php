<?php

namespace Eccube\Tests\Service\PurchaseFlow\Comparer;

use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\ShipmentItem;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Service\PurchaseFlow\Comparer\OrderItemTypeComparer;
use Eccube\Service\PurchaseFlow\Comparer\PriceComparer;
use Eccube\Service\PurchaseFlow\Comparer\TaxComparer;
use Eccube\Tests\EccubeTestCase;

class OrderItemTypeComparerTest extends EccubeTestCase
{
    /**
     * @var ShipmentItem
     */
    protected $Item1;

    /**
     * @var ShipmentItem
     */
    protected $Item2;

    /**
     * @var OrderItemType
     */
    protected $TypeProduct;

    /**
     * @var OrderItemType
     */
    protected $TypeDeliveryFee;

    /**
     * @var OrderItemType
     */
    protected $TypeCharge;

    /**
     * @var OrderItemType
     */
    protected $TypeDiscount;

    /**
     * @var OrderItemType
     */
    protected $TypeTax;

    /**
     * @var OrderItemTypeComparer
     */
    protected $comparer;

    public function setUp()
    {
        parent::setUp();

        $this->comparer = new OrderItemTypeComparer();

        $this->Item1 = new ShipmentItem();
        $this->Item2 = new ShipmentItem();

        /** @var OrderItemTypeRepository $repository */
        $repository = $this->app['eccube.repository.master.order_item_type'];
        $this->TypeProduct = $repository->find(OrderItemType::PRODUCT);
        $this->TypeDeliveryFee = $repository->find(OrderItemType::DELIVERY_FEE);
        $this->TypeCharge = $repository->find(OrderItemType::CHARGE);
        $this->TypeDiscount = $repository->find(OrderItemType::DISCOUNT);
        $this->TypeTax = $repository->find(OrderItemType::TAX);
    }

    public function testCompare()
    {
        $this->Item1->setOrderItemType($this->TypeProduct);
        $this->Item2->setOrderItemType($this->TypeProduct);

        $this->assertTrue($this->comparer->compare($this->Item1, $this->Item2));
    }

    public function testCompare_notEqual()
    {
        $this->Item1->setOrderItemType($this->TypeProduct);
        $this->Item2->setOrderItemType($this->TypeDiscount);

        $this->assertFalse($this->comparer->compare($this->Item1, $this->Item2));
    }

    public function testCompare_null()
    {
        $this->assertFalse($this->comparer->compare($this->Item1, $this->Item2));
    }

    public function testCompare_deliveryFee()
    {
        $this->Item1->setOrderItemType($this->TypeDeliveryFee);
        $this->Item2->setOrderItemType($this->TypeDeliveryFee);

        $this->assertFalse($this->comparer->compare($this->Item1, $this->Item2));
    }

    public function testCompare_charge()
    {
        $this->Item1->setOrderItemType($this->TypeCharge);
        $this->Item2->setOrderItemType($this->TypeCharge);

        $this->assertFalse($this->comparer->compare($this->Item1, $this->Item2));
    }

    public function testCompare_discount()
    {
        $this->Item1->setOrderItemType($this->TypeDiscount);
        $this->Item2->setOrderItemType($this->TypeDiscount);

        $this->assertFalse($this->comparer->compare($this->Item1, $this->Item2));
    }

    public function testCompare_tax()
    {
        $this->Item1->setOrderItemType($this->TypeTax);
        $this->Item2->setOrderItemType($this->TypeTax);

        $this->assertFalse($this->comparer->compare($this->Item1, $this->Item2));
    }
}
