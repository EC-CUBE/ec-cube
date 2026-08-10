<?php

declare(strict_types=1);

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Form\Type\Admin\OrderItemType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Form\FormInterface;

final class OrderItemTypeTest extends AbstractTypeTestCase
{
    protected ?FormInterface $form = null;

    /** @var array デフォルト値（正常系）を設定 */
    protected ?array $formData = [
        'ProductClass' => '1',
        'price' => '10000',
        'quantity' => '10000',
        'product_name' => 'name1',
        'order_item_type' => '1',
        'tax_rate' => '8',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(OrderItemType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
        $Product = $this->createProduct();
        $ProductClass = $Product->getProductClasses()->first();
        $this->formData['ProductClass'] = $ProductClass->getId();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPriceBlank()
    {
        $this->formData['price'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPriceOverMaxLength()
    {
        $this->formData['price'] = $this->eccubeConfig['eccube_price_max'] + 1;

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPriceNotNumeric()
    {
        $this->formData['price'] = 'abc';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidPriceHasMinus()
    {
        $this->formData['price'] = '-123456';
        // 値引き明細はマイナス値
        $this->formData['order_item_type'] = \Eccube\Entity\Master\OrderItemType::DISCOUNT;

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidQuantityBlank()
    {
        $this->formData['quantity'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidQuantityOverMaxLength()
    {
        $this->formData['quantity'] = '12345678910'; // Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidQuantityNotNumeric()
    {
        $this->formData['quantity'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * 個数のマイナス値を許容しない明細種別の検証.
     *
     * 商品明細(PRODUCT)は「金額 -> 正, 個数 -> 正負」が仕様であり、
     * 個数のマイナス値が valid となるため対象外とする。
     * 値引き(DISCOUNT)・送料(DELIVERY_FEE)・手数料(CHARGE)のみ個数の符号が検証される。
     *
     * @see OrderItemType::buildForm() の POST_SUBMIT リスナ
     */
    #[DataProvider(methodName: 'getQuantitySignValidatedOrderItemTypes')]
    public function testInvalidQuantityHasMinus(int $orderItemType, string $price)
    {
        $this->formData['order_item_type'] = $orderItemType;
        // 値引き明細は金額 -> 負が要求されるため、個数のみを検証対象とするよう明細種別ごとに妥当な金額を与える
        $this->formData['price'] = $price;
        $this->formData['quantity'] = '-123456';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * @return \Iterator<string, array{int, string}>
     */
    public static function getQuantitySignValidatedOrderItemTypes(): \Iterator
    {
        yield 'discount' => [\Eccube\Entity\Master\OrderItemType::DISCOUNT, '-10000'];
        yield 'delivery_fee' => [\Eccube\Entity\Master\OrderItemType::DELIVERY_FEE, '10000'];
        yield 'charge' => [\Eccube\Entity\Master\OrderItemType::CHARGE, '10000'];
    }
}
