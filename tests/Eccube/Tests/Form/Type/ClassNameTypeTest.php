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

use Eccube\Form\Type\Admin\ClassNameType;

class ClassNameTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'display_name' => '形状'
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(ClassNameType::class, null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidName_NotBlank()
    {
        $this->formData['display_name'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName_SptabCheck()
    {
        $this->formData['display_name'] = '     ';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName_MaxLengthInvalid()
    {
        $name = str_repeat('S', $this->eccubeConfig['eccube_stext_len']) . 'S';

        $this->formData['display_name'] = $name;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName_MaxLengthValid()
    {
        $name = str_repeat('S', $this->eccubeConfig['eccube_stext_len']);

        $this->formData['display_name'] = $name;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

}
