<?php

namespace Eccube\Tests\Service;

use Eccube\Application;
use Eccube\Entity\CartItem;
use Eccube\Entity\Master\Disp;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\DisplayStatusValidator;
use Eccube\Tests\EccubeTestCase;

class DisplayStatusValidatorTest extends EccubeTestCase
{
    /**
     * @var DisplayStatusValidator
     */
    protected $validator;

    /**
     * @var CartItem
     */
    protected $cartItem;

    /**
     * @var Product
     */
    protected $Product;

    /**
     * @var ProductClass
     */
    protected $ProductClass;

    public function setUp()
    {
        parent::setUp();

        $this->Product = $this->createProduct('テスト商品', 1);
        $this->ProductClass = $this->Product->getProductClasses()[0];
        $this->validator = new DisplayStatusValidator();
        $this->cartItem = new CartItem();
        $this->cartItem->setQuantity(10);
        $this->cartItem->setObject($this->ProductClass);
    }

    public function testInstance()
    {
        self::assertInstanceOf(DisplayStatusValidator::class, $this->validator);
    }

    /**
     * 公開商品の場合はなにもしない
     */
    public function testDisplayStatusWithShow()
    {
        /** @var Application $app */
        $app = $this->app;
        $Disp = $app['eccube.repository.master.disp']->find(Disp::DISPLAY_SHOW);
        $this->Product->setStatus($Disp);

        $this->validator->process($this->cartItem);

        self::assertEquals(10, $this->cartItem->getQuantity());
    }

    /**
     * 非公開商品の場合は明細の個数を0に設定する
     */
    public function testDisplayStatusWithClosed()
    {
        /** @var Application $app */
        $app = $this->app;
        $Disp = $app['eccube.repository.master.disp']->find(Disp::DISPLAY_HIDE);
        $this->Product->setStatus($Disp);

        $this->validator->process($this->cartItem);

        self::assertEquals(0, $this->cartItem->getQuantity());
    }
}
