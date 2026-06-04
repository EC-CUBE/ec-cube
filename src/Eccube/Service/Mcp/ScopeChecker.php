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

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * MCP Tool が要求する scope (role) を `AuthorizationCheckerInterface` で検査する薄いヘルパ。
 *
 * 設計 §3.3 / §5 の通り認可は league の `IsGranted` 機構に一本化。 Tool は呼び出し冒頭で
 * `require()` を呼び、 不足ならば `AccessDeniedException` を上位に投げる。 `#[IsGranted]` 属性は
 * controller resolver からしか発火しないため、 MCP Tool では本ヘルパで明示する。
 */
final readonly class ScopeChecker
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    /**
     * 指定 role を持たない場合は `AccessDeniedException` を投げる。
     */
    public function require(string $role): void
    {
        if (!$this->authorizationChecker->isGranted($role)) {
            throw new AccessDeniedException(sprintf('Insufficient scope: %s', $role));
        }
    }
}
