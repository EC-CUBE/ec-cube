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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Cart;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\Processor\PaymentTotalLimitValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\MockObject\Exception;

class PaymentTotalLimitValidatorTest extends EccubeTestCase
{
    public function testCartValidate()
    {
        $validator = $this->newValidator(1000);

        $cart = new Cart();
        $cart->setTotal(100);

        $result = $validator->execute($cart, new PurchaseContext());
        self::assertFalse($result->isError());
    }

    public function testCartValidateFail()
    {
        $validator = $this->newValidator(1000);

        $cart = new Cart();
        $cart->setTotal(1001);

        $result = $validator->execute($cart, new PurchaseContext());
        self::assertTrue($result->isError());
    }

    public function testOrderValidate()
    {
        $validator = $this->newValidator(1000);

        $order = new Order();
        $order->setTotal(100);

        $result = $validator->execute($order, new PurchaseContext());
        self::assertFalse($result->isError());
    }

    public function testOrderValidateFail()
    {
        $validator = $this->newValidator(1000);

        $order = new Order();
        $order->setTotal(1001);

        $result = $validator->execute($order, new PurchaseContext());
        self::assertTrue($result->isError());
    }

    /**
     * @param $maxTotalFee
     *
     * @throws Exception
     */
    private function newValidator($maxTotalFee): PaymentTotalLimitValidator
    {
        // PaymentTotalLimitValidatorのmaxTotalFeeをreadonlyに設定したため、
        // EccubeConfigをモックして、指定した値を返すようにする
        /** @var EccubeConfig&\PHPUnit\Framework\MockObject\MockObject $eccubeConfig */
        $eccubeConfig = $this->createMock(EccubeConfig::class);
        $eccubeConfig->method('offsetGet')
            ->with('eccube_max_total_fee')
            ->willReturn($maxTotalFee);

        return new PaymentTotalLimitValidator($eccubeConfig);
    }
}
