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

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Form\Type\Admin\MemberType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class MemberTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'name' => 'タカハシ',
        'department' => 'EC-CUBE事業部',
        'login_id' => 'takahashi',
        'password' => [
            'first' => 'password',
            'second' => 'password',
        ],
        'Authority' => 1,
        'Work' => 1,
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(MemberType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidName_NotBlank()
    {
        $this->formData['name'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName_MaxLengthInvalid()
    {
        $name = str_repeat('S', $this->eccubeConfig['eccube_stext_len']).'S';

        $this->formData['name'] = $name;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName_MaxLengthValid()
    {
        $name = str_repeat('S', $this->eccubeConfig['eccube_stext_len']);

        $this->formData['name'] = $name;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidDepartment_MaxLengthInvalid()
    {
        $department = str_repeat('S', $this->eccubeConfig['eccube_stext_len']).'S';

        $this->formData['department'] = $department;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDepartment_MaxLengthValid()
    {
        $department = str_repeat('S', $this->eccubeConfig['eccube_stext_len']);

        $this->formData['department'] = $department;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLoginId_NotBlank()
    {
        $this->formData['login_id'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLoginId_AlnumCheck()
    {
        $this->formData['login_id'] = 'あいうえお';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPassword_NoBlank()
    {
        $this->formData['password']['first'] = '';
        $this->formData['password']['second'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPassword_Invalid()
    {
        $this->formData['password']['first'] = '12345';
        $this->formData['password']['second'] = '54321';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPassword_Graph()
    {
        $this->formData['password']['first'] = 'あいうえお';
        $this->formData['password']['second'] = 'あいうえお';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAuthority_NotBlank()
    {
        $this->formData['Authority'] = null;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAuthority_Invalid()
    {
        $Authority = $this->entityManager->getRepository('Eccube\Entity\Master\Authority')
            ->findOneBy([], ['id' => 'DESC']);
        $id = $Authority->getId() + 1;

        $this->formData['Authority'] = $id;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidWork_NotBlank()
    {
        $this->formData['Work'] = null;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidWork_Invalid()
    {
        $Work = $this->entityManager->getRepository('Eccube\Entity\Master\Work')
            ->findOneBy([], ['id' => 'DESC']);
        $id = $Work->getId() + 1;

        $this->formData['Work'] = $id;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }
}
