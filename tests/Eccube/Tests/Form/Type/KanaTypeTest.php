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

use Eccube\Form\Type\KanaType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class KanaTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    protected $maxLength = 25;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'kana' => [
            'kana01' => 'たかはし',
            'kana02' => 'しんいち',
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
                    'kana' => [
                        'kana01' => 'たかはし',
                        'kana02' => 'しんいち',
                    ],
                ],
            ],
            [
                'data' => [
                    'kana' => [
                        'kana01' => 'タカハシ',
                        'kana02' => 'しんいち',
                    ],
                ],
            ],
            [
                'data' => [
                    'kana' => [
                        'kana01' => 'たかはし',
                        'kana02' => 'シンイチ',
                    ],
                ],
            ],
            [
                'data' => [
                    'kana' => [
                        'kana01' => str_repeat('ア', $this->maxLength),
                        'kana02' => str_repeat('ア', $this->maxLength),
                    ],
                ],
            ],
        ];
    }

    public function setUp()
    {
        parent::setUp();

        $this->form = $this->formFactory->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('kana', KanaType::class)
            ->getForm();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->form = null;
    }

    /**
     * @dataProvider getValidTestData
     */
    public function testValidData($data)
    {
        $this->form->submit($data);
        $this->assertTrue($this->form->isValid(), (string) $this->form->getErrors(true, false));
    }

    public function testInvalidData_Kana01_MaxLength()
    {
        $data = [
            'kana' => [
                'kana01' => str_repeat('ア', $this->maxLength + 1),
                'kana02' => 'にゅうりょく',
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Kana02_MaxLength()
    {
        $data = [
            'kana' => [
                'kana01' => 'にゅうりょく',
                'kana02' => str_repeat('ア', $this->maxLength + 1),
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testinvaliddata_kana01_haswhitespaceEn()
    {
        $data = [
            'kana' => [
                'kana01' => 'ホゲ ホゲ',
                'kana02' => 'フガフガ',
            ], ];

        $this->form->submit($data);
        $this->assertfalse($this->form->isvalid());
    }

    public function testinvaliddata_kana02_haswhitespaceEn()
    {
        $data = [
            'kana' => [
                'kana01' => 'ホゲホゲ',
                'kana02' => 'フガ フガ',
            ], ];

        $this->form->submit($data);
        $this->assertfalse($this->form->isvalid());
    }

    public function testinvaliddata_kana01_haswhitespaceJa()
    {
        $data = [
            'kana' => [
                'kana01' => 'ホゲ　ホゲ',
                'kana02' => 'フガフガ',
            ], ];

        $this->form->submit($data);
        $this->assertfalse($this->form->isvalid());
    }

    public function testinvaliddata_kana02_haswhitespaceJa()
    {
        $data = [
            'kana' => [
                'kana01' => 'ホゲホゲ',
                'kana02' => 'フガ　フガ',
            ], ];

        $this->form->submit($data);
        $this->assertfalse($this->form->isvalid());
    }

    /**
     * ひらがな入力されてもカタカナで返す
     */
    public function testSubmitFromHiraganaToKana()
    {
        $input = [
            'kana' => [
                'kana01' => 'ひらがな',
                'kana02' => 'にゅうりょく',
            ], ];

        $output = [
            'kana01' => 'ヒラガナ',
            'kana02' => 'ニュウリョク',
        ];

        $this->form->submit($input);
        $this->assertEquals($output, $this->form->getData());
    }
}
