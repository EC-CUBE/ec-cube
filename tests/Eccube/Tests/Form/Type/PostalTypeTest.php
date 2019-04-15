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

use Eccube\Form\Type\PostalType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class PostalTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'postal_code' => '060-0000',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('postal_code', PostalType::class)
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLengthMax()
    {
        $this->formData['postal_code'] = str_repeat('1', $this->eccubeConfig['eccube_postal_code'] + 1);
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testRequiredAddNotBlank()
    {
        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('postal_code', PostalType::class, [
                'required' => true,
            ])
            ->getForm();

        $this->formData['postal_code'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
