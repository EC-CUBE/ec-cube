<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Form\Type\Admin\OrderItemType;

class OrderItemTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'ProductClass' => '1',
        'price' => '10000',
        'quantity' => '10000',
        'product_name' => 'name1',
        'order_item_type' => '1',
    ];

    public function setUp()
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

    public function testInvalidPrice_Blank()
    {
        $this->formData['price'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice_OverMaxLength()
    {
        $this->formData['price'] = $this->eccubeConfig['eccube_price_max'] + 1;

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice_NotNumeric()
    {
        $this->formData['price'] = 'abc';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidPrice_HasMinus()
    {
        $this->formData['price'] = '-123456';
        // 値引き明細はマイナス値
        $this->formData['order_item_type'] = \Eccube\Entity\Master\OrderItemType::DISCOUNT;

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidQuantity_Blank()
    {
        $this->formData['quantity'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidQuantity_OverMaxLength()
    {
        $this->formData['quantity'] = '12345678910'; //Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidQuantity_NotNumeric()
    {
        $this->markTestIncomplete('testInvalidQuantity_NotNumeric is not implemented.');
        $this->formData['quantity'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidQuantity_HasMinus()
    {
        $this->markTestIncomplete('testInvalidQuantity_HasMinus is not implemented.');
        $this->formData['quantity'] = '-123456';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTaxRate_Blank()
    {
        $this->formData['tax_rate'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTaxRate_OverMaxLength()
    {
        $this->formData['tax_rate'] = '12345678910'; // Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTaxRate_NotNumeric()
    {
        $this->formData['tax_rate'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTaxRate_HasMinus()
    {
        $this->formData['tax_rate'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
