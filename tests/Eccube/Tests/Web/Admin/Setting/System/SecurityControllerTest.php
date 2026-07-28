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
        $this->forceKeyOverridden('ECCUBE_ADMIN_ROUTE', function (): void {
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

        $this->forceKeyOverridden('ECCUBE_ADMIN_ROUTE', function () use ($session, $formData): void {
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
     * 指定キーが「.env 由来でなく OS のプロセス環境変数で上書きされている」状態を
     * 決定的に作り出してコールバックを実行し、終了後に $_SERVER を元へ復元する.
     *
     * テスト環境では bootEnv により .env のキーが SYMFONY_DOTENV_VARS に載るため
     * putenv では上書き状態を再現できない（EnvFileService は SYMFONY_DOTENV_VARS を優先する）。
     * そこで当該キーを SYMFONY_DOTENV_VARS から除外し、$_SERVER にプロセス環境変数値を注入する.
     */
    private function forceKeyOverridden(string $key, callable $fn): void
    {
        $sentinel = '__ECCUBE_TEST_UNSET__';
        $origVars = \array_key_exists('SYMFONY_DOTENV_VARS', $_SERVER) ? $_SERVER['SYMFONY_DOTENV_VARS'] : $sentinel;
        $origVal = \array_key_exists($key, $_SERVER) ? $_SERVER[$key] : $sentinel;

        $vars = array_filter(
            explode(',', (string) ($sentinel === $origVars ? '' : $origVars)),
            fn ($k) => $k !== $key && '' !== $k
        );
        $_SERVER['SYMFONY_DOTENV_VARS'] = implode(',', $vars);
        $_SERVER[$key] = 'osvalue';

        try {
            $fn();
        } finally {
            if ($sentinel === $origVars) {
                unset($_SERVER['SYMFONY_DOTENV_VARS']);
            } else {
                $_SERVER['SYMFONY_DOTENV_VARS'] = $origVars;
            }
            if ($sentinel === $origVal) {
                unset($_SERVER[$key]);
            } else {
                $_SERVER[$key] = $origVal;
            }
        }
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
