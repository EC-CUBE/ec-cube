<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Form\Type\Admin;

use Symfony\Component\HttpFoundation\Request;

class ProductClassTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'stock' => '100',
        'sale_limit' => '100',
        'price01' => '100',
        'price02' => '100',
        'tax_rate' => '10.0',
        'delivery_fee' => '100',
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        // 会員管理会員登録・編集
        $this->form = $this->app['form.factory']
            ->createBuilder('admin_product_class', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testInValidData()
    {
        $this->app['request'] = new Request();
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidStock_NotNumeric()
    {
        $this->app['request'] = new Request();
        $this->formData['stock'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidStock_HasMinus()
    {
        $this->app['request'] = new Request();
        $this->formData['stock'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidSaleLimit_OverMaxLength()
    {
        $this->app['request'] = new Request();
        $this->formData['sale_limit'] = '12345678910'; //Max 10

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidSaleLimit_NotNumeric()
    {
        $this->app['request'] = new Request();
        $this->formData['sale_limit'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidSaleLimit_HasMinus()
    {
        $this->app['request'] = new Request();
        $this->formData['sale_limit'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice01_OverMaxLength()
    {
        $this->app['request'] = new Request();
        $this->formData['price01'] = '12345678910'; //Max 10

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice01_NotNumeric()
    {
        $this->app['request'] = new Request();
        $this->formData['price01'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice01_HasMinus()
    {
        $this->app['request'] = new Request();
        $this->formData['price01'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice02_Blank()
    {
        $this->app['request'] = new Request();
        $this->formData['price02'] = ''; //Max 10

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice02_OverMaxLength()
    {
        $this->app['request'] = new Request();
        $this->formData['price02'] = '12345678910'; //Max 10

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice02_NotNumeric()
    {
        $this->app['request'] = new Request();
        $this->formData['price02'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrice02_HasMinus()
    {
        $this->app['request'] = new Request();
        $this->formData['price02'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTaxRate_OverMinLength()
    {
        $this->app['request'] = new Request();
        $this->formData['tax_rate'] = str_repeat('2', 101);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTaxRate_NotNumeric()
    {
        $this->app['request'] = new Request();
        $this->formData['tax_rate'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTaxRate_HasMinus()
    {
        $this->app['request'] = new Request();
        $this->formData['tax_rate'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDeliveryFee_NotNumeric()
    {
        $this->app['request'] = new Request();
        $this->formData['delivery_fee'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDeliveryFee_HasMinus()
    {
        $this->app['request'] = new Request();
        $this->formData['delivery_fee'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
