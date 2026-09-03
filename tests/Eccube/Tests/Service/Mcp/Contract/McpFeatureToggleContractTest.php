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
use Eccube\Tests\Service\Mcp\EnablesMcpTrait;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * MCP 機能フラグ (`BaseInfo::mcp_enabled`、 既定 OFF) が `^/admin/mcp` の到達可否を切り替えることの HTTP 契約テスト。
 *
 * - OFF (既定): `McpEnabledListener`(priority 33) が firewall より前で **404** を返し、 エンドポイントの存在自体を隠す。
 * - ON: リスナは素通しし、 oauth2 firewall に到達する (Bearer なしは 401)。
 */
#[Group('mcp')]
final class McpFeatureToggleContractTest extends EccubeTestCase
{
    use EnablesMcpTrait;

    public function testReturns404WhenDisabledByDefault(): void
    {
        // 既定 OFF のまま (enable しない)
        $this->request();

        $this->assertSame(
            Response::HTTP_NOT_FOUND,
            $this->client->getResponse()->getStatusCode(),
            'MCP 機能 OFF では ^/admin/mcp は 404 (存在秘匿)',
        );
        $this->assertSame(
            '',
            (string) $this->client->getResponse()->getContent(),
            'listener 由来の 404 は空ボディ (routing 由来 404 の HTML ではない)',
        );
    }

    public function testReachesFirewallWhenEnabled(): void
    {
        $this->setMcpEnabled(true);

        $this->request();

        $this->assertSame(
            Response::HTTP_UNAUTHORIZED,
            $this->client->getResponse()->getStatusCode(),
            'MCP 機能 ON では 404 でなく oauth2 firewall に到達し 401 (Bearer なし)',
        );
        $this->assertTrue(
            $this->client->getResponse()->headers->has('WWW-Authenticate'),
            'ON では mcp firewall の entry_point に到達し WWW-Authenticate が付く',
        );
    }

    public function testDisabledMasks415ForInvalidContentType(): void
    {
        // OFF の間は Origin/CT ガード (415) より前に 404 で塞ぎ、 不正 CT でもエンドポイントの存在を漏らさない
        $this->request('text/plain');

        $this->assertSame(
            Response::HTTP_NOT_FOUND,
            $this->client->getResponse()->getStatusCode(),
            'OFF では不正 Content-Type でも 415 でなく 404 (listener が Origin/CT ガードより前)',
        );
    }

    private function request(string $contentType = 'application/json'): void
    {
        $route = static::getContainer()->getParameter('eccube_admin_route');
        \assert(\is_string($route));

        $this->client->request(
            Request::METHOD_POST,
            '/'.$route.'/mcp',
            server: ['CONTENT_TYPE' => $contentType],
            content: '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-03-26","clientInfo":{"name":"t","version":"1"},"capabilities":{}}}',
        );
    }
}
