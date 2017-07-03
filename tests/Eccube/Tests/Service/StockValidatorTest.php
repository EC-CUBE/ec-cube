<?php

namespace Eccube\Tests\Service;

use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;
use Eccube\Service\ItemHolderProcessor;
use Eccube\Service\ItemProcessor;
use Eccube\Service\ItemValidateException;
use Eccube\Service\PurchaseFlow;
use Eccube\Service\StockValidator;
use Eccube\Service\ValidatableItemProcessor;
use Eccube\Service\ValidatableItemHolderProcessor;
use Eccube\Tests\EccubeTestCase;

class StockValidatorTest extends EccubeTestCase
{
    protected $validator;
    protected $cartItem;
    protected $Product;
    public function setUp()
    {
        parent::setUp();

        $this->Product = $this->createProduct('テスト商品', 1);
        $this->validator = new StockValidator();
        $this->cartItem = new CartItem();
        $this->cartItem->setObject($this->Product->getProductClasses()[0]);
    }

    public function testInstance()
    {
        self::assertInstanceOf(StockValidator::class, $this->validator);
        self::assertSame($this->Product->getProductClasses()[0], $this->cartItem->getObject());
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
        try {
            $this->validator->process($this->cartItem);
            self::fail('エラーチェックに失敗しました');
        } catch (ItemValidateException $e) {
            self::assertEquals($this->Product->getProductClasses()[0]->getStock(), $this->cartItem->getQuantity());
        }
    }
}
