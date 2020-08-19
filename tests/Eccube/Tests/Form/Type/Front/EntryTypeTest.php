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

namespace Eccube\Tests\Form\Type\Front;

use Eccube\Form\Type\Front\EntryType;

class EntryTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
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
        'company_name' => '',
        'postal_code' => '530-0001',
        'address' => [
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ],
        'phone_number' => '012-345-6789',
        'email' => [
            'first' => 'eccube@example.com',
            'second' => 'eccube@example.com',
        ],
        'password' => [
            'first' => '12345678',
            'second' => '12345678',
        ],
        'birth' => [
            'year' => '1980',
            'month' => '1',
            'day' => '1',
        ],
        'sex' => 1,
        'job' => 1,
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(EntryType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPhoneNumber_Blank()
    {
        $this->formData['phone_number'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName01_Blank()
    {
        $this->formData['name']['name01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName02_Blank()
    {
        $this->formData['name']['name02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana01_Blank()
    {
        $this->formData['kana']['kana01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana02_Blank()
    {
        $this->formData['kana']['kana02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana01_NotKana()
    {
        $this->formData['kana']['kana01'] = 'aaaa';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana02_NotKana()
    {
        $this->formData['kana']['kana02'] = 'aaaaa';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidCompanyName_Blank()
    {
        $this->formData['company_name'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPostalCode_Blank()
    {
        $this->formData['postal_code'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPref_Blank()
    {
        $this->formData['address']['pref'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr01_Blank()
    {
        $this->formData['address']['addr01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr02_Blank()
    {
        $this->formData['address']['addr02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidemail_Blank()
    {
        $this->formData['email']['first'] = '';
        $this->formData['email']['second'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPassword_EqualEmail()
    {
        $this->formData['password']['first'] = $this->formData['email']['first'];
        $this->formData['password']['second'] = $this->formData['email']['first'];

        $this->form->submit($this->formData);
        $this->assertEquals(trans('common.password_eq_email'), $this->form->getErrors(true)[0]->getMessage());
    }
}
