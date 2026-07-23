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

namespace Eccube\Tests\Web\Admin\Setting\System;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;

#[Group('cache-clear')]
final class SecurityControllerTest extends AbstractAdminWebTestCase
{
    protected $envFile;

    protected $env;

    protected function setUp(): void
    {
        parent::setUp();
        $this->envFile = static::getContainer()->getParameter('kernel.project_dir').'/.env';
        if (file_exists($this->envFile)) {
            $this->env = file_get_contents($this->envFile);
        }
    }

    protected function tearDown(): void
    {
        if ($this->env) {
            file_put_contents($this->envFile, $this->env);
        }
        parent::tearDown();
    }

    /**
     * Routing test
     */
    public function testRouting()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_system_security'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Submit test
     */
    #[Group(name: 'cache-clear')]
    public function testSubmit()
    {
        $session = $this->createSession($this->client);
        $formData = $this->createFormData();

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_system_security'),
            [
                'admin_security' => $formData,
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        // Message
        $outPut = $session->getFlashBag()->get('eccube.admin.success');
        $this->actual = array_shift($outPut);
        $this->expected = 'admin.setting.system.security.admin_url_changed';
        $this->verify();

        $this->assertMatchesRegularExpression('/ECCUBE_ADMIN_ROUTE='.$formData['admin_route_dir'].'/', file_get_contents($this->envFile));
    }

    /**
     * 環境変数が OS のプロセス環境変数として設定されている場合、画面表示時に
     * 警告を表示し、登録ボタンを無効化することを確認する（#6130）。
     */
    #[Group(name: 'cache-clear')]
    public function testDisplayWarningAndDisableButtonWhenEnvOverridden(): void
    {
        $key = 'ECCUBE_ADMIN_ROUTE';
        $original = getenv($key);
        putenv($key.'=admin');
        try {
            $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_system_security'));
            $this->assertTrue($this->client->getResponse()->isSuccessful());

            // 登録ボタンが無効化されている
            $this->assertGreaterThan(0, $crawler->filter('button[type="submit"][disabled]')->count());

            // 反映されない旨の警告が表示されている
            $this->assertStringContainsString(
                trans('admin.system.env.ineffective.overridden'),
                (string) $this->client->getResponse()->getContent()
            );
        } finally {
            false === $original ? putenv($key) : putenv($key.'='.$original);
        }
    }

    /**
     * 環境変数が OS のプロセス環境変数として設定されている場合、.env への書き込みは
     * 反映されないため保存を拒否しエラーを表示することを確認する（#6130）。
     */
    #[Group(name: 'cache-clear')]
    public function testSubmitRejectedWhenEnvOverridden(): void
    {
        $session = $this->createSession($this->client);
        $formData = $this->createFormData();

        // この画面が書き込む環境変数の1つをプロセス環境変数として設定
        $key = 'ECCUBE_ADMIN_ROUTE';
        $original = getenv($key);
        putenv($key.'=admin');
        try {
            $this->client->request(
                Request::METHOD_POST,
                $this->generateUrl('admin_setting_system_security'),
                [
                    'admin_security' => $formData,
                ]
            );

            $this->assertTrue($this->client->getResponse()->isRedirection());

            // 保存が拒否されエラーが表示される
            $errors = $session->getFlashBag()->get('eccube.admin.error');
            $this->assertContains('admin.common.save_error', $errors);

            // .env は書き換えられていない
            $this->assertDoesNotMatchRegularExpression('/ECCUBE_ADMIN_ROUTE='.$formData['admin_route_dir'].'/', file_get_contents($this->envFile));
        } finally {
            // 実行環境が事前に設定していた値を復元する
            false === $original ? putenv($key) : putenv($key.'='.$original);
        }
    }

    /**
     * Submit when empty
     */
    public function testSubmitEmpty()
    {
        $formData = $this->createFormData();
        $formData['admin_route_dir'] = null;
        $formData['admin_allow_hosts'] = null;
        $formData['admin_deny_hosts'] = null;
        $formData['front_allow_hosts'] = null;
        $formData['front_deny_hosts'] = null;
        $formData['force_ssl'] = null;

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_system_security'),
            [
                'admin_security' => $formData,
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $newEnv = file_exists($this->envFile) ? file_get_contents($this->envFile) : null;
        $this->assertSame($this->env, $newEnv);
    }

    /**
     * Submit form
     */
    public function createFormData(): array
    {
        return [
            '_token' => 'dummy',
            'admin_route_dir' => 'admintest',
            'admin_allow_hosts' => '127.0.0.1/32',
            'admin_deny_hosts' => '127.0.0.1/32',
            'front_allow_hosts' => '127.0.0.1/32',
            'front_deny_hosts' => '127.0.0.1/32',
            'trusted_hosts' => '^127\.0\.0\.1$,^localhost$',
        ];
    }
}
