<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\Cart;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\Processor\PaymentTotalLimitValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PaymentTotalLimitValidatorTest extends EccubeTestCase
{
    public function testCartValidate()
    {
        $validator = $this->newValidator(1000);

        $cart = new Cart();
        $cart->setTotal(100);

        $result = $validator->process($cart, new PurchaseContext());
        self::assertFalse($result->isError());
    }

    public function testCartValidateFail()
    {
        $validator = $this->newValidator(1000);

        $cart = new Cart();
        $cart->setTotal(1001);

        $result = $validator->process($cart, new PurchaseContext());
        self::assertTrue($result->isError());
    }

    public function testOrderValidate()
    {
        $validator = $this->newValidator(1000);

        $order = new Order();
        $order->setTotal(100);

        $result = $validator->process($order, new PurchaseContext());
        self::assertFalse($result->isError());
    }

    public function testOrderValidateFail()
    {
        $validator = $this->newValidator(1000);

        $order = new Order();
        $order->setTotal(1001);

        $result = $validator->process($order, new PurchaseContext());
        self::assertTrue($result->isError());
    }

    private function newValidator($maxTotalFee)
    {
        $result = $this->container->get(PaymentTotalLimitValidator::class);
        $rc = new \ReflectionClass(PaymentTotalLimitValidator::class);
        $prop = $rc->getProperty('maxTotalFee');
        $prop->setAccessible(true);
        $prop->setValue($result, $maxTotalFee);

        return $result;
    }
}
