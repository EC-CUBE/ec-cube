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

use Eccube\Form\Type\Front\PasswordResetType;

class PasswordResetTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array */
    protected $formData = [
        'login_email' => 'hideki_okajima@ec-cube.co.jp',
        'password' => [
            'first' => 'password',
            'second' => 'password',
        ],
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(PasswordResetType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidPasswordEqualEmail()
    {
        $this->formData['password']['first'] = $this->formData['login_email'];
        $this->formData['password']['second'] = $this->formData['login_email'];

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
