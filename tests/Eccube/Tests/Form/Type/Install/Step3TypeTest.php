<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
 * Foundation, Inc., 59 Temple Place - Suite $this->app['config']['id_min_len']-130, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Form\Type\Install;

class Step3TypeTest extends AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'shop_name' => '店舗名',
        'email' => 'eccube@example.com',
        'login_id' => 'administrator',
        'login_pass' => 'administrator',
        'admin_dir' => 'administrator',
        'admin_force_ssl' => true,
        'admin_allow_hosts' => '1.1.1.1',
        'mail_backend' => 'mail',
        'smtp_host' => '',
        'smtp_port' => '',
        'smtp_username' => '',
        'smtp_password' => '',
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('install_step3', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());

        $this->assertEquals('', $this->form->getErrorsAsString());
    }

    public function testInvalid_ShopName_Blank()
    {
        $this->formData['shop_name'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_Email_Blank()
    {
        $this->formData['email'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_LoginId_Blank()
    {
        $this->formData['login_id'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_LoginId_Min()
    {
        $this->formData['login_id'] = str_repeat('a', $this->app['config']['id_min_len']-1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_LoginId_Max()
    {
        $this->formData['login_id'] = str_repeat('a', $this->app['config']['id_max_len']+1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValid_LoginId_Min()
    {
        $this->formData['login_id'] = str_repeat('a', $this->app['config']['id_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValid_LoginId_Max()
    {
        $this->formData['login_id'] = str_repeat('a', $this->app['config']['id_max_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalid_LoginId_Hiragana()
    {
        $this->formData['login_id'] = str_repeat('あ', $this->app['config']['id_max_len']);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_LoginPass_Blank()
    {
        $this->formData['login_pass'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_LoginPass_Min()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->app['config']['password_min_len']-1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_LoginPass_Max()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->app['config']['password_max_len']+1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testVallid_LoginPass_Min()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->app['config']['password_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testVallid_LoginPass_Max()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->app['config']['password_max_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalid_LoginPass_Hiragana()
    {
        $this->formData['login_pass'] = str_repeat('あ', $this->app['config']['password_max_len']);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_AdminDir_Blank()
    {
        $this->formData['login_pass'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_AdminDir_Min()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->app['config']['id_min_len']-1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_AdminDir_Max()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->app['config']['id_max_len']+1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testVallid_AdminDir_Min()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->app['config']['password_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testVallid_AdminDir_Max()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->app['config']['password_max_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalid_AdminDir_Hiragana()
    {
        $this->formData['admin_dir'] = str_repeat('あ', $this->app['config']['id_max_len']);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValid_ForceSsl_Blank()
    {
        $this->formData['admin_force_ssl'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValid_AllowHosts_Blank()
    {
        $this->formData['admin_allow_hosts'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminAllowHost_OneLineIp()
    {
        $this->formData['admin_allow_hosts'] = "127.0.0.1";
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminAllowHost_MultiLineIps()
    {
        $this->formData['admin_allow_hosts'] = "127.0.0.1\n1.1.1.1";
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidAdminAllowHost_NotIp()
    {
        $this->formData['admin_allow_hosts'] = "255.255.255,256";
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValid_MailBackend_Blank()
    {
        $this->formData['mail_backend'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValid_AdminDir()
    {
        $this->formData['admin_dir'] = 'admin';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
