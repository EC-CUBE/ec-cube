<?php

namespace Eccube\Tests\Service\PurchaseFlow;

use Eccube\Entity\ItemInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Tests\EccubeTestCase;
use Eccube\Service\PurchaseFlow\ItemCollection;

class ItemCollectionTest extends EccubeTestCase
{
    protected $ItemsHolder;
    protected $Items;

    public function setUp()
    {
        parent::setUp();
        $Product = $this->createProduct();
        $ProductClasses = $Product->getProductClasses()->toArray();
        $Customer = $this->createCustomer();
        $this->ItemHolder = $this->app['eccube.fixture.generator']->createOrder(
            $Customer, $ProductClasses);
        $this->Items = $this->ItemHolder->getItems()->toArray();
    }

    public function testInstance()
    {
        $actual = new ItemCollection($this->Items);
        self::assertInstanceOf(ItemCollection::class, $actual);
    }

    public function testInstanceWithCollection()
    {
        $actual = new ItemCollection($this->ItemHolder->getItems());
        self::assertInstanceOf(ItemCollection::class, $actual);
    }

    public function testReduce()
    {
        $reducer = function ($sum, ItemInterface $item) {
            return $sum =+ $item->getPrice() * $item->getQuantity();
        };

        $this->expected = array_reduce($this->Items, $reducer, 0);
        $this->actual = (new ItemCollection($this->Items))->reduce($reducer, 0);
        $this->verify();
    }

    public function testGetProductClasses()
    {
        $Items = (new ItemCollection($this->Items))->getProductClasses();
        foreach ($Items as $Item) {
            self::assertTrue($Item->isProduct());
        }
    }

    public function testGetDeliveryFees()
    {
        $Items = (new ItemCollection($this->Items))->getDeliveryFees();
        foreach ($Items as $Item) {
            self::assertTrue($Item->isDeliveryFee());
        }
    }

    public function testGetCharges()
    {
        $Items = (new ItemCollection($this->Items))->getCharges();
        foreach ($Items as $Item) {
            self::assertTrue($Item->isCharge());
        }
    }

    public function testGetDiscounts()
    {
        $Items = (new ItemCollection($this->Items))->getCharges();
        foreach ($Items as $Item) {
            self::assertTrue($Item->isCharge());
        }
    }

    public function testHasItemByOrderItemType()
    {
        $ProductClassType = $this->app['eccube.repository.master.order_item_type']->find(OrderItemType::PRODUCT);
        $DeliveryFeeType = $this->app['eccube.repository.master.order_item_type']->find(OrderItemType::DELIVERY_FEE);
        $ChargeType = $this->app['eccube.repository.master.order_item_type']->find(OrderItemType::CHARGE);
        $DiscountType = $this->app['eccube.repository.master.order_item_type']->find(OrderItemType::DISCOUNT);

        $Items = new ItemCollection($this->Items);

        self::assertTrue($Items->hasItemByOrderItemType($ProductClassType));
        self::assertTrue($Items->hasItemByOrderItemType($DeliveryFeeType));
        self::assertTrue($Items->hasItemByOrderItemType($ChargeType));
        self::assertTrue($Items->hasItemByOrderItemType($DiscountType));
    }

    public function testSort()
    {
        shuffle($this->Items);

        $this->expected = [ 1 => '商品', 2 => '送料', 3 => '手数料', 4 => '割引'];
        $this->actual = [];
        $Items = (new ItemCollection($this->Items))->sort();
        foreach ($Items as $Item) {
            $this->actual[$Item->getOrderItemType()->getId()] = $Item->getOrderItemType()->getName();
        }

        $this->verify();
    }

    public function testSortWithProductClasses()
    {
        shuffle($this->Items);

        $ids = (new ItemCollection($this->Items))
            ->getProductClasses()
            ->map(function (ItemInterface $Item) {
                return $Item->getId();
            })->toArray();
        sort($ids);

        $this->expected = $ids;
        $this->actual = [];
        $Items = (new ItemCollection($this->Items))->sort()->getProductClasses();
        foreach ($Items as $Item) {
            $this->actual[] = $Item->getId();
        }

        $this->verify('product_class_id 順にソートされているはず');
    }
}
