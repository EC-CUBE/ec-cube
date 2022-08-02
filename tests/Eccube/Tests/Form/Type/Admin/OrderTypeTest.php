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
        'postal_code' => '530-0001',
        'address' => [
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ],
        'phone_number' => '012-345-6789',
        'email' => 'default@example.com',
        'discount' => '1',
        'delivery_fee_total' => '1',
        'charge' => '1',
        'Payment' => '1', // dtb_payment?
        'Shipping' => [
            'name' => [
                'name01' => 'たかはし',
                'name02' => 'しんいち',
            ],
            'kana' => [
                'kana01' => 'タカハシ',
                'kana02' => 'シンイチ',
            ],
            'postal_code' => '530-0001',
            'address' => [
                'pref' => '5',
                'addr01' => '北区',
                'addr02' => '梅田',
            ],
            'phone_number' => '012-345-6789',
            'Delivery' => 1,
        ],
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
        self::$container->get('request_stack')->push(new Request());
    }

    public function testInValidData()
    {
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPhoneNumberBlank()
    {
        $this->formData['phone_number'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form['phone_number']->isValid());
    }

    public function testInvalidPhoneNumberTooLong() {
        $this->formData['phone_number'] = '0123456789012345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['phone_number']->isValid());
    }

    public function testInvalidDiscountOverMaxLength()
    {
        $this->formData['discount'] = '12345678910'; //Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['discount']->isValid());
    }

    public function testInvalidDiscountNotNumeric()
    {
        $this->formData['discount'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['discount']->isValid());
    }

    public function testInValidDiscountHasMinus()
    {
        $this->formData['discount'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['discount']->isValid());
    }

    public function testInvalidDeliveryFeeTotalOverMaxLength()
    {
        $this->formData['delivery_fee_total'] = '12345678910'; //Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['delivery_fee_total']->isValid());
    }

    public function testInvalidDeliveryFeeTotalNotNumeric()
    {
        $this->formData['delivery_fee_total'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['delivery_fee_total']->isValid());
    }

    public function testInValidDeliveryFeeTotalHasMinus()
    {
        $this->formData['delivery_fee_total'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['delivery_fee_total']->isValid());
    }

    public function testInvalidChargeOverMaxLength()
    {
        $this->formData['charge'] = '12345678910'; //Max 9

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['charge']->isValid());
    }

    public function testInvalidChargeNotNumeric()
    {
        $this->formData['charge'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['charge']->isValid());
    }

    public function testInValidChargeHasMinus()
    {
        $this->formData['charge'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['charge']->isValid());
    }

    public function testInvalidPostalCodeToLong()
    {
        $this->formData['postal_code'] = '012345678';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form['postal_code']->isValid());
    }
}
