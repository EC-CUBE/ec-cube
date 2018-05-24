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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\BaseInfo;
use Eccube\Service\PurchaseFlow\Processor\UsePointToCustomerPurchaseProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\Product;

class UsePointToCustomerPurchaseProcessorTest extends EccubeTestCase
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

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
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->BaseInfo->setBasicPointRate(10);
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $this->Product = $this->createProduct('テスト商品', 5);
    }

    public function testProcess()
    {
        $this->expected = $this->Customer->getPoint();
        $OriginalOrder = clone $this->Order;
        $processor = new UsePointToCustomerPurchaseProcessor();
        $processor->process($this->Order, new PurchaseContext($OriginalOrder, $this->Customer));
        $this->actual = $this->Customer->getPoint() - $OriginalOrder->getUsePoint();
        $this->verify();
    }

    public function testProcessFailure()
    {
        $this->Order->setUsePoint($this->Customer->getPoint() + 1);
        $OriginalOrder = clone $this->Order;
        $processor = new UsePointToCustomerPurchaseProcessor();
        $ProcessResult = $processor->process($this->Order, new PurchaseContext($OriginalOrder, $this->Customer));
        self::assertTrue($ProcessResult->isError());
    }
}
