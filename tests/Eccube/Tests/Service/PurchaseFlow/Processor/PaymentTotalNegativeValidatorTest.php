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
use Eccube\Service\PurchaseFlow\Processor\PaymentTotalNegativeValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PaymentTotalNegativeValidatorTest extends EccubeTestCase
{
    public function testPositiveValidate()
    {
        $validator = $this->newValidator();

        $cart = new Cart();
        $cart->setTotal(100);

        $result = $validator->execute($cart, new PurchaseContext());
        self::assertTrue($result->isSuccess());
    }

    public function testNegativeValidate()
    {
        $validator = $this->newValidator();

        $cart = new Cart();
        $cart->setTotal(-100);

        $result = $validator->execute($cart, new PurchaseContext());
        self::assertTrue($result->isError());
    }

    /**
     * @return PaymentTotalNegativeValidator
     */
    private function newValidator()
    {
        return static::getContainer()->get(PaymentTotalNegativeValidator::class);
    }
}
