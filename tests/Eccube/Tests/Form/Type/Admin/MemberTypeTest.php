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


class MemberTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{

    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'name' => 'タカハシ',
        'department' => 'EC-CUBE事業部',
        'login_id' => 'takahashi',
        'password' => array(
            'first' => 'password',
            'second' => 'password',
        ),
        'Authority' => 1,
        'Work' => 1,
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('admin_member', null, array(
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
        $this->formData['name'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName_MaxLengthInvalid()
    {
        $name = str_repeat('S', $this->app['config']['stext_len']) . 'S';

        $this->formData['name'] = $name;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidName_MaxLengthValid()
    {
        $name = str_repeat('S', $this->app['config']['stext_len']);

        $this->formData['name'] = $name;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidDepartment_MaxLengthInvalid()
    {
        $department = str_repeat('S', $this->app['config']['stext_len']) . 'S';

        $this->formData['department'] = $department;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDepartment_MaxLengthValid()
    {
        $department = str_repeat('S', $this->app['config']['stext_len']);

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
        $Authority = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Authority')
            ->findOneBy(array(), array('id' => 'DESC'));
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
        $Work = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Work')
            ->findOneBy(array(), array('id' => 'DESC'));
        $id = $Work->getId() + 1;
        
        $this->formData['Work'] = $id;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }
}
