<?php

namespace Eccube\Tests\Service;

class CartCompareServiceTest extends AbstractServiceTestCase
{
    /** @var \Eccube\Service\CartService */
    protected $cartService;

    /** @var \Eccube\Service\CartCompareService */
    protected $compareService;

    /** @var \Eccube\Entity\Product */
    protected $Product;

    public function setUp()
    {
        parent::setUp();
        $this->cartService = $this->app['eccube.service.cart'];
        $this->compareService = $this->cartService->generateCartCompareService();
        $this->Product = $this->createProduct();
    }

    public function testGetExistsCartItem_exists()
    {
        $ProductClasses = $this->Product->getProductClasses();
        $CartItem1 = $this->cartService->generateCartItem($ProductClasses[0]);
        $CartItem2 = $this->cartService->generateCartItem($ProductClasses[1]);
        $this->cartService->addCartItem($CartItem1);
        $this->cartService->addCartItem($CartItem2);

        $SearchCartItem = clone $CartItem2;
        $this->assertEquals($CartItem2, $this->compareService->getExistsCartItem($SearchCartItem));
    }

    public function testGetExistsCartItem_notExists()
    {
        $ProductClasses = $this->Product->getProductClasses();
        $CartItem1 = $this->cartService->generateCartItem($ProductClasses[0]);
        $CartItem2 = $this->cartService->generateCartItem($ProductClasses[1]);
        $this->cartService->addCartItem($CartItem1);
        $this->cartService->addCartItem($CartItem2);

        $CartItem3 = $this->cartService->generateCartItem($ProductClasses[2]);

        $SearchCartItem = clone $CartItem3;
        $this->assertNull($this->compareService->getExistsCartItem($SearchCartItem));
    }

    public function testCompare_equal()
    {
        $ProductClasses = $this->Product->getProductClasses();
        $CartItem1 = $this->cartService->generateCartItem($ProductClasses[0]);
        $CartItem2 = $this->cartService->generateCartItem($ProductClasses[1]);
        $SearchCartItem1 = clone $CartItem1;
        $SearchCartItem2 = clone $CartItem2;

        $this->assertTrue($this->compareService->compare($CartItem1, $SearchCartItem1));
        $this->assertTrue($this->compareService->compare($CartItem2, $SearchCartItem2));
    }

    public function testCompare_notEqual()
    {
        $ProductClasses = $this->Product->getProductClasses();
        $CartItem1 = $this->cartService->generateCartItem($ProductClasses[0]);
        $CartItem2 = $this->cartService->generateCartItem($ProductClasses[1]);
        $SearchCartItem1 = clone $CartItem1;
        $SearchCartItem2 = clone $CartItem2;

        $this->assertFalse($this->compareService->compare($CartItem1, $SearchCartItem2));
        $this->assertFalse($this->compareService->compare($CartItem2, $SearchCartItem1));
    }
}
