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
        $cart = $this->app['eccube.service.cart'];
        $cart->unlock();

        $this->assertFalse($cart->isLocked());
    }

    public function testLock()
    {
        $cart = $this->app['eccube.service.cart'];
        $cart->lock();

        $this->assertTrue($cart->isLocked());
    }

    public function testClear_PreOrderId()
    {
        $cart = $this->app['eccube.service.cart'];
        $cart->clear();

        $this->assertNull($cart->getPreOrderId());
    }

    public function testClear_Lock()
    {
        $cart = $this->app['eccube.service.cart'];
        $cart->clear();

        $this->assertFalse($cart->isLocked());
    }

    public function testClear_Products()
    {
        $cart = $this->app['eccube.service.cart'];
        $cart->clear();

        $this->assertCount(0, $cart->getProducts());
    }

    public function testAddProducts_ProductClassEntity()
    {
        $cart = $this->app['eccube.service.cart'];
        $cart->addProduct(1);

        $products = $cart->getProducts();
        $productClassId = $products[1]['ProductClass']->getId();

        $this->assertEquals(1, $productClassId);
    }

    public function testAddProducts_Quantity()
    {
        $cart = $this->app['eccube.service.cart'];
        $this->assertCount(0, $cart->getProducts());

        $cart->addProduct(1);
        $this->assertEquals(1, $cart->getProductQuantity(1));
    }

    public function testUpProductQuantity()
    {
        $cart = $this->app['eccube.service.cart'];
        $cart->setProductQuantity(1, 1);
        $cart->upProductQuantity(1);

        $quantity = $cart->getProductQuantity(1);

        $this->assertEquals(2, $quantity);
    }

    public function testDownProductQuantity()
    {
        $cart = $this->app['eccube.service.cart'];

        $cart->setProductQuantity(1, 2);
        $cart->downProductQuantity(1);

        $quantity = $cart->getProductQuantity(1);

        $this->assertEquals(1, $quantity);
    }

    public function testDownProductQuantity_Remove()
    {
        $cart = $this->app['eccube.service.cart'];

        $cart->setProductQuantity(1, 1);
        $cart->downProductQuantity(1);

        $quantity = $cart->getProductQuantity(1);

        $this->assertCount(0, $cart->getProducts());
    }

    public function testRemoveProduct()
    {
        $cart = $this->app['eccube.service.cart'];

        $cart->setProductQuantity(1, 2);
        $cart->removeProduct(1);

        $this->assertCount(0, $cart->getProducts());
    }

    public function testGetErrors()
    {
        $cart = $this->app['eccube.service.cart'];

        $this->assertCount(0, $cart->getErrors());
        
        $cart->setError('foo');
        $cart->setError('bar');
        
        $this->assertCount(2, $cart->getErrors());
    }

    public function testGetMessages()
    {
        $cart = $this->app['eccube.service.cart'];
        $this->assertCount(0, $cart->getMessages());

        $cart->setMessage('foo');
        $cart->setMessage('bar');

        $this->assertCount(2, $cart->getMessages());
    }

}