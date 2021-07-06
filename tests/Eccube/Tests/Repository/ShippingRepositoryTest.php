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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Member;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Shipping;
use Eccube\Repository\MemberRepository;
use Eccube\Repository\ShippingRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

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

        $this->memberRepository = $this->entityManager->getRepository(\Eccube\Entity\Member::class);
        $this->taxRuleRepository = $this->entityManager->getRepository(\Eccube\Entity\TaxRule::class);
        $this->shippingRepository = $this->entityManager->getRepository(\Eccube\Entity\Shipping::class);

        $faker = $this->getFaker();
        $this->Member = $this->memberRepository->find(2);
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $this->Product = $this->createProduct();
        $this->Shippings = [];
        $ProductClasses = $this->Product->getProductClasses();
        $this->ProductClass = $ProductClasses[0];
        $quantity = 3;

        $TaxDisplayType = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);
        $TaxType = $this->entityManager->find(TaxType::class, TaxType::TAXATION);
        $ProductOrderItemType = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);

        // 1個ずつ別のお届け先に届ける
        for ($i = 0; $i < $quantity; $i++) {
            $Shipping = new Shipping();
            $Shipping->copyProperties($this->Customer);
            $Shipping
                ->setOrder($this->Order)
                ->setName01($faker->lastName)
                ->setName02($faker->firstName)
                ->setKana01('セイ');
            $this->Order->addShipping($Shipping);
            $this->entityManager->persist($Shipping);

            $OrderItem = new OrderItem();
            $OrderItem->setShipping($Shipping)
                ->setOrder($this->Order)
                ->setProductClass($this->ProductClass)
                ->setProduct($this->Product)
                ->setProductName($this->Product->getName())
                ->setProductCode($this->ProductClass->getCode())
                ->setPrice($this->ProductClass->getPrice02())
                ->setQuantity(1)
                ->setTaxDisplayType($TaxDisplayType)
                ->setTaxType($TaxType)
                ->setOrderItemType($ProductOrderItemType)
            ;
            $this->Order->addOrderItem($OrderItem);
            $Shipping->addOrderItem($OrderItem);
            $this->entityManager->persist($OrderItem);
            $this->Shippings[$i] = $Shipping;
        }

        $purchaseFlow = self::$container->get('eccube.purchase.flow.order');
        $purchaseFlow->validate($this->Order, new PurchaseContext($this->Order));
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
}
