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

namespace Eccube\Tests\Repository;

use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Tests\EccubeTestCase;
use Eccube\Repository\MemberRepository;
use Eccube\Entity\Member;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Repository\ShippingRepository;

/**
 * ShippingRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class ShippingRepositoryTest extends EccubeTestCase
{
    /**
     * @var Customer
     */
    protected $Customer;

    /**
     * @var Order
     */
    protected $Order;

    /**
     * @var Product
     */
    protected $Product;

    /**
     * @var ProductClass
     */
    protected $ProductClass;

    /**
     * @var Shipping[]
     */
    protected $Shippings;

    /**
     * @var Member
     */
    protected $Member;

    /**
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\NoResultException
     */
    public function setUp()
    {
        parent::setUp();

        $this->memberRepository = $this->container->get(MemberRepository::class);
        $this->taxRuleRepository = $this->container->get(TaxRuleRepository::class);
        $this->shippingRepository = $this->container->get(ShippingRepository::class);

        $faker = $this->getFaker();
        $this->Member = $this->memberRepository->find(2);
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $this->Product = $this->createProduct();
        $this->Shippings = [];
        $ProductClasses = $this->Product->getProductClasses();
        $this->ProductClass = $ProductClasses[0];
        $quantity = 3;
        $TaxRule = $this->taxRuleRepository->getByRule(); // デフォルト課税規則

        // 1個ずつ別のお届け先に届ける
        for ($i = 0; $i < $quantity; $i++) {
            $Shipping = new Shipping();
            $Shipping->copyProperties($this->Customer);
            $Shipping
                ->setName01($faker->lastName)
                ->setName02($faker->firstName)
                ->setKana01('セイ');

            $this->entityManager->persist($Shipping);

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
            $this->entityManager->persist($OrderItem);
            $this->Shippings[$i] = $Shipping;
        }

        $subTotal = 0;
        foreach ($this->Order->getOrderItems() as $Item) {
            $subTotal += $Item->getPriceIncTax() * $Item->getQuantity();
        }

        $this->Order->setSubTotal($subTotal);
        $this->Order->setTotal($subTotal);
        $this->Order->setPaymentTotal($subTotal);
        $this->entityManager->flush();
    }

    public function testFindShippingsProduct()
    {
        $Shippings = $this->shippingRepository->findShippingsProduct($this->Order, $this->ProductClass);

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
        $Shipping = $this->shippingRepository->find($this->Shippings[0]->getId());

        $this->assertInstanceOf('\Doctrine\Common\Collections\Collection', $Shipping->getOrders());
        $Order = $Shipping->getOrders()->first();
        $this->assertEquals($this->Order->getId(), $Order->getId());
    }
}
