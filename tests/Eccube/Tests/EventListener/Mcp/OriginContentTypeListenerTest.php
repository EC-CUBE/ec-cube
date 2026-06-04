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

namespace Eccube\Tests\EventListener\Mcp;

use Eccube\EventListener\Mcp\OriginContentTypeListener;
use Eccube\Service\Mcp\McpAuditLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * `OriginContentTypeListener` のユニットテスト。 DB 不要。
 *
 * `^/admin/mcp` 配下のリクエストに対する Content-Type / Origin の前段ガード挙動を検証する。
 */
final class OriginContentTypeListenerTest extends TestCase
{
    public function testIgnoresNonMcpPath(): void
    {
        $event = $this->dispatch(
            $this->makeListener(),
            $this->makeRequest('POST', '/admin/product', contentType: 'text/html'),
        );

        $this->assertNotInstanceOf(Response::class, $event->getResponse(), '対象外パスは何もしない');
    }

    public function testIgnoresGetHeadOptions(): void
    {
        $listener = $this->makeListener();

        foreach (['GET', 'HEAD', 'OPTIONS'] as $method) {
            $event = $this->dispatch($listener, $this->makeRequest($method, '/admin/mcp', contentType: 'text/html'));
            $this->assertNotInstanceOf(Response::class, $event->getResponse(), sprintf('%s は Content-Type 検証対象外', $method));
        }
    }

    public function testPassesPostWithJsonContentType(): void
    {
        $event = $this->dispatch(
            $this->makeListener(),
            $this->makeRequest('POST', '/admin/mcp', contentType: 'application/json'),
        );

        $this->assertNotInstanceOf(Response::class, $event->getResponse(), '正常な JSON POST は通過');
    }

    public function testRejectsPostWithNonJsonContentType(): void
    {
        $event = $this->dispatch(
            $this->makeListener(),
            $this->makeRequest('POST', '/admin/mcp', contentType: 'text/html'),
        );

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $response->getStatusCode(), (string) $response->getContent());
    }

    public function testRejectsPostWithoutContentTypeHeader(): void
    {
        $event = $this->dispatch(
            $this->makeListener(),
            $this->makeRequest('POST', '/admin/mcp', contentType: null),
        );

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $response->getStatusCode(), (string) $response->getContent());
    }

    public function testRejectsDisallowedOrigin(): void
    {
        $event = $this->dispatch(
            $this->makeListener(allowedOriginsCsv: 'https://example.com'),
            $this->makeRequest('POST', '/admin/mcp', contentType: 'application/json', origin: 'https://evil.example'),
        );

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), (string) $response->getContent());
    }

    public function testPassesAllowedOrigin(): void
    {
        $event = $this->dispatch(
            $this->makeListener(allowedOriginsCsv: 'https://example.com,http://localhost:6274'),
            $this->makeRequest('POST', '/admin/mcp', contentType: 'application/json', origin: 'http://localhost:6274'),
        );

        $this->assertNotInstanceOf(Response::class, $event->getResponse(), '許可リストにある Origin は通過');
    }

    public function testSkipsOriginCheckWhenAllowListIsEmpty(): void
    {
        $event = $this->dispatch(
            $this->makeListener(allowedOriginsCsv: ''),
            $this->makeRequest('POST', '/admin/mcp', contentType: 'application/json', origin: 'http://anything'),
        );

        $this->assertNotInstanceOf(Response::class, $event->getResponse(), '許可リスト未設定なら Origin 検証 skip');
    }

    public function testSkipsOriginCheckWhenOriginHeaderAbsent(): void
    {
        $event = $this->dispatch(
            $this->makeListener(allowedOriginsCsv: 'https://example.com'),
            $this->makeRequest('POST', '/admin/mcp', contentType: 'application/json'),
        );

        $this->assertNotInstanceOf(Response::class, $event->getResponse(), 'Origin 無し (curl 等) は通過');
    }

    private function makeListener(string $allowedOriginsCsv = ''): OriginContentTypeListener
    {
        $auditLogger = new McpAuditLogger(new NullLogger(), new RequestStack());

        return new OriginContentTypeListener('admin', $allowedOriginsCsv, $auditLogger);
    }

    private function makeRequest(string $method, string $path, ?string $contentType, ?string $origin = null): Request
    {
        $server = ['REQUEST_METHOD' => $method, 'REQUEST_URI' => $path];
        if (null !== $contentType) {
            $server['CONTENT_TYPE'] = $contentType;
        }
        $request = Request::create($path, $method, server: $server);
        if (null !== $contentType) {
            $request->headers->set('Content-Type', $contentType);
        }
        if (null !== $origin) {
            $request->headers->set('Origin', $origin);
        }

        return $request;
    }

    private function dispatch(OriginContentTypeListener $listener, Request $request): RequestEvent
    {
        $event = new RequestEvent($this->createStub(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);
        $listener->onKernelRequest($event);

        return $event;
    }
}
