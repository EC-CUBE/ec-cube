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

namespace Eccube\Tests\Form\Type;

use Eccube\Form\Type\Admin\ClassNameType;

class ClassNameTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'name' => '形状',
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(ClassNameType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidName_NotBlank()
    {
        $this->formData['name'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName_SptabCheck()
    {
        $this->formData['name'] = '     ';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName_MaxLengthInvalid()
    {
        $name = str_repeat('S', $this->eccubeConfig['eccube_stext_len']).'S';

        $this->formData['name'] = $name;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName_MaxLengthValid()
    {
        $name = str_repeat('S', $this->eccubeConfig['eccube_stext_len']);

        $this->formData['name'] = $name;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }
}
