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

use Eccube\Form\Type\NameType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class NameTypeTest extends AbstractTypeTestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    protected $maxLength = 50;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'name' => [
            'name01' => 'たかはし',
            'name02' => 'しんいち',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->form = $this->formFactory
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('name', NameType::class)
            ->getForm();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->form = null;
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidDataName01MaxLength()
    {
        $data = [
            'name' => [
                'name01' => str_repeat('ア', $this->maxLength + 1),
                'name02' => 'にゅうりょく',
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDataName02MaxLength()
    {
        $data = [
            'name' => [
                'name01' => 'にゅうりょく',
                'name02' => str_repeat('ア', $this->maxLength + 1),
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDataName01HasWhiteSpaceEn()
    {
        $data = [
            'name' => [
                'name01' => 'hoge hoge',
                'name02' => 'にゅうりょく',
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDataName02HasWhiteSpaceEn()
    {
        $data = [
            'name' => [
                'name01' => 'にゅうりょく',
                'name02' => 'hoge hoge',
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDataName01HasWhiteSpaceJa()
    {
        $data = [
            'name' => [
                'name01' => 'hoge　hoge',
                'name02' => 'にゅうりょく',
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDataName02HasWhiteSpaceJa()
    {
        $data = [
            'name' => [
                'name01' => 'にゅうりょく',
                'name02' => 'hoge　hoge',
            ], ];

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }
}
