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

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Entity\Master\DeviceType;
use Eccube\Form\Type\Admin\BlockType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class BlockTypeTest extends AbstractTypeTestCase
{
    /** @var FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'name' => 'new/Block_1',
        'file_name' => 'file_name',
        'block_html' => '<p>test</p>',
        'DeviceType' => DeviceType::DEVICE_TYPE_MB,
        'id' => 1,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        // ブロック登録・編集
        $this->form = $this->formFactory
            ->createBuilder(BlockType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
        static::getContainer()->get('request_stack')->push(new Request());
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidNameBlank()
    {
        $this->formData['name'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form['name']->isValid());
    }

    public function testInvalidNameMaxLengthInvalid()
    {
        $str = str_repeat('S', $this->eccubeConfig['eccube_stext_len']).'S';
        $this->formData['name'] = $str;

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['name']->isValid());
    }

    public function testInvalidNameMaxLengthValid()
    {
        $str = str_repeat('S', $this->eccubeConfig['eccube_stext_len']);
        $this->formData['name'] = $str;

        $this->form->submit($this->formData);
        $this->assertTrue($this->form['name']->isValid());
    }

    public function testInvalidFileNameBlank()
    {
        $this->formData['file_name'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form['file_name']->isValid());
    }

    public function testInvalidFileNameMaxLengthInvalid()
    {
        $str = str_repeat('S', $this->eccubeConfig['eccube_stext_len']).'S';
        $this->formData['file_name'] = $str;

        $this->form->submit($this->formData);
        $this->assertFalse($this->form['file_name']->isValid());
    }

    public function testInvalidFileNameMaxLengthValid()
    {
        $str = str_repeat('S', $this->eccubeConfig['eccube_stext_len']);
        $this->formData['file_name'] = $str;

        $this->form->submit($this->formData);
        $this->assertTrue($this->form['file_name']->isValid());
    }

    public function testInvalidFileNameCharacter()
    {
        $this->formData['file_name'] = 'new/Block_1.*{;';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form['file_name']->isValid());
    }

    public function testInvalidFileNameContinuousSlashes()
    {
        $this->formData['file_name'] = 'new//Block_1';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form['file_name']->isValid());
    }

    public function testInvalidBlockHtmlBlank()
    {
        $this->formData['block_html'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form['block_html']->isValid());
    }
}
