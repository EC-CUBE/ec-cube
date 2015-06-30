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


namespace Eccube\Tests\Form\Type;


class ShopMasterTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{

    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'company_name' => '株式会社ロックオン',
        'company_kana' => 'カブシキガイシャロックオン',
        'shop_name' => 'EC-CUBE Test ショップ',
        'shop_kana' => 'イーシーキュウブテストショップ',
        'shop_name_eng' => 'EC-CUBE Test Shop',
        'zip' => array(
            'zip01' => '530',
            'zip02' => '0001',
        ),
        'address' => array(
            'pref' => '27',
            'addr01' => '大阪市北区梅田2-4-9',
            'addr02' => 'ブリーゼタワー13F',
        ),
        'tel' => array(
            'tel01' => '06',
            'tel02' => '4795',
            'tel03' => '7500',
        ),
        'fax' => array(
            'fax01' => '06',
            'fax02' => '4795',
            'fax03' => '7501',
        ),
        'business_hour' => '9:00～18:00',
        'email01' => 'takahashi1@lockon.co.jp',
        'email02' => 'takahashi2@lockon.co.jp',
        'email03' => 'takahashi3@lockon.co.jp',
        'email04' => 'takahashi4@lockon.co.jp',
        'good_traded' => 'インテリグッズ',
        'message' => '立方隊長が集めた\nワールドワイドなインテリグッズ',
        'delivery_free_amount' => '1000',
        'latitude' => '34.4138',
        'longitude' => '135.3008',
        'delivery_free_quantity' => 100,
        'option_multiple_shipping' => 1,
        'option_mypage_order_status_display' => 1,
        'nostock_hidden' => 1,
        'option_favorite_product' => 1,
        'option_product_delivery_fee' => 1,
        'option_delivery_fee' => 1,
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('shop_master', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidCompanyName_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['stext_len']) . 'S';

