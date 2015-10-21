<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;
use Eccube\Entity\Shipping;
use Eccube\Entity\ShipmentItem;

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

    public function setUp()
    {
        parent::setUp();
        $faker = $this->getFaker();
        $this->Member = $this->app['eccube.repository.member']->find(2);
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $this->Product = $this->createProduct();
        $ProductClasses = $this->Product->getProductClasses();
        $this->ProductClass = $ProductClasses[0];
        $quantity = 3;
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(); // デフォルト課税規則

        $OrderDetail = new OrderDetail();
        $OrderDetail->setProduct($this->Product)
            ->setProductClass($this->ProductClass)
            ->setProductName($this->Product->getName())
            ->setProductCode($this->ProductClass->getCode())
            ->setPrice($this->ProductClass->getPrice02())
            ->setQuantity($quantity)
            ->setTaxRule($TaxRule->getCalcRule()->getId())
            ->setTaxRate($TaxRule->getTaxRate());
        $this->app['orm.em']->persist($OrderDetail);
        $OrderDetail->setOrder($this->Order);
        $this->Order->addOrderDetail($OrderDetail);

        // 1個ずつ別のお届け先に届ける
        for ($i = 0; $i < $quantity; $i++) {
            $Shipping = new Shipping();
            $Shipping->copyProperties($this->Customer);
            $Shipping
                ->setName01($faker->lastName)
                ->setName02($faker->firstName)
                ->setKana01('セイ');
            $this->Order->addShipping($Shipping);
            $Shipping->setOrder($this->Order);
            $this->app['orm.em']->persist($Shipping);

            $ShipmentItem = new ShipmentItem();
            $ShipmentItem->setShipping($Shipping)
                ->setOrder($this->Order)
                ->setProductClass($this->ProductClass)
                ->setProduct($this->Product)
                ->setProductName($this->Product->getName())
                ->setProductCode($this->ProductClass->getCode())
                ->setPrice($this->ProductClass->getPrice02())
                ->setQuantity(1);
            $this->app['orm.em']->persist($ShipmentItem);
        }

        $subTotal = 0;
        foreach ($this->Order->getOrderDetails() as $OrderDetail) {
            $subTotal += $OrderDetail->getPriceIncTax() * $OrderDetail->getQuantity();
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
}
