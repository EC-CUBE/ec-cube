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

namespace Eccube\Tests\Form\Type\Front;

use Eccube\Form\Type\Front\CustomerLoginType;
use Symfony\Component\HttpFoundation\Request;

class CustomerLoginTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'login_email' => 'eccube@example.com',
        'login_pass' => '111111111',
    ];

    public function setUp()
    {
        parent::setUp();

        $request = Request::createFromGlobals();
        $this->container->get('request_stack')->push($request);

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(CustomerLoginType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidEmail_Blank()
    {
        $this->formData['login_email'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPass_Blank()
    {
        $this->formData['login_pass'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testMailNoRFC()
    {
        $this->formData['login_email'] = 'aa..@example.com';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }
}
