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

use Eccube\EventListener\Mcp\RateLimitListener;
use Eccube\Service\Mcp\McpAuditLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\RateLimiter\LimiterStateInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Component\RateLimiter\Storage\StorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * `RateLimitListener` のユニットテスト (DB 不要、 in-memory storage)。
 *
 * 実 `RateLimiterFactory` を低い limit (例: 2/分) で構築し、 listener に注入。 期待を超えて consume
 * したときに 429 レスポンスが返ることをアサートする。
 */
final class RateLimitListenerTest extends TestCase
{
    private TokenStorageInterface $tokenStorage;
    private RateLimitListener $listener;

    protected function setUp(): void
    {
        $ipLimiter = new RateLimiterFactory(
            ['id' => 'mcp_ip', 'policy' => 'fixed_window', 'limit' => 2, 'interval' => '1 minute'],
            new InMemoryStorage(),
        );
        $clientLimiter = new RateLimiterFactory(
            ['id' => 'mcp_client', 'policy' => 'fixed_window', 'limit' => 2, 'interval' => '1 minute'],
            new InMemoryStorage(),
        );
        $this->tokenStorage = new TokenStorage();

        $this->listener = new RateLimitListener(
            eccubeAdminRoute: 'admin',
            mcpIpLimiter: $ipLimiter,
            mcpClientLimiter: $clientLimiter,
            tokenStorage: $this->tokenStorage,
            auditLogger: new McpAuditLogger(new NullLogger(), new RequestStack()),
            logger: new NullLogger(),
        );
    }

    public function testIpLimitAllowsUpToLimit(): void
    {
        $event1 = $this->buildRequestEvent('192.0.2.1', '/admin/mcp');
        $event2 = $this->buildRequestEvent('192.0.2.1', '/admin/mcp');
        $this->listener->onKernelRequest($event1);
        $this->listener->onKernelRequest($event2);

        $this->assertNotInstanceOf(Response::class, $event1->getResponse(), '1 回目は通過');
        $this->assertNotInstanceOf(Response::class, $event2->getResponse(), '2 回目も通過 (limit=2)');
    }

    public function testIpLimitBlocksOverLimit(): void
    {
        for ($i = 0; $i < 2; ++$i) {
            $this->listener->onKernelRequest($this->buildRequestEvent('192.0.2.2', '/admin/mcp'));
        }

        $event3 = $this->buildRequestEvent('192.0.2.2', '/admin/mcp');
        $this->listener->onKernelRequest($event3);

        $response = $event3->getResponse();
        $this->assertInstanceOf(Response::class, $response, '3 回目で 429 を期待');
        $this->assertSame(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode(), (string) $response->getContent());
        $this->assertNotNull($response->headers->get('Retry-After'));
        $this->assertSame('0', $response->headers->get('X-RateLimit-Remaining'));

        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('rate_limited', $body['error'] ?? null);
        $this->assertIsInt($body['retry_after_seconds'] ?? null);
    }

    public function testIpLimitIsScopedToMcpPath(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            // 別パス (例: 通常の admin) では limiter は消費されない
            $this->listener->onKernelRequest($this->buildRequestEvent('192.0.2.3', '/admin/dashboard'));
        }

