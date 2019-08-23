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

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\TaxRule;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class TaxProcessorTest extends EccubeTestCase
{
    /** @var TaxProcessor */
    private $processor;

    /** @var  */
    private $Order;

    /** @var Product */
    private $Product;

    /** @var ProductClass */
    private $ProductClass;

    private $TaxRule;

    public function setUp()
    {
        parent::setUp();

        $this->processor = $this->container->get(TaxProcessor::class);

        /** @var RoundingType $RoundingType */
        $RoundingType = $this->entityManager->find(RoundingType::class, RoundingType::ROUND);
        $this->TaxRule = new TaxRule();
        $this->TaxRule->setTaxRate(8)
            ->setApplyDate(new \DateTime('yesterday'))
            ->setRoundingType($RoundingType);
        $this->entityManager->persist($this->TaxRule);

        $Customer = $this->createCustomer();
        $this->Product = $this->createProduct('test', 1);

        $this->ProductClass = $this->Product->getProductClasses()[0];
        $this->ProductClass->setPrice02(1000);
        $this->entityManager->persist($this->ProductClass);

        $this->Order = $this->createOrderWithProductClasses($Customer, $this->Product->getProductClasses()->toArray());
        $this->Order->getProductOrderItems()[0]->setQuantity(1);

        $this->entityManager->flush();
    }

    public function testCalcTax()
    {
        $this->processor->process($this->Order, new PurchaseContext());

        /** @var OrderItem[] $ProductOrderItems */
        $ProductOrderItems = $this->Order->getProductOrderItems();

        self::assertEquals(1, count($ProductOrderItems));
        self::assertEquals(1080, $ProductOrderItems[0]->getTotalPrice());
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/issues/4269
     */
    public function testTaxRateChanged()
    {
        // 受注作成後に税率を変更
        $this->TaxRule->setTaxRate(10);

        $this->processor->process($this->Order, new PurchaseContext());

        /** @var OrderItem[] $ProductOrderItems */
        $ProductOrderItems = $this->Order->getProductOrderItems();

        self::assertEquals(1, count($ProductOrderItems));
        self::assertEquals(1080, $ProductOrderItems[0]->getTotalPrice());
    }
}
