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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * MCP Tool が要求する scope (role) を `AuthorizationCheckerInterface` で検査する薄いヘルパ。
 *
 * 拒否時は `Mcp\Exception\ToolCallException` を投げる。 mcp-bundle (mcp/sdk) の
 * `CallToolHandler` が `ToolCallException` を専用パスで catch し、 JSON-RPC レスポンスを
 * `result.isError = true` + `content[0].text = (例外メッセージ)` の形に整形して返す。
 * HTTP は 200 のままだが、 LLM 側は「呼べなかった」「どの scope が必要か」を読める。
 *
 * mcp-bundle は Tool 内例外を全て catch して JSON-RPC result に変換するため、 HTTP 層での
 * 403 化はできない (詳細: docs/mcp/scope-denied-response.md)。
 */
final readonly class ScopeChecker
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    /**
     * 指定 role を持たない場合は `ToolCallException` を投げる。 メッセージは
     * OAuth2 仕様の scope 名 (例: `mcp:order:read`) に戻して LLM 側に伝わるよう整形する。
     */
    public function require(string $role): void
    {
        if (!$this->authorizationChecker->isGranted($role)) {
            throw new ToolCallException(sprintf('Insufficient scope: %s', $this->roleToScope($role)));
        }
    }

    /**
     * `ROLE_OAUTH2_MCP:ORDER:READ` → `mcp:order:read` のように、 内部 role 名を
     * OAuth2 仕様の scope 名に戻す。 規則は `role_prefix: ROLE_OAUTH2_` + 小文字化。
     */
    private function roleToScope(string $role): string
    {
        $prefix = 'ROLE_OAUTH2_';
        if (str_starts_with($role, $prefix)) {
            return strtolower(substr($role, \strlen($prefix)));
        }

        return $role;
    }
}
