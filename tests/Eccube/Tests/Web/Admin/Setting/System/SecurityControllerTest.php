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

use Eccube\Tests\EnvOverrideTrait;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;

#[Group('cache-clear')]
final class SecurityControllerTest extends AbstractAdminWebTestCase
{
    use EnvOverrideTrait;

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
     * 反映可能な環境（.env が書き込み可能）では登録ボタンが無効化されないことを確認する（対照）。
     */
    public function testDisplayButtonEnabledWhenEffective(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_system_security'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('button[type="submit"][disabled]'));
    }

    /**
     * 対象キーの 1 つ（ECCUBE_ADMIN_ROUTE）が OS 環境変数で上書きされていても、
     * 画面全体はブロックせず（登録ボタンは有効のまま）、該当キーを名指しで警告することを確認する（#6130 / #2）。
     */
    public function testDisplayWarnsNamedKeyButKeepsButtonEnabledWhenSingleKeyOverridden(): void
    {
        $this->forceKeyOverridden('ECCUBE_ADMIN_ROUTE', $this->currentAdminRoute(), function (): void {
            $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_system_security'));
            $this->assertTrue($this->client->getResponse()->isSuccessful());

            // 残りのキーは .env に書けるため登録ボタンは無効化されない
            $this->assertCount(0, $crawler->filter('button[type="submit"][disabled]'));

            // 上書きされているキーを名指しした警告が表示される
            $this->assertStringContainsString(
                trans('admin.system.env.ineffective.overridden', ['%keys%' => 'ECCUBE_ADMIN_ROUTE']),
                (string) $this->client->getResponse()->getContent()
            );
        });
    }

    /**
     * 対象キーの 1 つ（ECCUBE_ADMIN_ROUTE）が上書きされている場合、そのキーは .env に書かず、
     * 反映される他キー（TRUSTED_HOSTS 等）は保存されることを確認する（#6130 / #2 の退行解消）。
     */
    public function testSubmitSkipsOverriddenKeyButSavesOthers(): void
    {
        $session = $this->createSession($this->client);
        $formData = $this->createFormData();

        $this->forceKeyOverridden('ECCUBE_ADMIN_ROUTE', $this->currentAdminRoute(), function () use ($session, $formData): void {
            $this->client->request(
                Request::METHOD_POST,
                $this->generateUrl('admin_setting_system_security'),
                ['admin_security' => $formData]
            );

            $this->assertTrue($this->client->getResponse()->isRedirection());

            // 保存自体は拒否されない（save_error は出ない）
            $errors = $session->getFlashBag()->get('eccube.admin.error');
            $this->assertNotContains('admin.common.save_error', $errors);

            $content = file_get_contents($this->envFile);
            // 上書きされている ECCUBE_ADMIN_ROUTE は書き換わらない
            $this->assertDoesNotMatchRegularExpression('/ECCUBE_ADMIN_ROUTE='.$formData['admin_route_dir'].'/', $content);
            // 反映される TRUSTED_HOSTS は保存される
            $this->assertStringContainsString('TRUSTED_HOSTS='.$formData['trusted_hosts'], (string) $content);
        });
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
     * 現在アプリケーションが使用している管理画面のルーティングプレフィックス.
     *
     * ECCUBE_ADMIN_ROUTE の上書きを再現する際、実効値と異なる値を注入すると
     * 管理画面の URL 自体が変わってしまうため、実効値をそのまま注入する
     * （{@see EnvOverrideTrait::forceKeyOverridden()}）.
     */
    private function currentAdminRoute(): string
    {
        return (string) static::getContainer()->getParameter('eccube_admin_route');
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
