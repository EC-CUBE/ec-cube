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

namespace Eccube\Tests\Service\Mcp\Contract;

use Eccube\Tests\EccubeTestCase;

/**
 * MCP firewall (Api44 が prepend する `mcp` stateless OAuth2 リソースサーバ) が、 admin Cookie firewall
 * ではなく oauth2 firewall を通ることを保証する HTTP 統合テスト。
 *
 * 設計 §8 の AC のうち以下を間接的に担保する:
 *   - #6 「トークン失効 (or 期限切れ) は即 401」 — Bearer なし / 不正 Bearer で 401 を返すなら、 league の
 *     `isAccessTokenRevoked` 経路に到達している (= 失効 token も 401 になる、 league の検証ロジックを信頼)
 *   - #9 「発行 Member の無効化が即 401」 — 同様に MemberProvider 経由の解決が走るため、 Member が無効化
 *     されれば 401 になる (= league の `member_provider` 解決を信頼)
 *
 * 直接「revoked 状態の DB レコードを置く」 までやるには league 内部の JWT 自前発行が必要で規模が大きい。
 * ここでは firewall 経路の入口を担保することで、 league 側の責務に踏み込まず最小コストで AC を実証する。
 *
 * Api44 install + enable 状態が前提 (テスト DB に Api44 が登録済み)。
 */
final class McpFirewallContractTest extends EccubeTestCase
{
    public function testReturns401WithoutBearerHeader(): void
    {
        $this->client->request(
            'POST',
            '/'.$this->getAdminRoute().'/mcp',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-03-26","clientInfo":{"name":"t","version":"1"},"capabilities":{}}}',
        );

        $response = $this->client->getResponse();
        $this->assertSame(401, $response->getStatusCode(), 'Bearer なしは admin Cookie firewall ではなく oauth2 firewall で 401');
    }

    public function testReturns401WithInvalidBearer(): void
    {
        $this->client->request(
            'POST',
            '/'.$this->getAdminRoute().'/mcp',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer invalid-opaque-token-that-does-not-match-any-jwt',
            ],
            content: '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-03-26","clientInfo":{"name":"t","version":"1"},"capabilities":{}}}',
        );

        $response = $this->client->getResponse();
        $this->assertSame(401, $response->getStatusCode(), '不正な Bearer は league の JWT 検証で 401');
    }

    public function testReturns401WithMalformedJwt(): void
    {
        // 「JWT に見える」 が署名が不正な値。 league の SignedJWT validator で reject される
        $this->client->request(
            'POST',
            '/'.$this->getAdminRoute().'/mcp',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJtY3AtdGVzdCJ9.invalid_signature',
            ],
            content: '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-03-26","clientInfo":{"name":"t","version":"1"},"capabilities":{}}}',
        );

        $response = $this->client->getResponse();
        $this->assertSame(401, $response->getStatusCode(), '署名不正な JWT は 401');
    }

    private function getAdminRoute(): string
    {
        $route = static::getContainer()->getParameter('eccube_admin_route');
        \assert(\is_string($route));

        return $route;
    }
}
