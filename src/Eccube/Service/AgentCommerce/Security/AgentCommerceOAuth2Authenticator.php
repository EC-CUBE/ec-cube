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

use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * エージェントコマースのインバウンド OAuth2 トークン検証 + scope×protocol 照合.
 *
 * 設計方針 (#6777):
 * - **eccube-api4 の具象には依存しない**。トークン検証は Symfony 標準の
 *   {@link AccessTokenHandlerInterface} 経由で行い、実体 (リソースサーバー) は
 *   eccube-api4 が提供する想定 ([EC-CUBE/eccube-api4#188])。
 * - eccube-api4 未導入時は handler が null となり、503 (ServiceUnavailable) を返す
 *   フィーチャーフラグ的フォールバックとする。
 * - 付与 scope は {@link UserBadge} の attributes に handler が載せる前提
 *   (`scopes`: array<string> もしくは `scope`: 空白区切り string)。
 *
 * 本クラスは検証ロジックの中核であり、firewall への配線 (Symfony の access_token
 * authenticator 化) は checkout エンドポイントを持つ #6776/#6574 で行う。
 */
class AgentCommerceOAuth2Authenticator
{
    public function __construct(
        private readonly AgentCommerceScopeRegistry $scopeRegistry,
        private readonly ?AccessTokenHandlerInterface $accessTokenHandler = null,
    ) {
    }

    /**
     * eccube-api4 (リソースサーバー) が利用可能か.
     */
    public function isAvailable(): bool
    {
        return $this->accessTokenHandler !== null;
    }

    /**
     * アクセストークンを検証し、protocol/capability に必要な scope を持つか確認する.
     *
     * @return UserBadge 検証済みユーザーバッジ (subject 識別子 + attributes)
     *
     * @throws ServiceUnavailableHttpException eccube-api4 未導入 (503 相当)
     * @throws AuthenticationException トークン不正 (401 相当)
     * @throws AccessDeniedException scope 不足・protocol 越境 (403 相当)
     */
    public function authenticate(string $accessToken, string $protocol, string $capability): UserBadge
    {
        if ($this->accessTokenHandler === null) {
            throw new ServiceUnavailableHttpException(null, 'OAuth2 resource server (eccube-api4) is not installed');
        }

        // 不正トークンは AuthenticationException (401 相当) が送出される。
        $userBadge = $this->accessTokenHandler->getUserBadgeFrom($accessToken);

        $grantedScopes = $this->extractScopes($userBadge);
        if (!$this->scopeRegistry->supports($protocol, $capability, $grantedScopes)) {
            throw new AccessDeniedException(sprintf('The token is not granted the required scope "%s:%s".', $protocol, $capability));
        }

        return $userBadge;
    }

    /**
     * UserBadge の attributes から付与 scope 一覧を取り出す.
     *
     * `scopes` (array<string>) を優先し、無ければ `scope` (空白区切り string) を解釈する。
     *
     * @return list<string>
     */
    private function extractScopes(UserBadge $userBadge): array
    {
        $attributes = $userBadge->getAttributes() ?? [];

        if (isset($attributes['scopes']) && is_array($attributes['scopes'])) {
            return array_values(array_filter(
                array_map(static fn ($scope): string => (string) $scope, $attributes['scopes']),
                static fn (string $scope): bool => $scope !== '',
            ));
        }

        if (isset($attributes['scope']) && is_string($attributes['scope'])) {
            return array_values(array_filter(explode(' ', $attributes['scope']), static fn (string $scope): bool => $scope !== ''));
        }

        return [];
    }
}
