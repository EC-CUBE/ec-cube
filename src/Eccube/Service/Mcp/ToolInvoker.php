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

namespace Eccube\Service\Mcp;

use Mcp\Exception\ToolCallException;

/**
 * MCP Tool が必ず行う 3 つの横断処理を一括化する: (1) 必要 scope の検査、 (2) 実行時間計測、
 * (3) 監査ログ (Success / ScopeDenied / InternalError の三分岐)。
 *
 * 各 Tool は本クラスに「実際の業務処理」 (callable) を渡し、 業務ロジックに専念する。
 * Result は判別共用体的に `ToolResult` の `data` / `summary` に分け、 summary だけ監査ログに
 * 残す (本体は記録しない)。
 *
 * @phpstan-type ToolResultArray array{data: array<string, mixed>, summary?: array<string, mixed>|null}
 */
final readonly class ToolInvoker
{
    public function __construct(
        private ScopeChecker $scopeChecker,
        private McpAuditLogger $auditLogger,
    ) {
    }

    /**
     * Tool を実行する。 `$work` は `{data: ..., summary?: ...}` を返す callable。
     *
     * @param array<string, mixed>             $args         監査ログに記録する引数 (個人情報を含み得る)
     * @param callable(): array<string, mixed> $work         業務処理本体。 戻り値は `{data, summary?}`
     *
     * @return array<string, mixed> work() が返した `data` 部分。 そのまま MCP の JSON-RPC result に渡る
     */
    public function invoke(
        string $toolName,
        string $requiredScope,
        array $args,
        callable $work,
    ): array {
        $startedAt = microtime(true);

        try {
            $this->scopeChecker->require($requiredScope);
        } catch (ToolCallException $e) {
            $this->auditLogger->logToolCall(
                toolName: $toolName,
                args: $args,
                result: AuditResult::ScopeDenied,
                durationMs: $this->elapsedMs($startedAt),
            );
            throw $e;
        }

        try {
            $outcome = $work();
        } catch (\Throwable $e) {
            $this->auditLogger->logToolCall(
                toolName: $toolName,
                args: $args,
                result: AuditResult::InternalError,
                durationMs: $this->elapsedMs($startedAt),
            );
            throw $e;
        }

        $data = $outcome['data'] ?? [];
        $summary = $outcome['summary'] ?? null;

        $this->auditLogger->logToolCall(
            toolName: $toolName,
            args: $args,
            result: AuditResult::Success,
            durationMs: $this->elapsedMs($startedAt),
            resultSummary: \is_array($summary) ? $summary : null,
        );

        return \is_array($data) ? $data : [];
    }

    private function elapsedMs(float $startedAt): float
    {
        return (microtime(true) - $startedAt) * 1000;
    }
}