        $event = $this->buildRequestEvent('192.0.2.3', '/admin/mcp');
        $this->listener->onKernelRequest($event);
        $this->assertNotInstanceOf(Response::class, $event->getResponse(), 'MCP 以外の path は消費しない');
    }

    public function testClientIdLimitConsumesOnlyWhenOAuth2TokenPresent(): void
    {
        // token なし: client_id 制限は消費されない
        for ($i = 0; $i < 5; ++$i) {
            $event = $this->buildControllerEvent('/admin/mcp');
            $this->listener->onKernelController($event);
        }

        $this->tokenStorage->setToken($this->buildOAuth2Token('test-client'));
        $event1 = $this->buildControllerEvent('/admin/mcp');
        $event2 = $this->buildControllerEvent('/admin/mcp');
        $this->listener->onKernelController($event1);
        $this->listener->onKernelController($event2);

        // 差し替えが起きていなければ、 元の controller が `Response('original')` を返す
        $this->assertSame('original', $event1->getController()()->getContent(), '1 回目は通過 (controller 据え置き)');
        $this->assertSame('original', $event2->getController()()->getContent(), '2 回目も通過 (limit=2)');
    }

    public function testClientIdLimitBlocksOverLimit(): void
    {
        $this->tokenStorage->setToken($this->buildOAuth2Token('over-limit-client'));

        for ($i = 0; $i < 2; ++$i) {
            $this->listener->onKernelController($this->buildControllerEvent('/admin/mcp'));
        }

        $event3 = $this->buildControllerEvent('/admin/mcp');
        $this->listener->onKernelController($event3);

        $controller = $event3->getController();
        $this->assertIsCallable($controller);
        $response = $controller();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode(), (string) $response->getContent());

        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('rate_limited', $body['error'] ?? null);
    }

    public function testFailsClosedWhenLimiterStorageThrows(): void
    {
        // cache (カウンタ保存先) 障害を模した、 fetch で例外を投げる storage
        $brokenLimiter = new RateLimiterFactory(
            ['id' => 'mcp_ip', 'policy' => 'fixed_window', 'limit' => 2, 'interval' => '1 minute'],
            new class implements StorageInterface {
                #[\Override]
                public function save(LimiterStateInterface $limiterState): void
                {
                    throw new \RuntimeException('cache down');
                }

                #[\Override]
                public function fetch(string $limiterStateId): ?LimiterStateInterface
                {
                    throw new \RuntimeException('cache down');
                }

                #[\Override]
                public function delete(string $limiterStateId): void
                {
                }
            },
        );

        $listener = new RateLimitListener(
            eccubeAdminRoute: 'admin',
            mcpIpLimiter: $brokenLimiter,
            mcpClientLimiter: $brokenLimiter,
            tokenStorage: new TokenStorage(),
            auditLogger: new McpAuditLogger(new NullLogger(), new RequestStack()),
            logger: new NullLogger(),
        );

        $event = $this->buildRequestEvent('192.0.2.9', '/admin/mcp');
        $listener->onKernelRequest($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response, 'cache 障害時は素通しせず拒否する (fail-closed)');
        $this->assertSame(Response::HTTP_SERVICE_UNAVAILABLE, $response->getStatusCode(), (string) $response->getContent());

        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('rate_limiter_unavailable', $body['error'] ?? null);
    }

    public function testAuditFailureIsRecordedToFallbackAndResponsePreserved(): void
    {
        // mcp 監査チャンネル書き込みが落ちる状況を模す
        $throwingAuditLogger = new McpAuditLogger(
            new class extends AbstractLogger {
                public function log($level, string|\Stringable $message, array $context = []): void
                {
                    throw new \RuntimeException('mcp channel down');
                }
            },
            new RequestStack(),
        );
        // フォールバック先 (default チャンネル) の記録を捕捉する spy
        $fallback = new class extends AbstractLogger {
            /** @var list<string> */
            public array $messages = [];

            public function log($level, string|\Stringable $message, array $context = []): void
            {
                $this->messages[] = (string) $message;
            }
        };
        $ipLimiter = new RateLimiterFactory(
            ['id' => 'mcp_ip', 'policy' => 'fixed_window', 'limit' => 1, 'interval' => '1 minute'],
            new InMemoryStorage(),
        );

        $listener = new RateLimitListener(
            eccubeAdminRoute: 'admin',
            mcpIpLimiter: $ipLimiter,
            mcpClientLimiter: $ipLimiter,
            tokenStorage: new TokenStorage(),
            auditLogger: $throwingAuditLogger,
            logger: $fallback,
        );

        // limit=1: 2 回目で 429 → RateLimited 監査 → 監査が throw → safeAudit が fallback に記録
        $listener->onKernelRequest($this->buildRequestEvent('192.0.2.50', '/admin/mcp'));
        $event = $this->buildRequestEvent('192.0.2.50', '/admin/mcp');
        $listener->onKernelRequest($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response, '監査失敗でも 429 は返る');
        $this->assertSame(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode(), (string) $response->getContent());
        $this->assertNotEmpty($fallback->messages, '監査失敗が fallback logger に記録される (完全沈黙しない)');
        $this->assertStringContainsString('mcp 監査ログの書き込みに失敗', $fallback->messages[0]);
    }

    private function buildRequestEvent(string $ip, string $path): RequestEvent
    {
        $request = Request::create($path, Request::METHOD_POST);
        $request->server->set('REMOTE_ADDR', $ip);

        return new RequestEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
    }

    private function buildControllerEvent(string $path): ControllerEvent
    {
        $request = Request::create($path, Request::METHOD_POST);

        return new ControllerEvent(
            $this->createStub(HttpKernelInterface::class),
            static fn (): Response => new Response('original'),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
    }

    /**
     * client_id 単位の制限は、 listener が `getOAuthClientId()` の有無で対象を判定する。
     * league の具象 OAuth2Token に依存しないよう、 同メソッドを持つ最小トークンで代替する。
     */
    private function buildOAuth2Token(string $clientId): TokenInterface
    {
        return new class($clientId) extends AbstractToken {
            public function __construct(private readonly string $oauthClientId)
            {
                parent::__construct();
            }

            public function getOAuthClientId(): string
            {
                return $this->oauthClientId;
            }
        };
    }
}
