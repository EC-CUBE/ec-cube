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

class SecurityTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'admin_route_dir' => 'admin',
        'admin_allow_host' => '',
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('admin_security', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminAllowHost_OneLineIp()
    {
        $this->formData['admin_allow_host'] = "127.0.0.1";
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminAllowHost_MultiLineIps()
    {
        $this->formData['admin_allow_host'] = "127.0.0.1\n1.1.1.1";
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminAllowHost_NotIp()
    {
        $this->formData['admin_allow_host'] = "255.255.255,256";
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * Over ltext_len = 3000
     */
    public function testValidAdminAllowHost_MaxLength()
    {
        $this->formData['admin_allow_host'] = str_repeat("127.0.0.1\n", 1000);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