        $this->formData['company_name'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidCompanyName_MaxLengthValid()
    {
        $str = str_repeat('S', $this->app['config']['stext_len']);

        $this->formData['company_name'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidCompanyKana_KanaCheck()
    {
        $this->formData['company_kana'] = '株式会社ロックオン';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidCompanyKana_MaxLengthInvalid()
    {
        $str = str_repeat('ア', $this->app['config']['stext_len']) . 'ア';

        $this->formData['company_kana'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidCompanyKana_MaxLengthValid()
    {
        $str = str_repeat('ア', $this->app['config']['stext_len']);

        $this->formData['company_kana'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidShopName_NotBlank()
    {
        $this->formData['shop_name'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidShopName_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['stext_len']) . 'S';

        $this->formData['shop_name'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidShopName_MaxLengthValid()
    {
        $str = str_repeat('S', $this->app['config']['stext_len']);

        $this->formData['shop_name'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidShopKana_KanaCheck()
    {
        $this->formData['shop_kana'] = '株式会社ロックオン';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidShopKana_MaxLengthInvalid()
    {
        $str = str_repeat('ア', $this->app['config']['stext_len']) . 'ア';

        $this->formData['shop_kana'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidShopKkana_MaxLengthValid()
    {
        $str = str_repeat('ア', $this->app['config']['stext_len']);

        $this->formData['shop_kana'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidShopNameEng_Graph()
    {
        $this->formData['shop_name_eng'] = 'あいうえお';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidShopNameEng_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['mtext_len']) . 'S';

        $this->formData['shop_name_eng'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidShopNameEng_MaxLengthValid()
    {
        $str = str_repeat('S', $this->app['config']['mtext_len']);

        $this->formData['shop_name_eng'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidZip01_NotBlank()
    {
        $this->formData['zip']['zip01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip01_Number()
    {
        $this->formData['zip']['zip01'] = 'e12';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip01_NumberCount()
    {
        $this->formData['zip']['zip01'] = $this->formData['zip']['zip01'] * 10;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_NotBlank()
    {
        $this->formData['zip']['zip02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_Number()
    {
        $this->formData['zip']['zip02'] = 'e123';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_NumberCount()
    {
        $this->formData['zip']['zip02'] = $this->formData['zip']['zip02'] * 10;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPref_NotBlank()
    {
        $this->formData['pref'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr01_NotBlank()
    {
        $this->formData['address']['addr01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr01_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['mtext_len']) . 'S';

        $this->formData['address']['addr01'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr01_MaxLengthValid()
    {
        $str = str_repeat('S', $this->app['config']['mtext_len']);

        $this->formData['address']['addr01'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidAddr02_NotBlank()
    {
        $this->formData['address']['addr02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr02_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['mtext_len']) . 'S';

        $this->formData['address']['addr02'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr02_MaxLengthValid()
    {
        $str = str_repeat('S', $this->app['config']['mtext_len']);

        $this->formData['address']['addr02'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidEmail01_NotBlank()
    {
        $this->formData['email01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail01_Email()
    {
        $this->formData['email01'] = 'takahashi.lockon.co.jp';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail01_CharCheck()
    {
        $this->formData['email01'] = 'あいうえお';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail02_NotBlank()
    {
        $this->formData['email02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail02_Email()
    {
        $this->formData['email02'] = 'takahashi.lockon.co.jp';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail02_CharCheck()
    {
        $this->formData['email02'] = 'あいうえお';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail03_NotBlank()
    {
        $this->formData['email03'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail03_Email()
    {
        $this->formData['email03'] = 'takahashi.lockon.co.jp';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail03_CharCheck()
    {
        $this->formData['email03'] = 'あいうえお';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail04_NotBlank()
    {
        $this->formData['email04'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail04_Email()
    {
        $this->formData['email04'] = 'takahashi.lockon.co.jp';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail04_CharCheck()
    {
        $this->formData['email04'] = 'あいうえお';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_Number()
    {
        $this->formData['tel']['tel01'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_MaxLengthInvalid()
    {
        $this->formData['tel']['tel01'] = 11111;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_MaxLengthValid()
    {
        $this->formData['tel']['tel01'] = 111;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidTel02_Number()
    {
        $this->formData['tel']['tel02'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_MaxLengthInvalid()
    {
        $this->formData['tel']['tel02'] = 11111;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_MaxLengthValid()
    {
        $this->formData['tel']['tel02'] = 111;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidTel03_Number()
    {
        $this->formData['tel']['tel03'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_MaxLengthInvalid()
    {
        $this->formData['tel']['tel03'] = 11111;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_MaxLengthValid()
    {
        $this->formData['tel']['tel03'] = 1111;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidFax01_Number()
    {
        $this->formData['fax']['fax01'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax01_MaxLengthInvalid()
    {
        $this->formData['fax']['fax01'] = 11111;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax01_MaxLengthValid()
    {
        $this->formData['fax']['fax01'] = 111;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidFax02_Number()
    {
        $this->formData['fax']['fax02'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax02_MaxLengthInvalid()
    {
        $this->formData['fax']['fax02'] = 11111;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax02_MaxLengthValid()
    {
        $this->formData['fax']['fax02'] = 1111;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidFax03_Number()
    {
        $this->formData['fax']['fax03'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax03_MaxLengthInvalid()
    {
        $this->formData['fax']['fax03'] = 11111;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax03_MaxLengthValid()
    {
        $this->formData['fax']['fax03'] = 1111;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidFreeAmount_Number()
    {
        $this->formData['delivery_free_amount'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFreeAmount_MaxLengthInvalid()
    {
        $num = str_repeat('1', $this->app['config']['price_len']) . '1';

        $this->formData['delivery_free_amount'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFreeAmount_MaxLengthValid()
    {
        $num = str_repeat('1', $this->app['config']['price_len']);

        $this->formData['delivery_free_amount'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidBusinessHour_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['stext_len']) . 'S';

        $this->formData['business_hour'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidBusinessHour_MaxLengthValid()
    {
        $str = str_repeat('S', $this->app['config']['stext_len']);

        $this->formData['business_hour'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidGoodTraded_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['lltext_len']) . 'S';

        $this->formData['good_traded'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidGoodTraded_MaxLengthValid()
    {
        $str = str_repeat('S', $this->app['config']['lltext_len']);

        $this->formData['good_traded'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }


    public function testInvalidMessage_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['lltext_len']) . 'S';

        $this->formData['message'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidMessage_MaxLengthValid()
    {
        $str = str_repeat('S', $this->app['config']['lltext_len']);

        $this->formData['message'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLatitude_MaxLengthInvalid()
    {
        $this->formData['latitude'] = '90.000001';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLatitude_MaxLengthValid()
    {
        $this->formData['latitude'] = '90.000000';
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLatitude_MinLengthInvalid()
    {
        $this->formData['latitude'] = '-90.000001';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLatitude_MinLengthValid()
    {
        $this->formData['latitude'] = '-90.000000';
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLatitude_Number()
    {
        $this->formData['latitude'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLongitude_MaxLengthInvalid()
    {
        $this->formData['longitude'] = '180.000001';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLongitude_MaxLengthValid()
    {
        $this->formData['longitude'] = '180.000000';
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLongitude_MinLengthInvalid()
    {
        $this->formData['longitude'] = '-180.000001';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLongitude_MinLengthValid()
    {
        $this->formData['longitude'] = '-180.000000';
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLongitude_Number()
    {
        $this->formData['latitude'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDeliveryFreeQuantity_Number()
    {
        $this->formData['delivery_free_quantity'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidOptionProductDeliveryFee_Number()
    {
        $this->formData['option_product_delivery_fee'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }


    public function testInvalidOptionProductDeliveryFee_MaxInvalid()
    {
        $this->formData['option_product_delivery_fee'] = 1 + 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidOptionProductDeliveryFee_MaxValid()
    {
        $this->formData['option_product_delivery_fee'] = 1;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidOptionProductDeliveryFee_MinInvalid()
    {
        $this->formData['option_product_delivery_fee'] = 0 - 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidOptionProductDeliveryFee_MinValid()
    {
        $this->formData['option_product_delivery_fee'] = 0;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidOptionDeliveryFee_Number()
    {
        $this->formData['option_delivery_fee'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidOptionDeliveryFee_MaxInvalid()
    {
        $this->formData['option_delivery_fee'] = 1 + 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidOptionDeliveryFee_MaxValid()
    {
        $this->formData['option_delivery_fee'] = 1;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidOptionDeliveryFee_MinInvalid()
    {
        $this->formData['option_delivery_fee'] = 0 - 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidOptionDeliveryFee_MinValid()
    {
        $this->formData['option_delivery_fee'] = 0;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidUseMultipleShipping_Number()
    {
        $this->formData['use_multiple_shipping'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }


    public function testInvalidUseMultipleShipping_MaxInvalid()
    {
        $this->formData['option_multiple_shipping'] = 1 + 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidUseMultipleShipping_MaxValid()
    {
        $this->formData['option_multiple_shipping'] = 1;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidUseMultipleShipping_MinInvalid()
    {
        $this->formData['option_multiple_shipping'] = 0 - 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidUseMultipleShipping_MinValid()
    {
        $this->formData['option_multiple_shipping'] = 0;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidMypageOrderStatusDispFlg_Number()
    {
        $this->formData['option_mypage_order_status_display'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }


    public function testInvalidMypageOrderStatusDispFlg_MaxInvalid()
    {
        $this->formData['option_mypage_order_status_display'] = 1 + 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidMypageOrderStatusDispFlg_MaxValid()
    {
        $this->formData['option_mypage_order_status_display'] = 1;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidMypageOrderStatusDispFlg_MinInvalid()
    {
        $this->formData['option_mypage_order_status_display'] = 0 - 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidMypageOrderStatusDispFlg_MinValid()
    {
        $this->formData['option_mypage_order_status_display'] = 0;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidOptionFavoriteProduct_Number()
    {
        $this->formData['option_favorite_product'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }


    public function testInvalidOptionFavoriteProduct_MaxInvalid()
    {
        $this->formData['option_favorite_product'] = 1 + 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidOptionFavoriteProduct_MaxValid()
    {
        $this->formData['option_favorite_product'] = 1;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidOptionFavoriteProduct_MinInvalid()
    {
        $this->formData['option_favorite_product'] = 0 - 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidOptionFavoriteProduct_MinValid()
    {
        $this->formData['option_favorite_product'] = 0;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }


    public function testInvalidNostockHidden_Number()
    {
        $this->formData['nostock_hidden'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNostockHidden_MaxInvalid()
    {
        $this->formData['nostock_hidden'] = 1 + 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNostockHidden_MaxValid()
    {
        $this->formData['nostock_hidden'] = 1;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidNostockHidden_MinInvalid()
    {
        $this->formData['nostock_hidden'] = 0 - 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNostockHidden_MinValid()
    {
        $this->formData['nostock_hidden'] = 0;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }
}
