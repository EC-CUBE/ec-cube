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
use Eccube\Service\PurchaseFlow\Processor\TaxFeeChangeValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class TaxFeeChangeValidatorTest extends EccubeTestCase
{
    /**
     * @var TaxFeeChangeValidator
     */
    private $validator;

    /**
     * @var Customer
     */
    private $Customer;

    /**
     * @var Order
     */
    private $Order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new TaxFeeChangeValidator();

        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
    }

    public function testNewInstance()
    {
        $validator = new TaxFeeChangeValidator();

        self::assertInstanceOf(TaxFeeChangeValidator::class, $validator);
    }

    public function testValidateWithCart()
    {
        $result = $this->validator->execute(new Cart(), new PurchaseContext());

        // カートの場合は何もしない.
        self::assertTrue($result->isSuccess());
    }

    public function testValidateNochanged()
    {
        $CloneOrder = clone $this->Order;
        $CloneOrder->setTax(10);
        $this->Order->setTax(10);

        $result = $this->validator->execute($this->Order, new PurchaseContext($CloneOrder));

        self::assertTrue($result->isSuccess());
    }

    public function testValidateChanged()
    {
        $CloneOrder = clone $this->Order;
        $CloneOrder->setTax(10);
        $this->Order->setTax(20);

        $result = $this->validator->execute($this->Order, new PurchaseContext($CloneOrder));

        self::assertTrue($result->isWarning());
    }
}
