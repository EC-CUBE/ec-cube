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

namespace Eccube\Tests\Form\Type\Install;

use Eccube\Form\Type\Install\Step3Type;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class Step3TypeTest extends AbstractTypeTestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * @var array デフォルト値（正常系）を設定
     */
    protected $formData = [
        'shop_name' => '店舗名',
        'email' => 'eccube@example.com',
        'login_id' => 'administrator',
        'login_pass' => 'administrator',
        'admin_dir' => 'administrator',
        'admin_force_ssl' => true,
        'admin_allow_hosts' => '1.1.1.1',
        'smtp_host' => '',
        'smtp_port' => '',
        'smtp_username' => '',
        'smtp_password' => '',
    ];

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(Step3Type::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->form->isValid();
        $this->assertEquals('', (string) $this->form->getErrors(true, false));
    }

    public function testInvalidShopNameBlank()
    {
        $this->formData['shop_name'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmailBlank()
    {
        $this->formData['email'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLoginIdBlank()
    {
        $this->formData['login_id'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLoginIdMin()
    {
        $this->formData['login_id'] = str_repeat('a', $this->eccubeConfig['eccube_id_min_len'] - 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLoginIdMax()
    {
        $this->formData['login_id'] = str_repeat('a', $this->eccubeConfig['eccube_id_max_len'] + 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidLoginIdMin()
    {
        $this->formData['login_id'] = str_repeat('a', $this->eccubeConfig['eccube_id_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidLoginIdMax()
    {
        $this->formData['login_id'] = str_repeat('a', $this->eccubeConfig['eccube_id_max_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLoginIdHiragana()
    {
        $this->formData['login_id'] = str_repeat('あ', $this->eccubeConfig['eccube_id_max_len']);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLoginPassBlank()
    {
        $this->formData['login_pass'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLoginPassMin()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len'] - 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLoginPassMax()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len'] + 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testVallidLoginPassMin()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testVallidLoginPassMax()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidLoginPassHiragana()
    {
        $this->formData['login_pass'] = str_repeat('あ', $this->eccubeConfig['eccube_password_max_len']);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAdminDirBlank()
    {
        $this->formData['login_pass'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAdminDirMin()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_id_min_len'] - 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidAdminDirMax()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_id_max_len'] + 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testVallidAdminDirMin()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testVallidAdminDirMax()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidAdminDirHiragana()
    {
        $this->formData['admin_dir'] = str_repeat('あ', $this->eccubeConfig['eccube_id_max_len']);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidForceSslBlank()
    {
        $this->formData['admin_force_ssl'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAllowHostsBlank()
    {
        $this->formData['admin_allow_hosts'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminAllowHostOneLineIp()
    {
        $this->formData['admin_allow_hosts'] = '127.0.0.1';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminAllowHostMultiLineIps()
    {
        $this->formData['admin_allow_hosts'] = "127.0.0.1\n1.1.1.1";
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidAdminAllowHostNotIp()
    {
        $this->formData['admin_allow_hosts'] = '255.255.255,256';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidAdminDir()
    {
        $this->formData['admin_dir'] = 'admin';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
