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
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\TaxRule;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\PurchaseFlow\Processor\TaxProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class TaxProcessorTest extends EccubeTestCase
{
    /** @var TaxProcessor */
    private $processor;

    /** @var Order */
    private $Order;

    /** @var Product */
    private $Product;

    /** @var ProductClass */
    private $ProductClass;

    /** @var TaxRule */
    private $TaxRule;

    /** @var TaxRuleRepository */
    private $taxRuleRepository;

    public function setUp()
    {
        parent::setUp();

        $this->processor = self::$container->get(TaxProcessor::class);
        $this->taxRuleRepository = $this->entityManager->getRepository(\Eccube\Entity\TaxRule::class);

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
     * @see https://github.com/EC-CUBE/ec-cube/issues/4236
     */
    public function testTaxRateChangedShoppingFlow()
    {
        // 受注作成後に税率を変更
        $this->TaxRule->setTaxRate(10);

        $context = new PurchaseContext();
        $context->setFlowType(PurchaseContext::SHOPPING_FLOW);
        $this->processor->process($this->Order, $context);

        /** @var OrderItem[] $ProductOrderItems */
        $ProductOrderItems = $this->Order->getProductOrderItems();

        self::assertEquals(1, count($ProductOrderItems));
        self::assertEquals(1100, $ProductOrderItems[0]->getTotalPrice());
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/issues/4269
     */
    public function testTaxRateChangedOrderFlow()
    {
        // 受注作成後に税率を変更
        $this->TaxRule->setTaxRate(10);

        $context = new PurchaseContext();
        $context->setFlowType(PurchaseContext::ORDER_FLOW);
        $this->processor->process($this->Order, $context);

        /** @var OrderItem[] $ProductOrderItems */
        $ProductOrderItems = $this->Order->getProductOrderItems();

        self::assertEquals(1, count($ProductOrderItems));
        self::assertEquals(1080, $ProductOrderItems[0]->getTotalPrice());
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/issues/4330
     */
    public function testProductTaxRule()
    {
        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $BaseInfo->setOptionProductTaxRule(true);

        $this->TaxRule->setTaxRate(10);

        /** @var RoundingType $RoundingType */
        $RoundingType = $this->entityManager->find(RoundingType::class, RoundingType::ROUND);
        // 商品別税率を設定し, 受注を生成
        $TaxRule = new TaxRule();
        $TaxRule->setTaxRate(8)
            ->setApplyDate(new \DateTime('-3 days'))
            ->setRoundingType($RoundingType)
            ->setProduct($this->Product)
            ->setProductClass($this->ProductClass)
        ;
        $this->entityManager->persist($TaxRule);
        $this->entityManager->flush();
        $this->entityManager->refresh($this->TaxRule);
        $this->entityManager->refresh($TaxRule);

        $this->taxRuleRepository->clearCache();
        $actual = $this->taxRuleRepository->getByRule($this->Product, $this->ProductClass);
        self::assertEquals($TaxRule, $actual);

        $Customer = $this->createCustomer();
        $Order = $this->createOrderWithProductClasses($Customer, $this->Product->getProductClasses()->toArray());
        $Order->getProductOrderItems()[0]
            ->setRoundingType(null)
            ->setQuantity(1);
        $this->entityManager->flush();

        $this->processor->process($Order, new PurchaseContext());

        /** @var OrderItem[] $ProductOrderItems */
        $ProductOrderItems = $Order->getProductOrderItems();

        self::assertEquals(1, count($ProductOrderItems));
        self::assertEquals(1080, $ProductOrderItems[0]->getTotalPrice());
    }
}
