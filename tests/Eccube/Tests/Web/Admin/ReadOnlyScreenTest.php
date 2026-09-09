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

namespace Eccube\Tests\Web\Admin;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ECCUBE_RESTRICT_FILE_UPLOAD=1 のときの管理画面の挙動.
 *
 * 制限の有無はコンパイル済みコンテナへ焼き込まれるため, このクラスだけ
 * 環境変数を設定してからカーネルを起動し直す.
 */
final class ReadOnlyScreenTest extends AbstractAdminWebTestCase
{
    private const ENV_KEY = 'ECCUBE_RESTRICT_FILE_UPLOAD';

    protected function setUp(): void
    {
        $_ENV[self::ENV_KEY] = '1';
        $_SERVER[self::ENV_KEY] = '1';
        self::ensureKernelShutdown();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($_ENV[self::ENV_KEY], $_SERVER[self::ENV_KEY]);
        // 制限を有効にしたカーネルを他のテストへ持ち越さない
        self::ensureKernelShutdown();
    }

    public function testScreenIsRenderedWithGuidanceAndDisabledButton(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_content_css'));

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
        $this->assertStringContainsString('bin/console eccube:asset:apply --type=css --body=-', (string) $this->client->getResponse()->getContent(), '代替となる CLI コマンドが案内されていない');
        $this->assertCount(1, $crawler->filter('#save-button[disabled]'), '保存ボタンが無効化されていない');
    }

    /**
     * 制限中でもメニューから辿れること. 従来は項目ごと非表示にしていた.
     */
    public function testNavigationKeepsRestrictedScreens(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_homepage'));

        $hrefs = $crawler->filter('.c-mainNavArea a')->each(static fn ($node): ?string => $node->attr('href'));
        foreach (['admin_content_css', 'admin_content_js', 'admin_content_file'] as $route) {
            $path = $this->generateUrl($route);
            $this->assertNotEmpty(array_filter($hrefs, static fn (?string $href): bool => null !== $href && str_ends_with($href, $path)), sprintf('メニューから %s へ辿れない', $route));
        }
    }

    public static function writeRequests(): \Iterator
    {
        yield ['POST', 'admin_content_css', []];
        yield ['POST', 'admin_content_js', []];
        yield ['POST', 'admin_content_page_new', []];
        yield ['DELETE', 'admin_content_page_delete', ['id' => 1]];
        yield ['DELETE', 'admin_content_block_delete', ['id' => 1]];
        yield ['DELETE', 'admin_content_file_delete', []];
        yield ['POST', 'admin_setting_shop_mail', []];
        yield ['DELETE', 'admin_setting_shop_mail_delete', ['id' => 1]];
        yield ['POST', 'admin_setting_system_security', []];
        yield ['POST', 'admin_store_template', []];
        yield ['DELETE', 'admin_store_template_delete', ['id' => 1]];
        yield ['POST', 'admin_store_plugin_update', ['id' => 1]];
        yield ['POST', 'admin_store_plugin_enable', ['id' => 1]];
        yield ['POST', 'admin_store_plugin_disable', ['id' => 1]];
        yield ['DELETE', 'admin_store_plugin_uninstall', ['id' => 1]];
        yield ['POST', 'admin_store_plugin_api_install', []];
        yield ['DELETE', 'admin_store_plugin_api_uninstall', ['id' => 1]];
        yield ['POST', 'admin_store_plugin_api_update', []];
        yield ['POST', 'admin_store_plugin_api_upgrade', []];
        yield ['POST', 'admin_store_plugin_api_schema_update', []];
    }

    /**
     * @param array<string, mixed> $parameters
     */
    #[DataProvider(methodName: 'writeRequests')]
    public function testWriteRequestIsForbidden(string $method, string $route, array $parameters): void
    {
        $this->client->request($method, $this->generateUrl($route, $parameters));

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    /**
     * ファイル管理はディレクトリの移動も POST のため, 書き込む mode だけを拒否する.
     */
    public function testFileManagerAllowsNavigationAndDeniesWrite(): void
    {
        $url = $this->generateUrl('admin_content_file');

        $this->client->request(Request::METHOD_POST, $url, ['mode' => 'move']);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());

        $this->client->request(Request::METHOD_POST, $url, ['mode' => 'create']);
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());

        $this->client->request(Request::METHOD_POST, $url, ['mode' => 'upload']);
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }
}
