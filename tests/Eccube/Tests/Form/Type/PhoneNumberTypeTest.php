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

use Eccube\Form\Type\PhoneNumberType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class PhoneNumberTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'phone_number' => '012-345-6789',
    ];

    /**
     * getValidTestData
     *
     * 正常系のデータパターンを返す
     *
     * @return array
     */
    public function getValidTestData()
    {
        return [
            [
                'data' => [
                    'phone_number' => '012-345-6789',
                ],
            ],
            [
                'data' => [
                    'phone_number' => '1-345-6789',
                ],
            ],
            [
                'data' => [
                    'phone_number' => '012-34-6789',
                ],
            ],
            [
                'data' => [
                    'phone_number' => '012-34522-6789',
                ],
            ],
            [
                'data' => [
                    'phone_number' => '01222-345-6789',
                ],
            ],
            // 携帯,PHS
            [
                'data' => [
                    'phone_number' => '012-3455-6789',
                ],
            ],
            // フリーダイヤル
            [
                'data' => [
                    'phone_number' => '0122-345-678',
                ],
            ],
            [
                'data' => [
                    'phone_number' => '０３-１２３４-５６７８',
                ],
            ],
            [
                'data' => [
                    'phone_number' => '０３-12345-12345',
                ],
            ],
            // 全部空はOK
            [
                'data' => [
                    'phone_number' => '',
                ],
            ],
            // max length
            [
                'data' => [
                    'phone_number' => '01234567891011',
                ],
            ],
        ];
    }

    public function setUp()
    {
        parent::setUp();

        $this->form = $this->formFactory->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('phone_number', PhoneNumberType::class, [
                'required' => false,
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

    public function testInvalidLengthMax()
    {
        // > 14 chart
        $this->formData['phone_number'] = '123456789101113';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNotNumber()
    {
        $this->formData['phone_number'] = 'aaaa';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidBlankOne()
    {
        $this->formData['phone_number'] = '';
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testSubmitFromZenToHan()
    {
        $input = [
            'phone_number' => '１２３-１２３４-１２３４',
        ];

        $output = [
            'phone_number' => '12312341234',
        ];

        $this->form->submit($input);
        $this->assertEquals($output, $this->form->getData());
    }

    public function testRequiredAddNotBlank()
    {
        $this->form = $this->formFactory->createBuilder(FormType::class)
            ->add('phone_number', PhoneNumberType::class, [
                'required' => true,
            ])
            ->getForm();

        $this->formData['phone_number'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
