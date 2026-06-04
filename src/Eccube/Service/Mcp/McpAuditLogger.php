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

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Ulid;

/**
 * MCP の構造化監査ログを `mcp` チャネルへ書き出す **唯一のエントリポイント** (Single Source of Truth)。
 *
 * 設計 §5 の通り、 Origin ガード / 認証 / scope 認可 / Rate Limiter / Tool は `LoggerInterface`
 * を直接呼ばず、 必ず本クラスを経由する。 ログ項目は §4.2 と一致する。
 *
 * `request_id` は同一リクエスト中の複数ログ呼び出しを束ねるため、 Request の attribute
 * `mcp_request_id` を見て、 無ければ ULID を発行して attribute にもセットする。
 */
final readonly class McpAuditLogger
{
    public const REQUEST_ID_ATTRIBUTE = 'mcp_request_id';

    public function __construct(
        private LoggerInterface $mcpLogger,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * Tool 呼び出し結果をログする。
     *
     * @param array<string, mixed>      $args          Tool に渡された引数 (個人情報を含み得る点に注意)
     * @param array<string, mixed>|null $resultSummary 結果の要約 (件数等)。 結果本体は記録しない
     */
    public function logToolCall(
        string $toolName,
        array $args,
        AuditResult $result,
        float $durationMs,
        ?array $resultSummary = null,
        ?string $clientId = null,
        ?int $memberId = null,
    ): void {
        $this->emit(
            level: $this->levelFor($result),
            message: 'mcp.tool.'.$toolName,
            context: [
                'tool_name' => $toolName,
                'tool_args' => $args,
                'result_status' => $result->value,
                'result_summary' => $resultSummary,
                'duration_ms' => $durationMs,
                'client_id' => $clientId,
                'member_id' => $memberId,
            ],
        );
    }

    /**
     * 認証成否を記録する (Api44 の `kernel.exception` / `security.authentication.success`
     * 等のイベントを拾う listener から呼ぶ想定)。
     *
     * @param array<string, mixed> $context
     */
    public function logAuthEvent(
        AuditResult $result,
        ?string $clientId = null,
        array $context = [],
    ): void {
        $this->emit(
            level: $this->levelFor($result),
            message: 'mcp.auth.'.$result->value,
            context: ['result_status' => $result->value, 'client_id' => $clientId] + $context,
        );
    }

    /**
     * Origin / Content-Type ガード違反、 Rate Limit 超過などのセキュリティ事象を記録する。
     *
     * @param array<string, mixed> $context
     */
    public function logSecurityEvent(AuditResult $result, array $context = []): void
    {
        $this->emit(
            level: $this->levelFor($result),
            message: 'mcp.security.'.$result->value,
            context: ['result_status' => $result->value] + $context,
        );
    }

    /**
     * 監査ログのレベルを `AuditResult` から決める。 設計 §3.3 / §4.2 と一致。
     */
    private function levelFor(AuditResult $result): string
    {
        return match ($result) {
            AuditResult::Success => 'info',
            AuditResult::InternalError, AuditResult::TokenInvalid => 'error',
            default => 'warning',
        };
    }

    /**
     * @param array<string, mixed> $context
     */
    private function emit(string $level, string $message, array $context): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $base = [
            'request_id' => $this->resolveRequestId($request),
            'client_ip' => $request?->getClientIp(),
        ];

        $this->mcpLogger->log($level, $message, [...$base, ...$context]);
    }

    /**
     * リクエストに `mcp_request_id` がなければ ULID を割り当てる (同一リクエスト内のログを束ねる)。
     */
    private function resolveRequestId(?Request $request): string
    {
        if (null === $request) {
            return (new Ulid())->toBase32();
        }

        $existing = $request->attributes->get(self::REQUEST_ID_ATTRIBUTE);
        if (\is_string($existing) && '' !== $existing) {
            return $existing;
        }

        $generated = (new Ulid())->toBase32();
        $request->attributes->set(self::REQUEST_ID_ATTRIBUTE, $generated);

        return $generated;
    }
}
