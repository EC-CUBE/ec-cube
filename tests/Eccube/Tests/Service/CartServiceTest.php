<?php

namespace Eccube\Tests\Service;

use Eccube\Application;
use Eccube\Service\CartService;

class CartServiceTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    private $cart;

    public function setUp()
    {
        $this->app = new Application();
        $this->app['debug'] = true;
        $this->app['session.test'] = true;
        $this->app['exception_handler']->disable();

        $this->cart = new CartService($this->app);
    }

    public function testUnlock()
    {
        $this->cart->unlock();

        $this->assertFalse($this->cart->isLocked());
    }

    public function testLock()
    {
        $this->cart->lock();

        $this->assertTrue($this->cart->isLocked());
    }

    public function testClear_PreOrderId()
    {
        $this->cart->clear();

        $this->assertNull($this->cart->getPreOrderId());
    }

    public function testClear_Lock()
    {
        $this->cart->clear();

        $this->assertFalse($this->cart->isLocked());
    }

    public function testClear_Products()
    {
        $this->cart->clear();

        $this->assertCount(0, $this->cart->getProducts());
    }

    public function testAddProducts_ProductClassEntity()
    {
        $this->cart->addProduct(1);

        $products = $this->cart->getProducts();
        $productClassId = $products[1]['ProductClass']->getId();

        $this->assertEquals(1, $productClassId);
    }

    public function testAddProducts_Quantity()
    {
        $this->assertCount(0, $this->cart->getProducts());

        $this->cart->addProduct(1);
        $this->assertEquals(1, $this->cart->getProductQuantity(1));
    }

    public function testUpProductQuantity()
    {
        $this->cart->setProductQuantity(1, 1);
        $this->cart->upProductQuantity(1);

        $quantity = $this->cart->getProductQuantity(1);

        $this->assertEquals(2, $quantity);
    }

    public function testDownProductQuantity()
    {
        $this->cart->setProductQuantity(1, 2);
        $this->cart->downProductQuantity(1);

        $quantity = $this->cart->getProductQuantity(1);

        $this->assertEquals(1, $quantity);
    }

    public function testDownProductQuantity_Remove()
    {
        $this->cart->setProductQuantity(1, 1);
        $this->cart->downProductQuantity(1);

        $quantity = $this->cart->getProductQuantity(1);

        $this->assertCount(0, $this->cart->getProducts());
    }

    public function testRemoveProduct()
    {
        $this->cart->setProductQuantity(1, 2);
        $this->cart->removeProduct(1);

        $this->assertCount(0, $this->cart->getProducts());
    }

    public function testGetErrors()
    {
        $this->assertCount(0, $this->cart->getErrors());
        
        $this->cart->setError('foo');
        $this->cart->setError('bar');
        
        $this->assertCount(2, $this->cart->getErrors());
    }

    public function testGetMessages()
    {
        $this->assertCount(0, $this->cart->getMessages());
        
        $this->cart->setMessage('foo');
        $this->cart->setMessage('bar');
        
        $this->assertCount(2, $this->cart->getMessages());
    }

}