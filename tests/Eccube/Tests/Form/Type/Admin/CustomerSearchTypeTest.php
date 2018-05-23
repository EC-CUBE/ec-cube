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

use Eccube\Form\Type\Admin\SearchCustomerType;

class CustomerSearchTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(SearchCustomerType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function testTel_ValidData()
    {
        $formData = [
            'tel' => '12345',
        ];

        $this->form->submit($formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testTel_NotValidData()
    {
        //意味あんだか良くわからんが一応書いとく
        $formData = [
            'tel' => '+〇三=abcふれ',
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }
}
