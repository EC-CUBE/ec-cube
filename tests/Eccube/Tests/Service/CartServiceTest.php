<?php

namespace Eccube\Tests\Service;

use Eccube\Application;
use Eccube\Service\CartService;

class CartServiceTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function setUp()
    {
        $this->app = new Application(array(
            'env' => 'test'
        ));
        $this->app->boot();
    }

    public function testUnlock()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->unlock();

        $this->assertFalse($cartService->isLocked());
    }

    public function testLock()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->lock();

        $this->assertTrue($cartService->isLocked());
    }

    public function testClear_PreOrderId()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->clear();

        $this->assertNull($cartService->getPreOrderId());
    }

    public function testClear_Lock()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->clear();

        $this->assertFalse($cartService->isLocked());
        $this->assertCount(0, $cartService->getCart()->getCartItems());
    }

    public function testClear_Products()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->addProduct(1);
        $cartService->clear();

        $this->assertCount(0, $cartService->getCart()->getCartItems());
    }

    public function testAddProducts_ProductClassEntity()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->addProduct(1);

        $CartItems = $cartService->getCart()->getCartItems();

        $this->assertEquals('Eccube\Entity\ProductClass', $CartItems[0]->getClassName());
        $this->assertEquals(1, $CartItems[0]->getClassId());
    }

    public function testAddProducts_Quantity()
    {
        $cartService = $this->app['eccube.service.cart'];
        $this->assertCount(0, $cartService->getCart()->getCartItems());

        $cartService->addProduct(1);
        $this->assertEquals(1, $cartService->getProductQuantity(1));
    }

    public function testUpProductQuantity()
    {
        $cartService = $this->app['eccube.service.cart'];
        $cartService->setProductQuantity(1, 1);
        $cartService->upProductQuantity(1);

        $quantity = $cartService->getProductQuantity(1);

        $this->assertEquals(2, $quantity);
    }

    public function testDownProductQuantity()
    {
        $cartService = $this->app['eccube.service.cart'];

        $cartService->setProductQuantity(1, 2);
        $cartService->downProductQuantity(1);

        $quantity = $cartService->getProductQuantity(1);

        $this->assertEquals(1, $quantity);
    }

    public function testDownProductQuantity_Remove()
    {
        $cartService = $this->app['eccube.service.cart'];

        $cartService->setProductQuantity(1, 1);
        $cartService->downProductQuantity(1);

        $quantity = $cartService->getProductQuantity(1);
        $this->assertEquals(0, $quantity);
        $this->assertCount(0, $cartService->getCart()->getCartItems());
    }

    public function testRemoveProduct()
    {
        $cartService = $this->app['eccube.service.cart'];

        $cartService->setProductQuantity(1, 2);
        $cartService->removeProduct(1);

        $this->assertCount(0, $cartService->getCart()->getCartItems());
    }

    public function testGetErrors()
    {
        $cartService = $this->app['eccube.service.cart'];

        $this->assertCount(0, $cartService->getErrors());

        $cartService->addError('foo');
        $cartService->addError('bar');

        $this->assertCount(2, $cartService->getErrors());
    }

    public function testGetMessages()
    {
        $cartService = $this->app['eccube.service.cart'];
        $this->assertCount(0, $cartService->getMessages());

        $cartService->setMessage('foo');
        $cartService->setMessage('bar');

        $this->assertCount(2, $cartService->getMessages());
    }

}
