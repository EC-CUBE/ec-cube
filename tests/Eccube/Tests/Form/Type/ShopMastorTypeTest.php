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
        'free_rule' => '1000',
        'latitude' => '135.3008',
        'longitude' => '34.4138',
        'downloadable_days_unlimited' => false,
        'downloadable_days' => 10,
        'deliv_free_amount' => 100,
        'use_multiple_shipping' => 1,
        'forgot_mail' => 1,
        'mypage_order_status_disp_flg' => 1,
        'nostock_hidden' => 1,
        'option_favorite_product' => 1,
        'option_product_deliv_fee' => 1,
        'point_rule' => 1,
        'use_point' => 1,
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
        $this->formData['tel']['tel01'] = 1111;
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
        $this->formData['fax']['fax01'] = 1111;
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

    public function testInvalidFreeRule_Number()
    {
        $this->formData['free_rule'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFreeRule_MaxLengthInvalid()
    {
        $num = str_repeat('1', $this->app['config']['price_len']) . '1';

        $this->formData['free_rule'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFreeRule_MaxLengthValid()
    {
        $num = str_repeat('1', $this->app['config']['price_len']);

        $this->formData['free_rule'] = $num;
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

    public function testInvalidDownloadableDaysUnlimited_Valid()
    {
        $this->formData['downloadable_days_unlimited'] = true;
        $this->formData['downloadable_days'] = '';
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidDownloadableDaysUnlimited_NoBlankInvalid()
    {
        $this->formData['downloadable_days_unlimited'] = false;
        $this->formData['downloadable_days'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDownloadableDaysUnlimited_ZeroCheckInvalid()
    {
        $this->formData['downloadable_days_unlimited'] = false;
        $this->formData['downloadable_days'] = 0;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDownloadableDaysUnlimited_NumCheckInvalid()
    {
        $this->formData['downloadable_days_unlimited'] = false;
        $this->formData['downloadable_days'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDownloadableDaysUnlimited_MaxLengthCheck()
    {
        $this->formData['downloadable_days_unlimited'] = false;

        $num = str_repeat('1', $this->app['config']['download_days_len']) . '1';
        $this->formData['downloadable_days'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLatitude_MaxLengthInvalid()
    {
        $num = str_repeat('1', $this->app['config']['stext_len']) . '1';

        $this->formData['latitude'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLatitude_MaxLengthValid()
    {
        $num = str_repeat('1', $this->app['config']['stext_len']);

        $this->formData['latitude'] = $num;
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
        $num = str_repeat('1', $this->app['config']['stext_len']) . '1';

        $this->formData['longitude'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLongitude_MaxLengthValid()
    {
        $num = str_repeat('1', $this->app['config']['stext_len']);

        $this->formData['longitude'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLongitude_Number()
    {
        $this->formData['latitude'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

}
