<?php

namespace Eccube\Tests\Service;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Exception\CartException;
use Eccube\Service\CartService;
use Eccube\Util\Str;

class CalculateServiceTest extends AbstractServiceTestCase
{
    public function testConstructorInjection()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $previousTotal = $Order->getSubtotal();

        $newOrder = $this->app['eccube.service.calculate']($Order, $Customer)->calculate();
        $this->assertNotEquals($previousTotal, $newOrder->getSubtotal(), '小計が加算されている');
    }
}
