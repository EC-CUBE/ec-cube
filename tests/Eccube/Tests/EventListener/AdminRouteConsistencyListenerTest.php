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
use Eccube\EventListener\AdminRouteConsistencyListener;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * ルーティングと設定値で管理画面のパスが食い違ったときの挙動を検証する.
 */
final class AdminRouteConsistencyListenerTest extends EccubeTestCase
{
    #[DoesNotPerformAssertions]
    public function testPassesWhenRoutingMatchesConfig(): void
    {
        $listener = $this->createListener('/admin/', 'admin');

        $listener->onKernelRequest($this->createEvent('/admin/product'));
    }

    public function testRejectsStaleAdminPathWhenMismatched(): void
    {
        $listener = $this->createListener('/admin/', 'newadmin');

        $this->expectException(ServiceUnavailableHttpException::class);

        $listener->onKernelRequest($this->createEvent('/admin/product'));
    }

    /**
     * ルーティングはデコードしたパスで照合するため, パーセントエンコードしたパスも止める.
     */
    public function testRejectsPercentEncodedStaleAdminPath(): void
    {
        $listener = $this->createListener('/admin/', 'newadmin');

        $this->expectException(ServiceUnavailableHttpException::class);

        $listener->onKernelRequest($this->createEvent('/%61dmin/product'));
    }

    /**
     * ベース URL 配下に設置した場合も, ベース URL を除いたパスで比較する.
     */
    public function testRejectsStaleAdminPathUnderBaseUrl(): void
    {
        $listener = $this->createListener('/shop/admin/', 'newadmin', '/shop');

        $this->expectException(ServiceUnavailableHttpException::class);

        $listener->onKernelRequest($this->createEvent('/admin/'));
    }

    /**
     * 食い違っていても, 古い管理画面のパス以外へのリクエストは止めない.
     */
    #[DataProvider(methodName: 'provideNonStalePaths')]
    #[DoesNotPerformAssertions]
    public function testPassesOtherPathsWhenMismatched(string $pathInfo): void
    {
        $listener = $this->createListener('/admin/', 'newadmin');

        $listener->onKernelRequest($this->createEvent($pathInfo));
    }

    /**
     * @return \Iterator<string, array{string}>
     */
    public static function provideNonStalePaths(): \Iterator
    {
        yield 'トップページ' => ['/'];
        yield '商品一覧' => ['/products/list'];
        yield '新しい管理画面のパス' => ['/newadmin/'];
        yield '接頭辞が同じ別のパス' => ['/administrator/'];
    }

    #[DoesNotPerformAssertions]
    public function testIgnoresSubRequest(): void
    {
        $listener = $this->createListener('/admin/', 'newadmin');

        $listener->onKernelRequest($this->createEvent('/admin/product', HttpKernelInterface::SUB_REQUEST));
    }

    public function testPassesWhenAdminHomepageRouteIsMissing(): void
    {
        $router = $this->createStub(RouterInterface::class);
        $router->method('generate')->willThrowException(new RouteNotFoundException());
        $listener = new AdminRouteConsistencyListener($router, $this->createConfig('newadmin'));

        $listener->onKernelRequest($this->createEvent('/admin/product'));

        $this->addToAssertionCount(1);
    }

    private function createListener(string $generated, string $adminRoute, string $baseUrl = ''): AdminRouteConsistencyListener
    {
        $router = $this->createStub(RouterInterface::class);
        $router->method('generate')->willReturn($generated);
        $router->method('getContext')->willReturn(new RequestContext($baseUrl));

        return new AdminRouteConsistencyListener($router, $this->createConfig($adminRoute));
    }

    private function createConfig(string $adminRoute): EccubeConfig
    {
        $config = $this->createStub(EccubeConfig::class);
        $config->method('get')->willReturnMap([['eccube_admin_route', $adminRoute]]);

        return $config;
    }

    private function createEvent(string $pathInfo, int $requestType = HttpKernelInterface::MAIN_REQUEST): RequestEvent
    {
        return new RequestEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create($pathInfo),
            $requestType,
        );
    }
}
