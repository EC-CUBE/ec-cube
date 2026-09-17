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

/**
 * MCP Tool が必ず行う 2 つの横断処理を一括化する: (1) 実行時間計測、 (2) 監査ログ
 * (Success / InternalError の二分岐)。
 *
 * scope 検査は本クラスでは行わない。 Tool 呼び出しの手前にある {@see ScopeEnforcingReferenceHandler}
 * が中央で強制する。 ToolInvoker は実行時間計測と監査ログに専念する。
 *
 * 各 Tool は本クラスに「実際の業務処理」 (callable) を渡す。 Result は `data` / `summary` に分け、
 * summary だけ監査ログに残す (本体は記録しない)。
 *
 * @phpstan-type ToolResultArray array{data: array<string, mixed>, summary?: array<string, mixed>|null}
 */
final readonly class ToolInvoker
{
    public function __construct(
        private McpAuditLogger $auditLogger,
    ) {
    }

    /**
     * Tool を実行する。 `$work` は `{data: ..., summary?: ...}` を返す callable。
     *
     * @param array<string, mixed>             $args 監査ログに記録する引数 (個人情報を含み得る)
     * @param callable(): array<string, mixed> $work 業務処理本体。 戻り値は `{data, summary?}`
     *
     * @return array<string, mixed> work() が返した `data` 部分。 そのまま MCP の JSON-RPC result に渡る
     */
    public function invoke(
        string $toolName,
        array $args,
        callable $work,
    ): array {
        $startedAt = microtime(true);

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

        // data キー欠落・非配列は Tool 実装の契約違反。 空の正常応答に化けさせず内部エラーとして扱う
        // (静かに success へ握り潰すと、 クライアントも監査ログも実装バグを検知できない)。
        if (!\array_key_exists('data', $outcome) || !\is_array($outcome['data'])) {
            $this->auditLogger->logToolCall(
                toolName: $toolName,
                args: $args,
                result: AuditResult::InternalError,
                durationMs: $this->elapsedMs($startedAt),
            );

            throw new \UnexpectedValueException('Tool result must contain an array `data` key.');
        }

        $summary = $outcome['summary'] ?? null;

        $this->auditLogger->logToolCall(
            toolName: $toolName,
            args: $args,
            result: AuditResult::Success,
            durationMs: $this->elapsedMs($startedAt),
            resultSummary: \is_array($summary) ? $summary : null,
        );

        return $outcome['data'];
    }

    private function elapsedMs(float $startedAt): float
    {
        return (microtime(true) - $startedAt) * 1000;
    }
}
