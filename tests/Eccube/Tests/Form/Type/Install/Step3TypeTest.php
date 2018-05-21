<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
        $this->formData['login_id'] = str_repeat('a', $this->eccubeConfig['eccube_id_min_len'] - 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_LoginId_Max()
    {
        $this->formData['login_id'] = str_repeat('a', $this->eccubeConfig['eccube_id_max_len'] + 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValid_LoginId_Min()
    {
        $this->formData['login_id'] = str_repeat('a', $this->eccubeConfig['eccube_id_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValid_LoginId_Max()
    {
        $this->formData['login_id'] = str_repeat('a', $this->eccubeConfig['eccube_id_max_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalid_LoginId_Hiragana()
    {
        $this->formData['login_id'] = str_repeat('あ', $this->eccubeConfig['eccube_id_max_len']);

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
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len'] - 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_LoginPass_Max()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len'] + 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testVallid_LoginPass_Min()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testVallid_LoginPass_Max()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalid_LoginPass_Hiragana()
    {
        $this->formData['login_pass'] = str_repeat('あ', $this->eccubeConfig['eccube_password_max_len']);

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
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_id_min_len'] - 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_AdminDir_Max()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_id_max_len'] + 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testVallid_AdminDir_Min()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testVallid_AdminDir_Max()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalid_AdminDir_Hiragana()
    {
        $this->formData['admin_dir'] = str_repeat('あ', $this->eccubeConfig['eccube_id_max_len']);

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
        $this->formData['admin_allow_hosts'] = '127.0.0.1';
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
        $this->formData['admin_allow_hosts'] = '255.255.255,256';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
