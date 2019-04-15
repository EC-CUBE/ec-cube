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

use Eccube\Form\Type\AddressType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class AddressTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'address' => [
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ],
    ];

    public function setUp()
    {
        parent::setUp();

        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('address', AddressType::class)
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidData_Addr01_MaxLength()
    {
        $data = [
            'address' => [
                'pref' => '1',
                'addr01' => str_repeat('ア', $this->eccubeConfig['eccube_address1_len'] + 1),
                'addr02' => 'にゅうりょく',
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Addr02_MaxLength()
    {
        $data = [
            'address' => [
                'pref' => '1',
                'addr01' => 'にゅうりょく',
                'addr02' => str_repeat('ア', $this->eccubeConfig['eccube_address2_len'] + 1),
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Pref_String()
    {
        $this->formData['address']['pref'] = 'aa';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Pref_NonexistentValue()
    {
        $this->formData['address']['pref'] = '99'; // smallint以上の値だとpostgresが落ちる

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testRequiredAddNotBlank_Pref()
    {
        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('address', AddressType::class, [
                'required' => true,
            ])
            ->getForm();

        $this->formData['address']['pref'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testRequiredAddNotBlank_Addr01()
    {
        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('address', AddressType::class, [
                'required' => true,
            ])
            ->getForm();

        $this->formData['address']['addr01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testRequiredAddNotBlank_Addr02()
    {
        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('address', AddressType::class, [
                'required' => true,
            ])
            ->getForm();

        $this->formData['address']['addr02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
