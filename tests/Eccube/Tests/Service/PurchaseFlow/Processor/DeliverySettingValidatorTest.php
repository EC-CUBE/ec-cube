<?php

namespace Eccube\Tests\Service;

use Eccube\Entity\CartItem;
use Eccube\Entity\Master\ProductType;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\DeliverySettingValidator;
use Eccube\Service\PurchaseFlow\Processor\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class DeliverySettingValidatorTest extends EccubeTestCase
{
    /**
     * @var DeliverySettingValidator
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
        $this->validator = new DeliverySettingValidator($this->app['eccube.repository.delivery']);
        $this->cartItem = new CartItem();
        $this->cartItem->setObject($this->ProductClass);
    }

    public function testInstance()
    {
        self::assertInstanceOf(DeliverySettingValidator::class, $this->validator);
    }

    /**
     * 配送業者が適切に設定されていれば何もしない
     */
    public function testDeliverySettingIsValid()
    {
        $result = $this->validator->process($this->cartItem, PurchaseContext::create());

        self::assertFalse($result->isError());
    }

    /**
     * 配送業者が設定できていない商品の場合は明細の個数を0に設定する
     */
    public function testDisplayStatusWithClosed()
    {
        $ProductType = new ProductType();
        $ProductType->setId(10000);
        $this->ProductClass->setProductType($ProductType);

        $this->validator->process($this->cartItem, PurchaseContext::create());

        self::assertEquals(0, $this->cartItem->getQuantity());
    }
}
