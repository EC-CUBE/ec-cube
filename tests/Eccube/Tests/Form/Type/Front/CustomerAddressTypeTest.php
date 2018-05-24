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

use Eccube\Form\Type\Front\CustomerAddressType;

class CustomerAddressTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'name' => [
            'name01' => 'たかはし',
            'name02' => 'しんいち',
        ],
        'kana' => [
            'kana01' => 'タカハシ',
            'kana02' => 'シンイチ',
        ],
        'company_name' => '',
        'zip' => [
            'zip01' => '530',
            'zip02' => '0001',
        ],
        'address' => [
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ],
        'tel' => [
            'tel01' => '03',
            'tel02' => '1111',
            'tel03' => '1111',
        ],
        'fax' => [
            'fax01' => '03',
            'fax02' => '1111',
            'fax03' => '1111',
        ],
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(CustomerAddressType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalid_Blank_Name01()
    {
        $this->formData['name']['name01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_Blank_Name02()
    {
        $this->formData['name']['name02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_Blank_Kana01()
    {
        $this->formData['kana']['kana01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_Blank_Kana02()
    {
        $this->formData['kana']['kana02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_Blank_Pref()
    {
        $this->formData['address']['pref'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_Blank_Addr01()
    {
        $this->formData['address']['addr01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_Blank_Addr02()
    {
        $this->formData['address']['addr02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel_Blank()
    {
        $this->formData['tel']['tel01'] = '';
        $this->formData['tel']['tel02'] = '';
        $this->formData['tel']['tel03'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
