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

use Eccube\Form\Type\Admin\SecurityType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class SecurityTypeTest extends AbstractTypeTestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * @var array デフォルト値（正常系）を設定
     */
    protected $formData = [
        'admin_route_dir' => 'admin',
        'admin_allow_hosts' => '',
        'admin_deny_hosts' => '',
        'trusted_hosts' => 'localhost',
    ];

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(SecurityType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    public function testValidData()
    {
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

    public function testValidAdminAllowHostNotIp()
    {
        $this->formData['admin_allow_hosts'] = '255.255.255,256';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidAdminDenyHostOneLineIp()
    {
        $this->formData['admin_deny_hosts'] = '127.0.0.1';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminDenyHostMultiLineIps()
    {
        $this->formData['admin_deny_hosts'] = "127.0.0.1\n1.1.1.1";
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminDenyHostNotIp()
    {
        $this->formData['admin_deny_hosts'] = '255.255.255,256';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * Over ltext_len = 3000
     */
    public function testValidAdminAllowHostMaxLength()
    {
        $this->formData['admin_allow_host'] = str_repeat("127.0.0.1\n", 1000);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * @dataProvider adminRouteDirParams
     */
    public function testAdminRouteDir($rootDir, $valid)
    {
        $this->formData['admin_route_dir'] = $rootDir;
        $this->form->submit($this->formData);
        $this->assertEquals($valid, $this->form->isValid());
    }

    public function adminRouteDirParams()
    {
        return [
            ['admin', true],
            ['ADMIN', true],
            ['12345', true],
            ['adminADMIN123', true],
            ['admin_admin', true],
            ['/admin', false],
            ['admin/', false],
            ['admin/route', false],
            ['admin&', false],
            ['admin?', false],
        ];
    }

    public function testTrustedHosts()
    {
        $this->formData['trusted_hosts'] = '^127\.0\.0.1$,^localhost$';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }
}
