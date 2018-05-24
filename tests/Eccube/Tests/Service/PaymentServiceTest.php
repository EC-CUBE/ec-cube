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

namespace Eccube\Tests\Service;

class PaymentServiceTest extends AbstractServiceTestCase
{
    public function testConstructorInjection()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);

        $paymentService = $this->container->get($Order->getPayment()->getServiceClass());
        $this->assertInstanceOf(\Eccube\Service\PaymentService::class, $paymentService);

        $form = new \Eccube\Form\Type\ShoppingType();
        $paymentMethod = $this->container->get($Order->getPayment()->getMethodClass());
        $paymentMethod->setFormType($form);
        $this->assertInstanceOf(\Eccube\Service\Payment\Method\Cash::class, $paymentMethod);

        $dispatcher = $paymentService->dispatch($paymentMethod); // 決済処理中.
        $this->assertFalse($dispatcher);

        $PaymentResult = $paymentService->doCheckout($paymentMethod); // 決済実行
        $this->assertTrue($PaymentResult->isSuccess());
    }
}
