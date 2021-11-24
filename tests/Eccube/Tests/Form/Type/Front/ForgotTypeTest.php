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

use Eccube\Form\Type\Front\ForgotType;

class ForgotTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    protected $formData;

    /**
     * 異常系のデータパターンを返す
     *
     * @return array
     */
    public function getInvalidTestData()
    {
        return [
            [
                'data' => [
                    'login_email' => '',
                ],
            ],
            [
                'data' => [
                    'login_email' => 'example',
                ],
            ],
            // [
            //     'data' => [
            //         'login_email' => 'a..a@aa',
            //     ],
            // ],
            // [
            //     'data' => [
            //         'login_email' => 'aa.@aa',
            //     ],
            // ],
            [
                'data' => [
                    'login_email' => 'aa@adf@a.com',
                ],
            ],
        ];
    }

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(ForgotType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    /**
     * @dataProvider getInvalidTestData
     */
    public function testInvalidData($data)
    {
        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidBlank()
    {
        $this->formData['login_email'] = 'example@example.com';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testMailNoRFC()
    {
        $this->formData['login_email'] = 'aa..@example.com';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }
}
