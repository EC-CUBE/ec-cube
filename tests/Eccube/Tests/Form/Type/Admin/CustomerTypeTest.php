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


class CustomerTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{

    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'name' => array(
            'name01' => '高橋',
            'name02' => '慎一',
        ),
        'kana' => array(
            'kana01' => 'タカハシ',
            'kana02' => 'シンイチ',
        ),
        'zip' => array(
            'zip01' => '530',
            'zip02' => '0001',
        ),
        'company_name' => '株式会社ロックオン',
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
        'email' => 'takahashi@lockon.co.jp',
        'sex' => 1,
        'job' => 3,
        'birth' => array(
            'year' => 1997,
            'month' => 1,
            'day' => 12
        ),
        'password' => 'abcd1234',
        'status' => 1,
        'note' => ''
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('admin_customer', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidStatus_NotBlank()
    {
        self::markTestSkipped();
        $this->formData['status'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidStatus_Number()
    {
        $this->formData['status'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNote_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['ltext_len'] + 1);

        $this->formData['note'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNote_MaxLengthValid()
    {
        $str = str_repeat('S', $this->app['config']['ltext_len']);

        $this->formData['note'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPoint_NotBlank()
    {
        $this->formData['point'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPoint_Number()
    {
        $this->formData['point'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_SptabCheck()
    {
        $this->formData['email'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_EmailCheck()
    {
        $this->formData['email'] = 'takahashilockon.co.jp';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName01_NotBlank()
    {
        $this->formData['name']['name01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName01_SptabCheck()
    {
        $this->formData['name']['name01'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName01_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['stext_len'] + 1);

        $this->formData['name']['name01'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName01_MaxLengthValid()
    {
        // 修正漏れ name1とname2合わせてstext_lenの実装になっている
        self::markTestSkipped();
        $str = str_repeat('S', $this->app['config']['stext_len']);

        $this->formData['name']['name01'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidName02_NotBlank()
    {
        $this->formData['name']['name02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName02_SptabCheck()
    {
        $this->formData['name']['name02'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName02_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['stext_len'] + 1);

        $this->formData['name']['name02'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName02_MaxLengthValid()
    {
        // 修正漏れ name1とname2合わせてstext_lenの実装になっている
        self::markTestSkipped();
        $str = str_repeat('S', $this->app['config']['stext_len']);

        $this->formData['name']['name02'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidCompanyName_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['stext_len'] + 1);

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

    public function testInvalidKana01_NotBlank()
    {
        $this->formData['kana']['kana01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana01_SptabCheck()
    {
        $this->formData['kana']['kana01'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana01_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['stext_len'] + 1);

        $this->formData['kana']['kana01'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana01_MaxLengthValid()
    {
        // 修正漏れ kana1とkana2合わせてstext_lenの実装になっている
        self::markTestSkipped();
        $str = str_repeat('S', $this->app['config']['stext_len']);

        $this->formData['kana']['kana01'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidKana01_KanaCheck()
    {
        // 修正漏れ カナチェックをしていない
        self::markTestSkipped();
        $this->formData['kana']['kana01'] = '株式会社ロックオン';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana02_NotBlank()
    {
        $this->formData['kana']['kana02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana02_SptabCheck()
    {
        $this->formData['kana']['kana02'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana02_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['stext_len'] + 1);

        $this->formData['kana']['kana02'] = $str;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana02_MaxLengthValid()
    {
        // 修正漏れ kana1とkana2合わせてstext_lenの実装になっている
        self::markTestSkipped();

        $str = str_repeat('S', $this->app['config']['stext_len']);

        $this->formData['kana']['kana02'] = $str;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidKana02_KanaCheck()
    {
        // 修正漏れ カナチェックしていない
        self::markTestSkipped();

        $this->formData['kana']['kana02'] = '株式会社ロックオン';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip01_NotBlank()
    {
        $this->formData['zip']['zip01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip01_SptabCheck()
    {
        $this->formData['zip']['zip01'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip01_Number()
    {
        $this->formData['zip']['zip01'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_NotBlank()
    {
        $this->formData['zip']['zip02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_SptabCheck()
    {
        $this->formData['zip']['zip02'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_Number()
    {
        $this->formData['zip']['zip02'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidCountryId_Number()
    {
        $this->formData['country_id'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPref_NotBlank()
    {
        // 修正漏れ 都道府県にNullを許している
        self::markTestSkipped();

        $this->formData['address']['pref'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr01_NotBlank()
    {
        $this->formData['address']['addr01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr01_SptabCheck()
    {
        $this->formData['address']['addr01'] = '     ';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr01_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['mtext_len'] + 1);

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

    public function testInvalidAddr02_SptabCheck()
    {
        $this->formData['address']['addr02'] = '     ';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr02_MaxLengthInvalid()
    {
        $str = str_repeat('S', $this->app['config']['mtext_len'] + 1);

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

    public function testInvalidTel01_NotBlank()
    {
        // 修正漏れ Nullを許している
        self::markTestSkipped();

        $this->formData['tel']['tel01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_SptabCheck()
    {
        // 修正漏れ スペースを許している
        self::markTestSkipped();

        $this->formData['tel']['tel01'] = '     ';
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
        $num = str_repeat('1', 3 + 1);

        $this->formData['tel']['tel01'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_MaxLengthValid()
    {
        $num = str_repeat('1', 3);

        $this->formData['tel']['tel01'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidTel02_NotBlank()
    {
        // 修正漏れ Nullを許している
        self::markTestSkipped();

        $this->formData['tel']['tel02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_SptabCheck()
    {
        // 修正漏れ スペースを許している
        self::markTestSkipped();

        $this->formData['tel']['tel02'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_Number()
    {
        $this->formData['tel']['tel02'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_MaxLengthInvalid()
    {
        $num = str_repeat('1', 4 + 1);

        $this->formData['tel']['tel02'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_MaxLengthValid()
    {
        $num = str_repeat('1', 4);

        $this->formData['tel']['tel02'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidTel03_NotBlank()
    {
        // 修正漏れ Nullを許している
        self::markTestSkipped();

        $this->formData['tel']['tel03'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_SptabCheck()
    {
        // 修正漏れ スペースを許している
        self::markTestSkipped();

        $this->formData['tel']['tel03'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_Number()
    {
        $this->formData['tel']['tel03'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_MaxLengthInvalid()
    {
        $num = str_repeat('1', 4 + 1);

        $this->formData['tel']['tel03'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_MaxLengthValid()
    {
        $num = str_repeat('1', 4);

        $this->formData['tel']['tel03'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidFax01_SptabCheck()
    {
        // 修正漏れ スペースを許している
        self::markTestSkipped();

        $this->formData['fax']['fax01'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax01_Number()
    {
        $this->formData['fax']['fax01'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax01_MaxLengthInvalid()
    {
        $num = str_repeat('1', 3 + 1);

        $this->formData['fax']['fax01'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax01_MaxLengthValid()
    {
        $num = str_repeat('1', 3);

        $this->formData['fax']['fax01'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidFax02_SptabCheck()
    {
        // 修正漏れ スペースを許している
        self::markTestSkipped();

        $this->formData['fax']['fax02'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax02_Number()
    {
        $this->formData['fax']['fax02'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax02_MaxLengthInvalid()
    {
        $num = str_repeat('1', 4 + 1);

        $this->formData['fax']['fax02'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax02_MaxLengthValid()
    {
        $num = str_repeat('1', 4);

        $this->formData['fax']['fax02'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidFax03_SptabCheck()
    {
        // 修正漏れ スペースを許している
        self::markTestSkipped();

        $this->formData['fax']['fax03'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax03_Number()
    {
        $this->formData['fax']['fax03'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax03_MaxLengthInvalid()
    {
        $num = str_repeat('1', 4 + 1);

        $this->formData['fax']['fax03'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidFax03_MaxLengthValid()
    {
        $num = str_repeat('1', 4);

        $this->formData['fax']['fax03'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPassword_NotBlank()
    {
        $this->formData['password'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPassword_SptabCheck()
    {
        $this->formData['password'] = '     ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPassword_GraphCheck()
    {
        $this->formData['password'] = 'あ';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPassword_MaxLengthInvalid()
    {
        $this->formData['password'] = str_repeat('S', 50 + 1);
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPassword_MaxLengthValid()
    {
        $this->formData['password'] = str_repeat('S', 50);
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPassword_MinLengthInvalid()
    {
        $this->formData['password'] = str_repeat('S', 4 - 1);
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPassword_MinLengthValid()
    {
        $this->formData['password'] = str_repeat('S', 4);
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidSex_Number()
    {
        $this->formData['sex'] = 'e1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidSex_MaxInvalid()
    {
        $this->formData['sex'] = 2 + 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidSex_MaxValid()
    {
        $this->formData['sex'] = 2;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidSex_MinInvalid()
    {
        $this->formData['sex'] = 1 - 1;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidSex_MinValid()
    {
        $this->formData['sex'] = 1;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidJob_MaxLengthInvalid()
    {
        self::markTestSkipped();

        $num = str_repeat('1', $this->app['config']['int_len'] + 1);

        $this->formData['job'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidJob_MaxLengthValid()
    {
        self::markTestSkipped();

        $num = str_repeat('1', $this->app['config']['int_len']);

        $this->formData['job'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidYear_MaxLengthInvalid()
    {
        $num = 1999 * 10;

        $this->formData['birth']['year'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidYear_MaxLengthValid()
    {
        $num = 1999;

        $this->formData['birth']['year'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidMonth_MaxLengthInvalid()
    {
        $num = 12 + 1;

        $this->formData['birth']['month'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidMonth_MaxLengthValid()
    {
        $num = 12;

        $this->formData['birth']['month'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidDay_MaxLengthInvalid()
    {
        $num = 31 + 1;

        $this->formData['birth']['day'] = $num;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDay_MaxLengthValid()
    {
        $num = 31;

        $this->formData['birth']['day'] = $num;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }
}
