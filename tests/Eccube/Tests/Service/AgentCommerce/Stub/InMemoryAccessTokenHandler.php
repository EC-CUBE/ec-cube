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

namespace Eccube\Tests\Service\AgentCommerce\Stub;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * テスト用の OAuth2 アクセストークンハンドラスタブ (eccube-api4 非依存).
 *
 * 実運用では eccube-api4 のリソースサーバーが提供する {@link AccessTokenHandlerInterface} を、
 * 単体・機能テストではこのスタブで代替し、トークン文字列に応じて scope 付き UserBadge を返す
 * (または不正トークンとして例外を送出する)。これにより eccube-api4 を導入せずに
 * {@link \Eccube\Service\AgentCommerce\Security\AgentCommerceOAuth2Authenticator} の
 * 200/401/403 分岐を検証できる。
 *
 * トークン規約:
 * - `acp-checkout-token`  : scope `acp:checkout` を持つ正当なクライアント
 * - `acp-catalog-token`   : scope `acp:catalog` のみ (checkout には不足 → 403)
 * - `ucp-checkout-token`  : scope `ucp:checkout` のみ (acp 越境 → 403)
 * - それ以外               : 不正トークン (401)
 */
class InMemoryAccessTokenHandler implements AccessTokenHandlerInterface
{
    /**
     * @var array<string, array{identifier: string, scopes: list<string>}>
     */
    private const TOKENS = [
        'acp-checkout-token' => ['identifier' => 'acp-client-1', 'scopes' => ['acp:checkout', 'acp:catalog']],
        'acp-catalog-token' => ['identifier' => 'acp-client-2', 'scopes' => ['acp:catalog']],
        'ucp-checkout-token' => ['identifier' => 'ucp-client-1', 'scopes' => ['ucp:checkout']],
    ];

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $token = self::TOKENS[$accessToken] ?? null;
        if ($token === null) {
            throw new BadCredentialsException('Invalid access token.');
        }

        return new UserBadge($token['identifier'], null, ['scopes' => $token['scopes']]);
    }
}
