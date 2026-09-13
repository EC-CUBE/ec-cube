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

namespace Eccube\Tests\EventListener;

use Eccube\Common\EccubeConfig;
use Eccube\EventListener\RestrictFileUploadListener;
use Eccube\Request\Context;
use Eccube\Tests\Web\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class RestrictFileUploadListenerTest extends AbstractWebTestCase
{
    /**
     * レーン S (Web サーバーではなく CLI の実行ユーザーが所有するディレクトリ) へ
     * 書き込む管理画面のルート.
     *
     * 書き込む画面を追加したときにここへ足し忘れると, 読み取り専用モードで素通りする.
     * 対象を増やす際は eccube_restrict_file_upload_urls と本定数の双方を更新する.
     *
     * @var string[]
     */
    private const LANE_S_WRITE_ROUTES = [
        // html/user_data
        'admin_content_file',
        'admin_content_file_delete',
        'admin_content_css',
        'admin_content_js',
        // dtb_page / dtb_block + app/template
        'admin_content_page_new',
        'admin_content_page_edit',
        'admin_content_page_delete',
        'admin_content_block_new',
        'admin_content_block_edit',
        'admin_content_block_delete',
        // dtb_mail_template + app/template
        'admin_setting_shop_mail',
        'admin_setting_shop_mail_edit',
        'admin_setting_shop_mail_delete',
        // .env
        'admin_setting_system_security',
        'admin_store_template',
        // app/template, html/template
        'admin_store_template_install',
        'admin_store_template_delete',
        // app/Plugin, app/proxy, vendor, composer.json
        'admin_store_plugin_install',
        'admin_store_plugin_enable',
        'admin_store_plugin_disable',
        'admin_store_plugin_uninstall',
        'admin_store_plugin_update',
        'admin_store_plugin_api_install',
        'admin_store_plugin_api_uninstall',
        'admin_store_plugin_api_update',
        'admin_store_plugin_api_upgrade',
        'admin_store_plugin_api_schema_update',
    ];

    public function testLaneSWriteRoutesAreAllRestricted(): void
    {
        $configured = array_keys($this->restrictUrls());

        $this->assertSame([], array_values(array_diff(self::LANE_S_WRITE_ROUTES, $configured)), 'レーン S へ書き込むルートが eccube_restrict_file_upload_urls から漏れている');
    }

    public function testSafeMethodIsAllowedAndMarkedAsReadOnly(): void
    {
        $request = $this->handle('admin_content_page_edit', 'GET', '1');

        $this->assertTrue($request->attributes->getBoolean(RestrictFileUploadListener::RESTRICTABLE_ATTRIBUTE));
        $this->assertTrue($request->attributes->getBoolean(RestrictFileUploadListener::READ_ONLY_ATTRIBUTE));
        $this->assertSame($this->restrictUrls()['admin_content_page_edit'], $request->attributes->get(RestrictFileUploadListener::READ_ONLY_COMMAND_ATTRIBUTE));
    }

    public function testCommandIsNullWhenCliIsNotAvailable(): void
    {
        $request = $this->handle('admin_store_template_install', 'GET', '1');

        $this->assertTrue($request->attributes->getBoolean(RestrictFileUploadListener::READ_ONLY_ATTRIBUTE));
        $this->assertNull($request->attributes->get(RestrictFileUploadListener::READ_ONLY_COMMAND_ATTRIBUTE));
    }

    public static function writeRequests(): \Iterator
    {
        yield ['admin_content_page_edit', 'POST'];
        yield ['admin_content_page_delete', 'DELETE'];
        yield ['admin_content_block_delete', 'DELETE'];
        yield ['admin_content_file_delete', 'DELETE'];
        yield ['admin_content_css', 'POST'];
        yield ['admin_setting_shop_mail', 'POST'];
        yield ['admin_setting_shop_mail_delete', 'DELETE'];
        yield ['admin_setting_system_security', 'POST'];
        yield ['admin_store_template', 'POST'];
        yield ['admin_store_template_delete', 'DELETE'];
        yield ['admin_store_plugin_update', 'POST'];
        yield ['admin_store_plugin_enable', 'POST'];
        yield ['admin_store_plugin_disable', 'POST'];
        yield ['admin_store_plugin_uninstall', 'DELETE'];
        yield ['admin_store_plugin_api_install', 'POST'];
        yield ['admin_store_plugin_api_uninstall', 'DELETE'];
        yield ['admin_store_plugin_api_update', 'POST'];
        yield ['admin_store_plugin_api_upgrade', 'POST'];
        yield ['admin_store_plugin_api_schema_update', 'POST'];
    }

    #[DataProvider(methodName: 'writeRequests')]
    public function testWriteRequestIsDenied(string $route, string $method): void
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->handle($route, $method, '1');
    }

    /**
     * ファイル管理はディレクトリの移動も POST のため, ここでは拒否せず
     * FileController が mode を見て拒否する.
     */
    public function testFileManagerIsLeftToTheController(): void
    {
        $request = $this->handle('admin_content_file', 'POST', '1');

        $this->assertTrue($request->attributes->getBoolean(RestrictFileUploadListener::READ_ONLY_ATTRIBUTE));
    }

    public function testRestrictableIsMarkedWhileDisabled(): void
    {
        $request = $this->handle('admin_content_page_edit', 'POST', '0');

        $this->assertTrue($request->attributes->getBoolean(RestrictFileUploadListener::RESTRICTABLE_ATTRIBUTE));
        $this->assertFalse($request->attributes->getBoolean(RestrictFileUploadListener::READ_ONLY_ATTRIBUTE));
    }

    public function testUnlistedRouteIsUntouched(): void
    {
        $request = $this->handle('admin_product_product_new', 'POST', '1');

        $this->assertFalse($request->attributes->getBoolean(RestrictFileUploadListener::RESTRICTABLE_ATTRIBUTE));
        $this->assertFalse($request->attributes->getBoolean(RestrictFileUploadListener::READ_ONLY_ATTRIBUTE));
    }

    public function testFrontRequestIsUntouched(): void
    {
        $request = $this->handle('admin_content_page_edit', 'POST', '1', false);

        $this->assertFalse($request->attributes->getBoolean(RestrictFileUploadListener::RESTRICTABLE_ATTRIBUTE));
    }

    /**
     * ルーティングに一致しないリクエストでは _route が無い.
     */
    public function testRequestWithoutRouteIsUntouched(): void
    {
        $request = $this->handle(null, 'POST', '1');

        $this->assertFalse($request->attributes->getBoolean(RestrictFileUploadListener::RESTRICTABLE_ATTRIBUTE));
    }

    /**
     * @return array<string, string|null>
     */
    private function restrictUrls(): array
    {
        return $this->eccubeConfig['eccube_restrict_file_upload_urls'];
    }

    private function handle(?string $route, string $method, string $restrict, bool $isAdmin = true): Request
    {
        $request = Request::create('/', $method);
        if (null !== $route) {
            $request->attributes->set('_route', $route);
        }

        $event = $this->createStub(RequestEvent::class);
        $event->method('isMainRequest')->willReturn(true);
        $event->method('getRequest')->willReturn($request);

        $context = $this->createStub(Context::class);
        $context->method('isAdmin')->willReturn($isAdmin);

        // 対象ルートの一覧は実際の設定を使い, 制限の有効・無効だけを差し替える
        $eccubeConfig = $this->createStub(EccubeConfig::class);
        $eccubeConfig->method('offsetGet')->willReturnMap([
            ['eccube_restrict_file_upload', $restrict],
            ['eccube_restrict_file_upload_urls', $this->restrictUrls()],
        ]);

        (new RestrictFileUploadListener($eccubeConfig, $context))->onKernelRequest($event);

        return $request;
    }
}
