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

use Eccube\Form\Type\Admin\CaptchaType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class CaptchaTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /**
     * getValidTestData
     *
     * @return array
     */
    public function getValidTestData()
    {
        return [
            [
                ['captcha' => 'a'],
            ],
            [
                ['captcha' => 'ABCXYZabczyz013'],
            ],
        ];
    }

    public function setUp()
    {
        parent::setUp();

        $this->form = $this->formFactory
            ->createBuilder(CaptchaType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    /**
     * @dataProvider getValidTestData
     */
    public function testValidData($data)
    {
        $this->form->submit($data);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidData_Blank()
    {
        $this->form->submit(['captcha' => '']);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_SpecialChar()
    {
        $this->form->submit(['captcha' => '-']);
        $this->assertFalse($this->form->isValid());
    }
}
