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

use Eccube\Form\Type\Admin\AuthenticationType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class AuthenticationTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array */
    protected $formData = [
        'php_path' => '/usr/bin/php',
        'authentication_key' => 'abc123ABCZz',
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(AuthenticationType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidAuthentication()
    {
        $this->formData['authentication_key'] = '-';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPhpPathMaxLengthInvalid()
    {
        $name = str_repeat('S', $this->eccubeConfig['eccube_smtext_len']).'S';

        $this->formData['name'] = $name;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }
}
