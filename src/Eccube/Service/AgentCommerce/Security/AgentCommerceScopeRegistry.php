<?php

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

namespace Eccube\Service\AgentCommerce\Security;

/**
 * エージェントコマース (ACP / UCP) の OAuth2 scope を正準化するレジストリ.
 *
 * scope は "<protocol>:<capability>" 形式で統一する (例: "ucp:checkout")。
 * "agent:catalog:read" のような旧形式は無効とする。protocol 越境
 * (例: acp トークンで ucp capability を要求) は許可しない。
 */
class AgentCommerceScopeRegistry
{
    /**
     * 正準 scope レジストリ. protocol => 許可する capability の配列.
     *
     * app/Customize で差し替えやすいようメソッド経由で参照する。
     *
     * @var array<string, list<string>>
     */
    private const CANONICAL_SCOPES = [
        'acp' => ['checkout', 'catalog'],
        'ucp' => ['checkout', 'cart', 'catalog', 'identity'],
    ];

    /**
     * scope 文字列が正準レジストリに存在するか判定する.
     *
     * "ucp:checkout" などレジストリに定義された "<protocol>:<capability>" のみ true。
     */
    public function isValidScope(string $scope): bool
    {
        $parts = explode(':', $scope);

        // 正確に protocol:capability の 2 要素でなければ無効 (例: "agent:catalog:read" は 3 要素)。
        if (count($parts) !== 2) {
            return false;
        }

        [$protocol, $capability] = $parts;

        if ($protocol === '' || $capability === '') {
            return false;
        }

        return isset(self::CANONICAL_SCOPES[$protocol])
            && in_array($capability, self::CANONICAL_SCOPES[$protocol], true);
    }

    /**
     * 指定 protocol が持つ全 scope 文字列を返す.
     *
     * 例: "ucp" => ["ucp:checkout", "ucp:cart", "ucp:catalog", "ucp:identity"]
     *
     * @return list<string>
     */
    public function scopesForProtocol(string $protocol): array
    {
        if (!isset(self::CANONICAL_SCOPES[$protocol])) {
            return [];
        }

        return array_map(
            static fn (string $capability): string => $protocol.':'.$capability,
            self::CANONICAL_SCOPES[$protocol]
        );
    }

    /**
     * 付与済み scope 群が、指定 protocol/capability の操作を許可しているか判定する.
     *
     * grantedScopes に "<protocol>:<capability>" が含まれ、かつその scope が
     * 正準であり protocol が一致するときのみ true。protocol 越境は false。
     *
     * @param list<string> $grantedScopes トークンに付与された scope 文字列の配列
     */
    public function supports(string $protocol, string $capability, array $grantedScopes): bool
    {
        $required = $protocol.':'.$capability;

        // 要求 scope 自体が正準でなければ許可しない (越境・未定義の capability を排除)。
        if (!$this->isValidScope($required)) {
            return false;
        }

        return in_array($required, $grantedScopes, true);
    }
}
