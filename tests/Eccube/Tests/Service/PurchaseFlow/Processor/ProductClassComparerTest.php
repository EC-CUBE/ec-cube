<?php

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\ProductClassComparer;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class ProductClassComparerTest extends EccubeTestCase
{
    /**
     * @var ProductClassComparer
     */
    protected $comparer;

    /**
     * @var Cart
     */
    protected $Cart;

    /**
     * @var ProductClass
     */
    protected $ProductClass;

    /**
     * @var ProductClass
     */
    protected $ProductClassOfSameProduct;

    /**
     * @var ProductClass
     */
    protected $ProductClassOfOtherProduct;

    public function setUp()
    {
        parent::setUp();

        $this->comparer = new ProductClassComparer();

        $Product = $this->createProduct('テスト商品', 2);
        $this->ProductClass = $Product->getProductClasses()[0];
        $this->ProductClassOfSameProduct = $Product->getProductClasses()[1];

        $OtherProduct = $this->createProduct('他の商品', 1);
        $this->ProductClassOfOtherProduct = $OtherProduct->getProductClasses()[0];

        $CartItem = new CartItem();
        $CartItem->setProductClass($this->ProductClass);

        $this->Cart = new Cart();
        $this->Cart->addItem($CartItem);
    }

    public function testInstance()
    {
        self::assertInstanceOf(ProductClassComparer::class, $this->comparer);
    }

    public function testSameProductClass()
    {
        $CartItem = new CartItem();
        $CartItem->setProductClass($this->ProductClass);
        $result = $this->comparer->process($CartItem, new PurchaseContext($this->Cart));
        self::assertFalse($result->isSuccess());
    }

    public function testOtherProductClassOfSameProduct()
    {
        $CartItem = new CartItem();
        $CartItem->setProductClass($this->ProductClassOfSameProduct);
        $result = $this->comparer->process($CartItem, new PurchaseContext($this->Cart));
        self::assertFalse($result->isWarning());
    }

    public function testOtherProductClassOfOtherProduct()
    {
        $CartItem = new CartItem();
        $CartItem->setProductClass($this->ProductClassOfOtherProduct);
        $result = $this->comparer->process($CartItem, new PurchaseContext($this->Cart));
        self::assertFalse($result->isWarning());
    }
}
