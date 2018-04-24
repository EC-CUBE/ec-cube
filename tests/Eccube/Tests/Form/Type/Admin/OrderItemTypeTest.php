<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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

use Eccube\Form\Type\Admin\OrderItemType;
use Symfony\Component\HttpFoundation\Request;

class OrderItemTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'price' => '10000',
        'quantity'=> '10000',
        'tax_rate' => '10.0',
        'product_name' => 'name1',
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        // 会員管理会員登録・編集
        $this->form = $this->formFactory
            ->createBuilder(OrderItemType::class, null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
        $this->container->get('request_stack')->push(new Request());
    }

    public function testInValidData()
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
