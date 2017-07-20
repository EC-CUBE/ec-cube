<?php

namespace Eccube\Tests\Service;

use Eccube\Entity\CartItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Processor\DeletedProductValidator;
use Eccube\Service\PurchaseFlow\Processor\DeliverySettingValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class DeletedProductValidatorTest extends EccubeTestCase
{
    /**
     * @var DeletedProductValidator
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
        $this->validator = new DeletedProductValidator();
        $this->cartItem = new CartItem();
        $this->cartItem->setObject($this->ProductClass);
    }

    public function testInstance()
    {
        self::assertInstanceOf(DeletedProductValidator::class, $this->validator);
    }

    /**
     * 削除フラグ(スタータス)が立っていなければ何もしない
     */
    public function testProductIsValid()
    {
        $result = $this->validator->process($this->cartItem, PurchaseContext::create());

        self::assertFalse($result->isError());
    }

    /**
     * 削除済商品の場合は明細の個数を0に設定する
     */
    public function testDisplayStatusWithClosed()
    {
        $this->Product->setDelFlg(1);
        $result = $this->validator->process($this->cartItem, PurchaseContext::create());

        self::assertEquals(0, $this->cartItem->getQuantity());
        self::assertTrue($result->isWarning());
    }
}
