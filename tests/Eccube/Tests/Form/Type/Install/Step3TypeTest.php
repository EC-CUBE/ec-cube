<?php

declare(strict_types=1);

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
use Symfony\Component\Form\FormInterface;

final class Step3TypeTest extends AbstractTypeTestCase
{
    protected ?FormInterface $form = null;

    /**
     * @var array デフォルト値（正常系）を設定
     */
    protected ?array $formData = [
        'shop_name' => '店舗名',
        'email' => 'eccube@example.com',
        'login_id' => 'administrator',
        'login_pass' => 'administrator1234',
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
    protected function setUp(): void
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
        $this->assertSame('', (string) $this->form->getErrors(true, false));
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
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_min_len'] - 1).'1';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testVallidLoginPassMax()
    {
        $this->formData['login_pass'] = str_repeat('a', $this->eccubeConfig['eccube_password_max_len'] - 1).'1';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * NIST SP 800-63B-4 では文字種の複雑さを求めないため, ひらがなのみでも有効.
     */
    public function testValidLoginPassHiragana()
    {
        $this->formData['login_pass'] = str_repeat('あ', $this->eccubeConfig['eccube_password_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * 英字のみ(数字なし)でも有効.
     */
    public function testValidAlphabetOnly()
    {
        $password = str_repeat('a', $this->eccubeConfig['eccube_password_min_len']);

        $this->formData['login_pass'] = $password;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    /**
     * 数字のみでも有効.
     */
    public function testValidNumericOnly()
    {
        $password = '987654321098765';

        $this->formData['login_pass'] = $password;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    /**
     * ブロックリストに掲載されたパスワードは不可.
     */
    public function testInvalidBlocklisted()
    {
        $this->formData['login_pass'] = 'passwordpassword';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    /**
     * 制御文字(改行)を含む場合は不可.
     */
    public function testInvalidControlCharacter()
    {
        $this->formData['login_pass'] = "abcdefghijklmno\nabc";
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
        $this->formData['admin_dir'] = str_repeat('a', $this->eccubeConfig['eccube_id_max_len'] + 1);

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testVallidAdminDirMin()
    {
        $this->formData['admin_dir'] = str_repeat('a', $this->eccubeConfig['eccube_id_min_len']);

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminDirMax()
    {
        $this->formData['admin_dir'] = str_repeat('a', $this->eccubeConfig['eccube_id_max_len']);

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
