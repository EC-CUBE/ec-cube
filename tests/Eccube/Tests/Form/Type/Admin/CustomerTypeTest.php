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

use Eccube\Form\Type\Admin\CustomerType;

class CustomerTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
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
        'sex' => 1,
        'job' => 1,
        'birth' => '1983-2-14',
        'password' => [
            'first' => 'password',
            'second' => 'password',
        ],
        'status' => 1,
        'note' => 'note',
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        // 会員管理会員登録・編集
        $this->form = $this->formFactory
            ->createBuilder(CustomerType::class, null, [
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
        $this->formData['email'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_Nihongo()
    {
        $this->formData['email'] = 'あいうえお@example.com';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_RFC2822()
    {
        $this->formData['email'] = 'abc..@example.com';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidJob_Blank()
    {
        $this->formData['job'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidSex_Blank()
    {
        $this->formData['sex'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPassword_Blank()
    {
        $this->formData['password']['first'] = '';
        $this->formData['password']['second'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidPassword_MinLength()
    {
        $this->formData['password']['first'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len']);
        $this->formData['password']['second'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPassword_MinLength()
    {
        $this->formData['password']['first'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len'] - 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidPassword_MaxLength()
    {
        $this->formData['password']['first'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len']);
        $this->formData['password']['second'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPassword_MaxLength()
    {
        $this->formData['password']['first'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len'] + 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidStatus_Blank()
    {
        $this->formData['status'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNote_Blank()
    {
        $this->formData['note'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }
}
