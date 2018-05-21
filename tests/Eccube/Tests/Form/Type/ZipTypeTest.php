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

namespace Eccube\Tests\Form\Type;

use Eccube\Form\Type\ZipType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class ZipTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'zip' => [
            'zip01' => '530',
            'zip02' => '0001',
        ],
    ];

    public function setUp()
    {
        parent::setUp();
        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('zip', ZipType::class)
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalideccube_zip01_lengthMin()
    {
        $this->formData['zip']['zip01'] = str_repeat('1', $this->eccubeConfig['eccube_zip01_len'] - 1);
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalideccube_zip01_lengthMax()
    {
        $this->formData['zip']['zip01'] = str_repeat('1', $this->eccubeConfig['eccube_zip01_len'] + 1);
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalideccube_zip02_lengthMin()
    {
        $this->formData['zip']['zip02'] = str_repeat('1', $this->eccubeConfig['eccube_zip02_len'] - 1);
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalideccube_zip02_lengthMax()
    {
        $this->formData['zip']['zip02'] = str_repeat('1', $this->eccubeConfig['eccube_zip02_len'] + 1);
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testRequiredAddNotBlank_Zip01()
    {
        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('zip', ZipType::class, [
                'required' => true,
            ])
            ->getForm();

        $this->formData['zip']['zip01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testRequiredAddNotBlank_Zip02()
    {
        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('zip', ZipType::class, [
                'required' => true,
            ])
            ->getForm();

        $this->formData['zip']['zip02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
