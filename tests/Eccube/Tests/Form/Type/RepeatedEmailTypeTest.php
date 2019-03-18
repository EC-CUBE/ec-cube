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

use Eccube\Form\Type\RepeatedEmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class RepeatedEmailTypeTest extends AbstractTypeTestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * @var array デフォルト値（正常系）を設定
     */
    protected $formData = [
        'email' => [
            'first' => 'eccube@example.com',
            'second' => 'eccube@example.com',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('email', RepeatedEmailType::class, [
            ])
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

    public function testInvalid_NotSameValue()
    {
        $this->formData['email']['second'] = 'eccube3@example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_NotBlank()
    {
        $this->formData['email']['first'] = '';
        $this->formData['email']['second'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_Nihongo()
    {
        $this->formData['email']['first'] = 'あいうえお@example.com';
        $this->formData['email']['second'] = 'あいうえお@example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testMailNoRFC()
    {
        $this->formData['email']['first'] = 'abc..@example.com';
        $this->formData['email']['second'] = 'abc..@example.com';
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }
}
