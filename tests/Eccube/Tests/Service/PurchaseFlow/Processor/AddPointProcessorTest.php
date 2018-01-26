<?php

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\Product;
use Eccube\Service\PurchaseFlow\Processor\AddPointProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class AddPointProcessorTest extends EccubeTestCase
{

    /** @var  Cart */
    private $Cart;

    /** @var  BaseInfo */
    private $BaseInfo;

    /** @var  Product */
    private $Product;

    /** @var  int */
    private $total;

    public function setUp()
    {
        parent::setUp();
        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->BaseInfo->setBasicPointRate(10);
        $this->Cart = new Cart();
        $this->Product = $this->createProduct('テスト商品', 5);
        $this->total = 0;

        foreach ($this->Product->getProductClasses() as $ProductClass) {
            $cartItem = new CartItem();
            $cartItem->setObject($ProductClass);
            $cartItem->setPrice($ProductClass->getPrice02IncTax());
            $cartItem->setQuantity(1);
            $this->Cart->addCartItem($cartItem);
            $this->total += round($cartItem->getPrice() * $this->BaseInfo->getBasicPointRate() / 100) * $cartItem->getQuantity();
        }
    }

    public function testProcess()
    {
        $processor = new AddPointProcessor($this->entityManager, $this->BaseInfo);
        $processor->process($this->Cart, new PurchaseContext());
        $actual = $this->Cart->getAddPoint();
        self::assertGreaterThan(0, $actual);

        $expected = $this->total;
        self::assertEquals($expected, $actual);
    }
}
