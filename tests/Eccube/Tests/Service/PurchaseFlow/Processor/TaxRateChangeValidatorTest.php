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

use Eccube\Entity\Cart;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\Processor\TaxRateChangeValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class TaxRateChangeValidatorTest extends EccubeTestCase
{
    protected ?TaxRateChangeValidator $validator = null;

    protected ?Order $Order = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new TaxRateChangeValidator();

        $Customer = $this->createCustomer();
        $this->Order = $this->createOrder($Customer);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(TaxRateChangeValidator::class, $this->validator);
    }

    public function testValidateWithCart()
    {
        $result = $this->validator->execute(new Cart(), new PurchaseContext());

        // カートの場合な何もしない
        $this->assertTrue($result->isSuccess());
    }

    public function testValidateNoChanged()
    {
        $CloneOrder = clone $this->Order;
        foreach ($CloneOrder->getTaxableItems() as $orderItem) {
            $orderItem->setTaxRate('10');
        }

        foreach ($this->Order->getTaxableItems() as $orderItem) {
            $orderItem->setTaxRate('10');
        }

        $result = $this->validator->execute($this->Order, new PurchaseContext($CloneOrder));

        $this->assertTrue($result->isSuccess());
    }

    public function testValidateChanged()
    {
        $CloneOrder = clone $this->Order;
        foreach ($CloneOrder->getTaxableItems() as $orderItem) {
            $orderItem->setTaxRate('10');
        }

        foreach ($this->Order->getTaxableItems() as $orderItem) {
            $orderItem->setTaxRate('50');
        }

        $result = $this->validator->execute($this->Order, new PurchaseContext($CloneOrder));

        $this->assertTrue($result->isWarning());
    }
}
