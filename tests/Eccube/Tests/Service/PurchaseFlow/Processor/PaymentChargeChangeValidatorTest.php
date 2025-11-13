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
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\Processor\PaymentChargeChangeValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PaymentChargeChangeValidatorTest extends EccubeTestCase
{
    private ?PaymentChargeChangeValidator $validator = null;

    private ?Customer $Customer = null;

    private ?Order $Order = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new PaymentChargeChangeValidator();

        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
    }

    public function testNewInstance()
    {
        $validator = new PaymentChargeChangeValidator();

        $this->assertInstanceOf(PaymentChargeChangeValidator::class, $validator);
    }

    public function testValidateWithCart()
    {
        $result = $this->validator->execute(new Cart(), new PurchaseContext());

        // カートの場合は何もしない.
        $this->assertTrue($result->isSuccess());
    }

    public function testValidateNoCharged()
    {
        $CloneOrder = clone $this->Order;
        $CloneOrder->setCharge(100);
        $this->Order->setCharge(100);

        $result = $this->validator->execute($this->Order, new PurchaseContext($CloneOrder));

        $this->assertTrue($result->isSuccess());
    }

    public function testValidateChanged()
    {
        $CloneOrder = clone $this->Order;
        $CloneOrder->setCharge(100);
        $this->Order->setCharge(200);

        $result = $this->validator->execute($this->Order, new PurchaseContext($CloneOrder));

        $this->assertTrue($result->isWarning());
    }
}
