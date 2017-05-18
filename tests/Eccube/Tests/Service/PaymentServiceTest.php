<?php

namespace Eccube\Tests\Service;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Exception\CartException;
use Eccube\Service\CartService;
use Eccube\Util\Str;
use Eccube\Service\Calculator\ShipmentItemCollection;

class PaymentServiceTest extends AbstractServiceTestCase
{
    public function testConstructorInjection()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $previousTotal = $Order->getSubtotal();
        $paymentService = $this->app['eccube.service.payment']($Order->getPayment()->getServiceClass());
        $this->assertInstanceOf(\Eccube\Service\PaymentService::class, $paymentService);

        $form = new \Eccube\Form\Type\ShoppingType();
        $paymentMethod = $this->app['payment.method']($Order->getPayment()->getMethodClass(), $form);
        $this->assertInstanceOf(\Eccube\Service\Payment\Method\Cash::class, $paymentMethod);

        $dispatcher = $paymentService->dispatch($paymentMethod); // 決済処理中.
        $this->assertFalse($dispatcher);

        $PaymentResult = $paymentService->doCheckout($paymentMethod); // 決済実行
        $this->assertTrue($PaymentResult->isSuccess());
    }
}
