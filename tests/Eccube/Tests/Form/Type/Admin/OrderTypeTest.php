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

use Eccube\Form\Type\Admin\OrderType;
use Symfony\Component\HttpFoundation\Request;

class OrderTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'name' => [
            'name01' => 'たかはし',
            'name02' => 'しんいち',
        ],
        'kana' => [
            'kana01' => 'タカハシ',
            'kana02' => 'シンイチ',
        ],
        'company_name' => '株式会社テストショップ',
        'zip' => [
            'zip01' => '530',
            'zip02' => '0001',
        ],
        'address' => [
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ],
        'tel' => [
            'tel01' => '012',
            'tel02' => '345',
            'tel03' => '6789',
        ],
        'fax' => [
            'fax01' => '112',
            'fax02' => '345',
            'fax03' => '6789',
        ],
        'email' => 'default@example.com',
        'discount' => '1',
        'delivery_fee_total' => '1',
        'charge' => '1',
        'Payment' => '1', // dtb_payment?
        'Shippings' => [],
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        // 会員管理会員登録・編集
        $this->form = $this->formFactory
            ->createBuilder(OrderType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
        $this->container->get('request_stack')->push(new Request());
    }

    public function testInValidData()
    {
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel_Blank()
    {
        $this->formData['tel']['tel01'] = '';
        $this->formData['tel']['tel02'] = '';
        $this->formData['tel']['tel03'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDiscount_OverMaxLength()
    {
        $this->formData['discount'] = '12345678910'; //Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDiscount_NotNumeric()
    {
        $this->formData['discount'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDiscount_HasMinus()
    {
        $this->formData['discount'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDeliveryFeeTotal_OverMaxLength()
    {
        $this->formData['delivery_fee_total'] = '12345678910'; //Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDeliveryFeeTotal_NotNumeric()
    {
        $this->formData['delivery_fee_total'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFeeTotal_HasMinus()
    {
        $this->formData['delivery_fee_total'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidCharge_OverMaxLength()
    {
        $this->formData['charge'] = '12345678910'; //Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidCharge_NotNumeric()
    {
        $this->formData['charge'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidCharge_HasMinus()
    {
        $this->formData['charge'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
