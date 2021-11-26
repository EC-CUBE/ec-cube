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

use Eccube\Form\Type\Admin\ShopMasterType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class ShopMasterTypeTest extends AbstractTypeTestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * @var array デフォルト値（正常系）を設定
     */
    protected $formData = [
        /*
        'company_name' => '会社名',
        'company_kana' => 'カナ',
         */
        'shop_name' => '店舗名',
        /*
        'shop_kana' => 'カナ',
        'shop_name_eng' => 'shopname',
        'postal_code' => '530-0001',
        'address' => array(
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ),
         */
        'phone_number' => '012-345-6789',
        /*
        'business_hour' => '店舗営業時間',
         */
        'email01' => 'eccube@example.com',
        'email02' => 'eccube@example.com',
        'email03' => 'eccube@example.com',
        'email04' => 'eccube@example.com',
        'delivery_free_amount' => '1000',
        'delivery_free_quantity' => '1000',
        /*
        'good_traded' => '取り扱い商品',
        'message' => 'メッセージ',
        'option_product_delivery_fee' => '0',
        'option_delivery_fee' => '0',
        'option_customer_activate' => '0',
        'option_mypage_order_status_display' => '0',
        'option_favorite_product' => 0,
        'option_remember_me' => '0',
        'option_nostock_hidden' => '0',
         */
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(ShopMasterType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidPhoneNumberBlank()
    {
        $this->formData['phone_number'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidDeliveryFreeAmountOverMaxLength()
    {
        $this->formData['delivery_free_amount'] = '12345678900';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeAmountNotNumeric()
    {
        $this->formData['delivery_free_amount'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeAmountHasMinus()
    {
        $this->formData['delivery_free_amount'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeQuantityNotNumeric()
    {
        $this->formData['delivery_free_quantity'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeQuantityHasMinus()
    {
        $this->formData['delivery_free_quantity'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidBasicPointRateRangeMin()
    {
        $this->formData['basic_point_rate'] = '0';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidBasicPointRateRangeMax()
    {
        $this->formData['basic_point_rate'] = '100';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidBasicPointRateRangeMin()
    {
        $this->formData['basic_point_rate'] = '-1';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidBasicPointRateRangeMax()
    {
        $this->formData['basic_point_rate'] = '101';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
    public function testInValidGoodTradedMaxLength()
    {
        $this->formData['good_traded'] = str_repeat('1', $this->eccubeConfig['eccube_ltext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
    public function testInValidMessageMaxLength()
    {
        $this->formData['message'] = str_repeat('1', $this->eccubeConfig['eccube_ltext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
