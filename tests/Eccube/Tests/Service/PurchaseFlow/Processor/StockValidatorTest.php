<?php

namespace Eccube\Tests\Service;

use Eccube\Entity\CartItem;
use Eccube\Service\PurchaseFlow\Processor\StockValidator;
use Eccube\Tests\EccubeTestCase;

class StockValidatorTest extends EccubeTestCase
{
    /**
     * @var StockValidator
     */
    protected $validator;
    protected $cartItem;
    protected $Product;
    protected $ProductClass;

    public function setUp()
    {
        parent::setUp();

        $this->Product = $this->createProduct('テスト商品', 1);
        $this->ProductClass = $this->Product->getProductClasses()[0];
        $this->validator = new StockValidator();
        $this->cartItem = new CartItem();
        $this->cartItem->setObject($this->ProductClass);
    }

    public function testInstance()
    {
        self::assertInstanceOf(StockValidator::class, $this->validator);
        self::assertSame($this->ProductClass, $this->cartItem->getObject());
    }

    public function testValidStock()
    {
        $this->cartItem->setQuantity(1);
        $this->validator->process($this->cartItem);
        self::assertEquals(1, $this->cartItem->getQuantity());
    }

    public function testValidStockFail()
    {
        $this->cartItem->setQuantity(PHP_INT_MAX);
        $result = $this->validator->process($this->cartItem);

        self::assertEquals($this->ProductClass->getStock(), $this->cartItem->getQuantity());
        self::assertTrue($result->isWarning());
    }

    public function testValidStockOrder()
    {
        $Customer = $this->createCustomer();
        $Order = $this->app['eccube.fixture.generator']->createOrder($Customer, array($this->ProductClass));

        self::assertEquals($Order->getShipmentItems()[0]->getProductClass(), $this->ProductClass);

        $Order->getShipmentItems()[0]->setQuantity(1);
        $this->ProductClass->setStock(100);

        $this->validator->process($Order->getShipmentItems()[0]);
        self::assertEquals(1, $Order->getShipmentItems()[0]->getQuantity());
    }
}
