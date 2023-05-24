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
        'email' => 'default1@example.com',
        'sex' => 1,
        'job' => 1,
        'birth' => '1983-2-14',
        'plain_password' => [
            'first' => 'password1234',
            'second' => 'password1234',
        ],
        'status' => 1,
        'note' => 'note',
        'point' => '0',
    ];

    protected function setUp(): void
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

    public function testInvalidPhoneNumberBlank()
    {
        $this->formData['phone_number'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName01Blank()
    {
        $this->formData['name']['name01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName02Blank()
    {
        $this->formData['name']['name02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana01Blank()
    {
        $this->formData['kana']['kana01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana02Blank()
    {
        $this->formData['kana']['kana02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidCompanyNameBlank()
    {
        $this->formData['company_name'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPostalCodeBlank()
    {
        $this->formData['postal_code'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPrefBlank()
    {
        $this->formData['address']['pref'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr01Blank()
    {
        $this->formData['address']['addr01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAddr02Blank()
    {
        $this->formData['address']['addr02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidemailBlank()
    {
        $this->formData['email'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmailNihongo()
    {
        $this->formData['email'] = 'あいうえお@example.com';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmailRFC2822()
    {
        $this->formData['email'] = 'abc..@example.com';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidEmailLong()
    {
        $this->formData['email'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTU@a';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidJobBlank()
    {
        $this->formData['job'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidSexBlank()
    {
        $this->formData['sex'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPasswordBlank()
    {
        $this->formData['password']['first'] = '';
        $this->formData['password']['second'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidPasswordMinLength()
    {
        $this->formData['plain_password']['first'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len'] - 1).'1';
        $this->formData['plain_password']['second'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len'] - 1).'1';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPasswordMinLength()
    {
        $password = str_repeat('a', $this->eccubeConfig['eccube_password_min_len'] - 1);

        $this->formData['plain_password']['first'] = $password;
        $this->formData['plain_password']['second'] = $password;

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidPasswordMaxLength()
    {
        $this->formData['plain_password']['first'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len'] - 1).'1';
        $this->formData['plain_password']['second'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len'] - 1).'1';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPasswordMaxLength()
    {
        $password = str_repeat('a', $this->eccubeConfig['eccube_password_max_len'] + 1);

        $this->formData['plain_password']['first'] = $password;
        $this->formData['plain_password']['second'] = $password;

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPasswordAlphabetOnly()
    {
        $password = str_repeat('a', $this->eccubeConfig['eccube_password_max_len']);

        $this->formData['plain_password']['first'] = $password;
        $this->formData['plain_password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPasswordNumericOnly()
    {
        $password = str_repeat('1', $this->eccubeConfig['eccube_password_max_len']);

        $this->formData['plain_password']['first'] = $password;
        $this->formData['plain_password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPasswordEqualEmail()
    {
        $this->formData['plain_password']['first'] = $this->formData['email'];
        $this->formData['plain_password']['second'] = $this->formData['email'];

        $this->form->submit($this->formData);
        $this->assertEquals(trans('common.password_eq_email'), $this->form->getErrors(true)[0]->getMessage());
    }

    public function testInvalidStatusBlank()
    {
        $this->formData['status'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNoteBlank()
    {
        $this->formData['note'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }


    public function testInvalidPointPlus()
    {
        $this->formData['point'] = '123';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPointMinus()
    {
        $this->formData['point'] = '-123';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPointBlank()
    {
        $this->formData['point'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPointMin()
    {
        $this->formData['point'] = '-1234567890123';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPointMax()
    {
        $this->formData['point'] = '1234567890123';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
