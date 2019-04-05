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

class OrderTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'name' => array(
            'name01' => 'たかはし',
            'name02' => 'しんいち',
        ),
        'kana'=> array(
            'kana01' => 'タカハシ',
            'kana02' => 'シンイチ',
        ),
        'company_name' => '株式会社テストショップ',
        'zip' => array(
            'zip01' => '530',
            'zip02' => '0001',
        ),
        'address' => array(
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ),
        'tel' => array(
            'tel01' => '012',
            'tel02' => '345',
            'tel03' => '6789',
        ),
        'fax' => array(
            'fax01' => '112',
            'fax02' => '345',
            'fax03' => '6789',
        ),
        'email' => 'default@example.com',
        'discount' => '1',
        'delivery_fee_total' => '1',
        'charge' => '1',
        'Payment' => '1', // dtb_payment?
        'OrderDetails' => array(),
        'Shippings' => array(),
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        // 会員管理会員登録・編集
        $this->form = $this->app['form.factory']
            ->createBuilder('order', null, array(
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

    public function testInvalidTel_Blank()
    {
        $this->app['request'] = new Request();
        $this->formData['tel']['tel01'] = '';
        $this->formData['tel']['tel02'] = '';
        $this->formData['tel']['tel03'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDiscount_OverMaxLength()
    {
        $this->app['request'] = new Request();
        $this->formData['discount'] = '12345678910'; //Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDiscount_NotNumeric()
    {
        $this->app['request'] = new Request();
        $this->formData['discount'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDiscount_HasMinus()
    {
        $this->app['request'] = new Request();
        $this->formData['discount'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDeliveryFeeTotal_OverMaxLength()
    {
        $this->app['request'] = new Request();
        $this->formData['delivery_fee_total'] = '12345678910'; //Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDeliveryFeeTotal_NotNumeric()
    {
        $this->app['request'] = new Request();
        $this->formData['delivery_fee_total'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFeeTotal_HasMinus()
    {
        $this->app['request'] = new Request();
        $this->formData['delivery_fee_total'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidCharge_OverMaxLength()
    {
        $this->app['request'] = new Request();
        $this->formData['charge'] = '12345678910'; //Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidCharge_NotNumeric()
    {
        $this->app['request'] = new Request();
        $this->formData['charge'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidCharge_HasMinus()
    {
        $this->app['request'] = new Request();
        $this->formData['charge'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
