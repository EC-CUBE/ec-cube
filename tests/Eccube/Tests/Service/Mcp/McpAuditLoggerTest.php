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

namespace Eccube\Tests\Service\Mcp;

use Eccube\Service\Mcp\AuditResult;
use Eccube\Service\Mcp\McpAuditLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * `McpAuditLogger` のユニットテスト。 DB 不要。
 *
 * `AuditResult` から導かれる level と、 同一リクエスト中の `request_id` 永続化を検証する。
 */
final class McpAuditLoggerTest extends TestCase
{
    public function testToolCallSuccessLogsAtInfoLevel(): void
    {
        $logger = $this->captureLogger();
        $auditLogger = new McpAuditLogger($logger, new RequestStack());

        $auditLogger->logToolCall(
            toolName: 'search_products',
            args: ['limit' => 20],
            result: AuditResult::Success,
            durationMs: 12.3,
            resultSummary: ['total' => 5],
        );

        $records = $logger->records;
        $this->assertCount(1, $records);
        $this->assertSame('info', $records[0]['level']);
        $this->assertSame('mcp.tool.search_products', $records[0]['message']);
        $this->assertSame('search_products', $records[0]['context']['tool_name']);
        $this->assertSame(['limit' => 20], $records[0]['context']['tool_args']);
        $this->assertSame('success', $records[0]['context']['result_status']);
        $this->assertSame(['total' => 5], $records[0]['context']['result_summary']);
        $this->assertEqualsWithDelta(12.3, $records[0]['context']['duration_ms'], PHP_FLOAT_EPSILON);
    }

    public function testScopeDeniedLogsAtWarningLevel(): void
    {
        $logger = $this->captureLogger();
        $auditLogger = new McpAuditLogger($logger, new RequestStack());

        $auditLogger->logToolCall('search_products', [], AuditResult::ScopeDenied, 0.5);

        $this->assertSame('warning', $logger->records[0]['level']);
        $this->assertSame('scope_denied', $logger->records[0]['context']['result_status']);
    }

    public function testInternalErrorLogsAtErrorLevel(): void
    {
        $logger = $this->captureLogger();
        $auditLogger = new McpAuditLogger($logger, new RequestStack());

        $auditLogger->logToolCall('search_products', [], AuditResult::InternalError, 0.5);

        $this->assertSame('error', $logger->records[0]['level']);
    }

    public function testSecurityEventUsesWarningLevel(): void
    {
        $logger = $this->captureLogger();
        $auditLogger = new McpAuditLogger($logger, new RequestStack());

        $auditLogger->logSecurityEvent(AuditResult::OriginInvalid, ['origin' => 'http://evil']);

        $this->assertSame('warning', $logger->records[0]['level']);
        $this->assertSame('mcp.security.origin_invalid', $logger->records[0]['message']);
        $this->assertSame('http://evil', $logger->records[0]['context']['origin']);
    }

    public function testRequestIdIsStableWithinSameRequest(): void
    {
        $logger = $this->captureLogger();
        $stack = new RequestStack();
        $stack->push(new Request());
        $auditLogger = new McpAuditLogger($logger, $stack);

        $auditLogger->logToolCall('a', [], AuditResult::Success, 1.0);
        $auditLogger->logSecurityEvent(AuditResult::OriginInvalid);

        $first = $logger->records[0]['context']['request_id'];
        $second = $logger->records[1]['context']['request_id'];
        $this->assertNotNull($first);
        $this->assertSame($first, $second, '同一リクエスト中は request_id が一定');
    }

    public function testRequestIdFallbackWhenNoRequest(): void
    {
        $logger = $this->captureLogger();
        $auditLogger = new McpAuditLogger($logger, new RequestStack());

        $auditLogger->logToolCall('a', [], AuditResult::Success, 1.0);

        $requestId = $logger->records[0]['context']['request_id'];
        $this->assertIsString($requestId);
        $this->assertNotSame('', $requestId);
    }

    private function captureLogger(): CapturingLogger
    {
        return new CapturingLogger();
    }
}

/**
 * @internal テストでログレコードを収集する PSR-3 ロガー。 名前付きクラスにしている理由は
 * PHPStan が anonymous class の `->records` アクセスを type-narrow できないため。
 */
final class CapturingLogger extends AbstractLogger
{
    /** @var list<array{level:string,message:string,context:array<string, mixed>}> */
    public array $records = [];

    #[\Override]
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->records[] = [
            'level' => (string) $level,
            'message' => (string) $message,
            'context' => $context,
        ];
    }
}
