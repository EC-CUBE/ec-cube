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

    public function testInvalidDataKana01MaxLength()
    {
        $data = [
            'kana' => [
                'kana01' => str_repeat('ア', $this->maxLength + 1),
                'kana02' => 'にゅうりょく',
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDataKana02MaxLength()
    {
        $data = [
            'kana' => [
                'kana01' => 'にゅうりょく',
                'kana02' => str_repeat('ア', $this->maxLength + 1),
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testinvaliddataKana01HaswhitespaceEn()
    {
        $data = [
            'kana' => [
                'kana01' => 'ホゲ ホゲ',
                'kana02' => 'フガフガ',
            ], ];

        $this->form->submit($data);
        $this->assertfalse($this->form->isvalid());
    }

    public function testinvaliddataKana02HaswhitespaceEn()
    {
        $data = [
            'kana' => [
                'kana01' => 'ホゲホゲ',
                'kana02' => 'フガ フガ',
            ], ];

        $this->form->submit($data);
        $this->assertfalse($this->form->isvalid());
    }

    public function testinvaliddataKana01HaswhitespaceJa()
    {
        $data = [
            'kana' => [
                'kana01' => 'ホゲ　ホゲ',
                'kana02' => 'フガフガ',
            ], ];

        $this->form->submit($data);
        $this->assertfalse($this->form->isvalid());
    }

    public function testinvaliddataKana02HaswhitespaceJa()
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
