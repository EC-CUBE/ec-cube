<?php

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

use Eccube\Form\Type\Admin\ProductClassType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class ProductClassTypeTest extends AbstractTypeTestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * @var array デフォルト値（正常系）を設定
     */
    protected $formData = [
        'stock' => '100',
        'sale_limit' => '100',
        'price01' => '100',
        'price02' => '100',
        'tax_rate' => '10.0',
        'delivery_fee' => '100',
    ];

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        // 会員管理会員登録・編集
        $this->form = $this->formFactory
            ->createBuilder(ProductClassType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    public function testInValidData()
    {
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidStockNotNumeric()
    {
        $this->formData['stock'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidStockHasMinus()
    {
        $this->formData['stock'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidSaleLimitOverMaxLength()
    {
        $this->formData['sale_limit'] = '12345678910'; //Max 10

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidSaleLimitNotNumeric()
    {
        $this->formData['sale_limit'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidSaleLimitHasMinus()
    {
        $this->formData['sale_limit'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice01OverMaxLength()
    {
        $this->formData['price01'] = '12345678910'; //Max 10

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice01NotNumeric()
    {
        $this->formData['price01'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice01HasMinus()
    {
        $this->formData['price01'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice02Blank()
    {
        $this->formData['price02'] = ''; //Max 10

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice02OverMaxLength()
    {
        $this->formData['price02'] = '12345678910'; //Max 10

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice02NotNumeric()
    {
        $this->formData['price02'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice02HasMinus()
    {
        $this->formData['price02'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTaxRateOverMinLength()
    {
        $this->formData['tax_rate'] = str_repeat('2', 101);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTaxRateNotNumeric()
    {
        $this->formData['tax_rate'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTaxRateHasMinus()
    {
        $this->formData['tax_rate'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDeliveryFeeNotNumeric()
    {
        $this->formData['delivery_fee'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDeliveryFeeHasMinus()
    {
        $this->formData['delivery_fee'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
