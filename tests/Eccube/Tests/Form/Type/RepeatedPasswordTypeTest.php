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

namespace Eccube\Tests\Form\Type;

use Eccube\Form\Type\RepeatedPasswordType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class RepeatedPasswordTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'password' => [
            'first' => 'eccube@example.com',
            'second' => 'eccube@example.com',
        ],
    ];

    public function setUp()
    {
        parent::setUp();
        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('password', RepeatedPasswordType::class, [])
            ->getForm();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->form = null;
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidNotSameValue()
    {
        $this->formData['password']['second'] = 'eccube3@example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNotBlank()
    {
        $this->formData['password']['first'] = '';
        $this->formData['password']['second'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLengthMin()
    {
        $password = str_repeat('1', $this->eccubeConfig['eccube_password_min_len'] - 1);

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLengthMax()
    {
        $password = str_repeat('1', $this->eccubeConfig['eccube_password_max_len'] + 1);

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidHiragana()
    {
        $password = str_repeat('あ', $this->eccubeConfig['eccube_password_max_len']);

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    /* 環境依存で通るっぽい
    public function testValid_ZenkakuAlpha()
    {
        // これ通っていいのかな?
        $password = str_repeat('Ａ', $this->eccubeConfig['eccube_password_max_len']);

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }
     */

    public function testInvalidSpaceOnly()
    {
        $password = str_repeat(' ', $this->eccubeConfig['eccube_password_max_len']);

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testValidSpace()
    {
        // これも通っていいのか？
        $password = '1234 \n\s\t78';

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }
}
