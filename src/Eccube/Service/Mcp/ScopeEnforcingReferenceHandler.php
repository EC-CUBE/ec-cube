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

use Mcp\Capability\Registry\ElementReference;
use Mcp\Capability\Registry\ReferenceHandlerInterface;
use Mcp\Capability\Registry\ToolReference;
use Mcp\Exception\ToolCallException;

/**
 * MCP の scope 検査を「mcp-bundle が Tool を呼ぶ手前の 1 箇所」 に強制する {@see ReferenceHandlerInterface}
 * の実装。
 *
 * mcp/sdk の `CallToolHandler` は Tool 実行を `ReferenceHandlerInterface::handle()` に委譲する。 本クラスを
 * `Builder::setReferenceHandler()` (compiler pass で配線) に差し込むことで、 **全 Tool 呼び出しが必ず本層を
 * 通過する**。 Tool 自身は scope を持たない。
 *
 * 検査は {@see McpToolScopeMap} に従う:
 *   - Tool 名が未登録 → **fail-closed deny** (誰の token でも呼べない)
 *   - 登録あり → {@see ScopeChecker} で `IsGranted` 検査。 不足なら `ToolCallException`
 *
 * `ToolCallException` は `CallToolHandler` が catch して JSON-RPC `result.isError = true` に整形するため、
 * HTTP は 200 のまま LLM 側に「呼べなかった」「必要 scope」 が伝わる (詳細: docs/mcp/scope-denied-response.md)。
 *
 * Tool 以外の参照 (prompt / resource) は scope 対象外なので素通しする。
 */
final readonly class ScopeEnforcingReferenceHandler implements ReferenceHandlerInterface
{
    public function __construct(
        private ReferenceHandlerInterface $inner,
        private ScopeChecker $scopeChecker,
        private McpAuditLogger $auditLogger,
    ) {
    }

    /**
     * @param array<string, mixed> $arguments
     */
    #[\Override]
    public function handle(ElementReference $reference, array $arguments): mixed
    {
        if ($reference instanceof ToolReference) {
            $this->enforce($reference->tool->name);
        }

        return $this->inner->handle($reference, $arguments);
    }

    /**
     * Tool 名に対応する必要 scope を中央マップで引き、 認可する。 拒否時は監査ログを残して
     * `ToolCallException` を投げる。
     */
    private function enforce(string $toolName): void
    {
        $role = McpToolScopeMap::requiredRole($toolName);

        if (null === $role) {
            // 中央マップ未登録 = fail-closed。 新規 Tool の scope 登録漏れは「全 deny」 に倒す
            $this->auditLogger->logSecurityEvent(AuditResult::ScopeDenied, [
                'tool' => $toolName,
                'reason' => 'no_scope_mapping',
            ]);

            throw new ToolCallException(sprintf('Tool "%s" is not authorized: no scope mapping registered.', $toolName));
        }

        try {
            $this->scopeChecker->require($role);
        } catch (ToolCallException $e) {
            $this->auditLogger->logSecurityEvent(AuditResult::ScopeDenied, [
                'tool' => $toolName,
                'reason' => 'insufficient_scope',
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
