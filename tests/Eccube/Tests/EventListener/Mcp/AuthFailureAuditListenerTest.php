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

use Eccube\EventListener\Mcp\AuthFailureAuditListener;
use Eccube\Service\Mcp\McpAuditLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * `AuthFailureAuditListener` のユニットテスト。
 *
 * mcp パスの 401 レスポンスを監査ログに warning で残し、 mcp 以外 / 401 以外は無視することを検証する。
 */
final class AuthFailureAuditListenerTest extends TestCase
{
    public function testLogsTokenInvalidAtWarningForMcp401(): void
    {
        $recorder = $this->recordingLogger();
        $this->buildListener($recorder)->onKernelResponse(
            $this->responseEvent('/admin/mcp', Response::HTTP_UNAUTHORIZED),
        );

        $this->assertCount(1, $recorder->records, 'mcp パスの 401 を 1 行記録する');
        $record = $recorder->records[0];
        $this->assertSame('warning', $record['level'], '認証失敗はクライアント都合なので warning');
        $this->assertSame('mcp.auth.token_invalid', $record['message']);
        $this->assertSame('token_invalid', $record['context']['result_status'] ?? null);
        $this->assertArrayHasKey('client_id', $record['context']);
        $this->assertNull($record['context']['client_id'], 'client_id は best-effort で null');
        $this->assertSame('unauthorized', $record['context']['reason'] ?? null, 'WWW-Authenticate 無しは fallback');
    }

    public function testReasonComesFromWwwAuthenticateHeader(): void
    {
        $recorder = $this->recordingLogger();
        $this->buildListener($recorder)->onKernelResponse($this->responseEvent(
            '/admin/mcp',
            Response::HTTP_UNAUTHORIZED,
            ['WWW-Authenticate' => 'Bearer error="invalid_token"'],
        ));

        $this->assertCount(1, $recorder->records);
        $this->assertSame('Bearer error="invalid_token"', $recorder->records[0]['context']['reason'] ?? null);
    }

    public function testIgnoresNon401Response(): void
    {
        $recorder = $this->recordingLogger();
        $this->buildListener($recorder)->onKernelResponse(
            $this->responseEvent('/admin/mcp', Response::HTTP_OK),
        );

        $this->assertSame([], $recorder->records, 'mcp パスでも 401 以外は記録しない (scope 拒否=200 等)');
    }

    public function testIgnoresNonMcpPath(): void
    {
        $recorder = $this->recordingLogger();
        $this->buildListener($recorder)->onKernelResponse(
            $this->responseEvent('/admin/product', Response::HTTP_UNAUTHORIZED),
        );

        $this->assertSame([], $recorder->records, 'mcp 以外のパスの 401 は記録しない');
    }

    public function testAuditFailureDoesNotBreakResponse(): void
    {
        $throwingAudit = new McpAuditLogger(
            new class extends AbstractLogger {
                /**
                 * @param array<string, mixed> $context
                 */
                public function log(mixed $level, string|\Stringable $message, array $context = []): void
                {
                    throw new \RuntimeException('mcp channel down');
                }
            },
            new RequestStack(),
        );
        $listener = new AuthFailureAuditListener('admin', $throwingAudit, new NullLogger());

        // 監査が throw しても例外が伝播しない (401 応答を壊さない)
        $listener->onKernelResponse($this->responseEvent('/admin/mcp', Response::HTTP_UNAUTHORIZED));

        $this->addToAssertionCount(1);
    }

    private function buildListener(AbstractLogger $recorder): AuthFailureAuditListener
    {
        // recorder を mcp チャネルロガーに据え、 監査出力 (logAuthEvent) を捕捉する。 fallback は使わない
        return new AuthFailureAuditListener('admin', new McpAuditLogger($recorder, new RequestStack()), new NullLogger());
    }

    /**
     * @return AbstractLogger&object{records: list<array{level: mixed, message: string, context: array<string, mixed>}>}
     */
    private function recordingLogger(): AbstractLogger
    {
        return new class extends AbstractLogger {
            /** @var list<array{level: mixed, message: string, context: array<string, mixed>}> */
            public array $records = [];

            /**
             * @param array<string, mixed> $context
             */
            public function log(mixed $level, string|\Stringable $message, array $context = []): void
            {
                $this->records[] = ['level' => $level, 'message' => (string) $message, 'context' => $context];
            }
        };
    }

    /**
     * @param array<string, string> $headers
     */
    private function responseEvent(string $path, int $statusCode, array $headers = []): ResponseEvent
    {
        return new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create($path, Request::METHOD_POST),
            HttpKernelInterface::MAIN_REQUEST,
            new Response('', $statusCode, $headers),
        );
    }
}
