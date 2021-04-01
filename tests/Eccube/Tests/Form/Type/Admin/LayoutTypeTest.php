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
use Eccube\Form\Type\Admin\LayoutType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class LayoutTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'name' => 'テスト用レイアウト',
        'DeviceType' => DeviceType::DEVICE_TYPE_PC,
        'Page' => 2,
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(LayoutType::class, null, [
                'csrf_protection' => false,
                'layout_id' => 2,
            ])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidNameNotBlank()
    {
        $this->formData['name'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDeviceTypeNotBlank()
    {
        $this->formData['DeviceType'] = null;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPageInvalid()
    {
        $PageLayout = $this->entityManager->getRepository('Eccube\Entity\PageLayout')
            ->findOneBy([], ['page_id' => 'DESC']);
        $id = $PageLayout->getPageId() + 1;

        $this->formData['Page'] = $id;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }
}
