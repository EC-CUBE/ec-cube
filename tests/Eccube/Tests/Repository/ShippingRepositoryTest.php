<?php

namespace Eccube\Tests\Repository;

use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Tests\EccubeTestCase;

/**
 * ShippingRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ShippingRepositoryTest extends EccubeTestCase
{
    protected $Customer;
    protected $Order;
    protected $Product;
    protected $ProductClass;
    protected $Shippings;

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
        $faker = $this->getFaker();
        $this->Member = $this->app['eccube.repository.member']->find(2);
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $this->Product = $this->createProduct();
        $this->Shippings = [];
        $ProductClasses = $this->Product->getProductClasses();
        $this->ProductClass = $ProductClasses[0];
        $quantity = 3;
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(); // デフォルト課税規則

        // 1個ずつ別のお届け先に届ける
        for ($i = 0; $i < $quantity; $i++) {
            $Shipping = new Shipping();
            $Shipping->copyProperties($this->Customer);
            $Shipping
                ->setName01($faker->lastName)
                ->setName02($faker->firstName)
                ->setKana01('セイ');

            $this->app['orm.em']->persist($Shipping);

            $OrderItem = new OrderItem();
            $OrderItem->setShipping($Shipping)
                ->setOrder($this->Order)
                ->setProductClass($this->ProductClass)
                ->setProduct($this->Product)
                ->setProductName($this->Product->getName())
                ->setProductCode($this->ProductClass->getCode())
                ->setPrice($this->ProductClass->getPrice02())
                ->setQuantity(1);
            $Shipping->addOrderItem($OrderItem);
            $this->app['orm.em']->persist($OrderItem);
            $this->Shippings[$i] = $Shipping;
        }

        $subTotal = 0;
        foreach ($this->Order->getOrderItems() as $Item) {
            $subTotal += $Item->getPriceIncTax() * $Item->getQuantity();
        }

        $this->Order->setSubTotal($subTotal);
        $this->Order->setTotal($subTotal);
        $this->Order->setPaymentTotal($subTotal);
        $this->app['orm.em']->flush();
    }

    public function testFindShippingsProduct()
    {
        $Shippings = $this->app['eccube.repository.shipping']->findShippingsProduct($this->Order, $this->ProductClass);

        $this->expected = 3;
        $this->actual = count($Shippings);
        $this->verify();

        for ($i = 0; $i < 3; $i++) {
            $this->expected = 'セイ';
            $this->actual = $Shippings[$i]->getKana01();
            $this->verify();
        }
    }

    public function testGetOrders()
    {
        $Shipping = $this->app['eccube.repository.shipping']->find($this->Shippings[0]->getId());

        $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $Shipping->getOrders());
        $Order = $Shipping->getOrders()->first();
        $this->assertEquals($this->Order->getId(), $Order->getId());
    }
}
