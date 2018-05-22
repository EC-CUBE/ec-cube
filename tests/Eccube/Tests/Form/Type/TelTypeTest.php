<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Tests\Form\Type;

use Eccube\Form\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class TelTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'tel' => [
            'tel01' => '012',
            'tel02' => '3456',
            'tel03' => '6789',
        ],
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
                    'tel' => [
                        'tel01' => '01',
                        'tel02' => '2345',
                        'tel03' => '6789',
                    ],
                ],
            ],
            [
                'data' => [
                    'tel' => [
                        'tel01' => '1',
                        'tel02' => '2345',
                        'tel03' => '6789',
                    ],
                ],
            ],
            [
                'data' => [
                    'tel' => [
                        'tel01' => '012',
                        'tel02' => '345',
                        'tel03' => '6789',
                    ],
                ],
            ],
            [
                'data' => [
                    'tel' => [
                        'tel01' => '0124',
                        'tel02' => '56',
                        'tel03' => '7890',
                    ],
                ],
            ],
            [
                'data' => [
                    'tel' => [
                        'tel01' => '01245',
                        'tel02' => '60',
                        'tel03' => '7890',
                    ],
                ],
            ],
            // 携帯,PHS
            [
                'data' => [
                    'tel' => [
                        'tel01' => '090',
                        'tel02' => '1234',
                        'tel03' => '5678',
                    ],
                ],
            ],
            // フリーダイヤル
            [
                'data' => [
                    'tel' => [
                        'tel01' => '0120',
                        'tel02' => '123',
                        'tel03' => '456',
                    ],
                ],
            ],
            [
                'data' => [
                    'tel' => [
                        'tel01' => '０３',
                        'tel02' => '１２３４',
                        'tel03' => '５６７８',
                    ],
                ],
            ],
            // 全部空はOK
            [
                'data' => [
                    'tel' => [
                        'tel01' => '',
                        'tel02' => '',
                        'tel03' => '',
                    ],
                ],
            ],
        ];
    }

    public function setUp()
    {
        parent::setUp();

        $this->form = $this->formFactory->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('tel', TelType::class, [
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

    public function testInvalidTel01_LengthMax()
    {
        $this->formData['tel']['tel01'] = '12345678';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_LengthMax()
    {
        $this->formData['tel']['tel02'] = '12345678';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_LengthMax()
    {
        $this->formData['tel']['tel03'] = '12345678';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_NotNumber()
    {
        $this->formData['tel']['tel01'] = 'aaaa';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_NotNumber()
    {
        $this->formData['tel']['tel02'] = 'aaaa';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_NotNumber()
    {
        $this->formData['tel']['tel03'] = 'aaaa';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel_BlankOne()
    {
        $this->formData['tel']['tel01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testSubmitFromZenToHan()
    {
        $input = [
            'tel' => [
                'tel01' => '１２３４５',
                'tel02' => '１２３４５',
                'tel03' => '６７８９０',
            ], ];

        $output = [
            'tel01' => '12345',
            'tel02' => '12345',
            'tel03' => '67890',
        ];

        $this->form->submit($input);
        $this->assertEquals($output, $this->form->getData());
    }

    public function testRequiredAddNotBlank_Tel()
    {
        $this->form = $this->formFactory->createBuilder(FormType::class)
            ->add('tel', TelType::class, [
                'required' => true,
            ])
            ->getForm();

        $this->formData['tel']['tel01'] = '';
        $this->formData['tel']['tel02'] = '';
        $this->formData['tel']['tel03'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
