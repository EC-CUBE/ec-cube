<?php

namespace Eccube\Tests\Service;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Exception\CartException;
use Eccube\Service\CartService;
use Eccube\Util\Str;
use Eccube\Service\Calculator\OrderItemCollection;

class CalculateServiceTest extends AbstractServiceTestCase
{
    public function testConstructorInjection()
    {
        $this->markTestSkipped('新しい配送管理の実装が完了するまでスキップ');

        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);
        $previousTotal = $Order->getSubtotal();

        $newOrder = $this->app['eccube.service.calculate']($Order, $Customer)->calculate();
        $this->assertNotEquals($previousTotal, $newOrder->getSubtotal(), '小計が加算されている');

        $Collection = new OrderItemCollection($newOrder->getOrderDetails()->toArray());

        $product_class_count = $Collection->getProductClasses()->count();
        $this->assertNotEquals($product_class_count, $Collection->count());
    }
}
