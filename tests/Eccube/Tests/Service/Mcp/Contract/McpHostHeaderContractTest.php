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
use Eccube\Tests\Service\Mcp\IssuesMcpAccessTokenTrait;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * MCP サーバ (HTTP) が localhost 以外の Host でも動くことと、 ブラウザ発のリクエストの拒否が
 * EC-CUBE 側 (`OriginContentTypeListener`) で引き続き効くことを保証する HTTP 統合テスト。
 *
 * mcp-bundle は `http.allowed_hosts` が未設定だと mcp/sdk の DnsRebindingProtectionMiddleware を
 * 既定 (localhost / 127.0.0.1 / [::1] のみ) で挟むため、 独自ドメインや Docker のホスト名では全リクエストが
 * `403 Forbidden: Invalid Host header.` になっていた。 `mcp.yaml` で `allowed_hosts: false` として外し、
 * Origin の検査は `OriginContentTypeListener` に一本化している。 ここではその両面を固定する。
 *
 * Api44 install + enable 状態が前提 (テスト DB に Api44 が登録済み)。
 */
#[Group('mcp')]
final class McpHostHeaderContractTest extends EccubeTestCase
{
    use EnablesMcpTrait;
    use IssuesMcpAccessTokenTrait;

    private const TEST_CLIENT_ID = 'mcp-host-header-test-client';

    private const HOST = 'example.com';

    private const ALLOWED_ORIGIN = 'https://example.com';

    private const ORIGINS_ENV = 'ECCUBE_MCP_ALLOWED_ORIGINS';

    /** @var array{env: string|null, server: string|null}|null */
    private ?array $originalOrigins = null;

    protected function setUp(): void
    {
        // test 環境 (kernel.debug=true) は許可リスト未設定だと Origin の検査を skip するため、 許可リストを設定して
        // 検査を有効にする。 OriginContentTypeListener の生成 (= 環境変数の解決) より前に設定する必要がある。
        $this->originalOrigins = [
            'env' => isset($_ENV[self::ORIGINS_ENV]) ? (string) $_ENV[self::ORIGINS_ENV] : null,
            'server' => isset($_SERVER[self::ORIGINS_ENV]) ? (string) $_SERVER[self::ORIGINS_ENV] : null,
        ];
        $_ENV[self::ORIGINS_ENV] = self::ALLOWED_ORIGIN;
        $_SERVER[self::ORIGINS_ENV] = self::ALLOWED_ORIGIN;

        parent::setUp();
        $this->setMcpEnabled(true);
    }

    protected function tearDown(): void
    {
        $original = $this->originalOrigins;
        parent::tearDown();

        if (null === $original) {
            return;
        }
        if (null === $original['env']) {
            unset($_ENV[self::ORIGINS_ENV]);
        } else {
            $_ENV[self::ORIGINS_ENV] = $original['env'];
        }
        if (null === $original['server']) {
            unset($_SERVER[self::ORIGINS_ENV]);
        } else {
            $_SERVER[self::ORIGINS_ENV] = $original['server'];
        }
    }

    public function testInitializeSucceedsWithNonLocalhostHost(): void
    {
        $this->initialize();

        $response = $this->client->getResponse();
        $body = (string) $response->getContent();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), 'localhost 以外の Host でも initialize が通る: '.$body);
        $this->assertNotEmpty($response->headers->get('Mcp-Session-Id'), 'セッションが発行される');

        $decoded = json_decode($body, true);
        $this->assertIsArray($decoded, $body);
        $this->assertArrayHasKey('result', $decoded, 'initialize の JSON-RPC result が返る (handshake 成立)');
    }

    public function testInitializeSucceedsWithAllowedOriginOnNonLocalhostHost(): void
    {
        $this->initialize(self::ALLOWED_ORIGIN);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), '許可リストにある Origin は localhost 以外でも通る: '.$response->getContent());
        $this->assertNotEmpty($response->headers->get('Mcp-Session-Id'));
    }

    public function testRejectsOriginNotInAllowListOnNonLocalhostHost(): void
    {
        // 有効な Bearer を付けても、 許可リストに無い Origin (= ブラウザ発) は EC-CUBE 側で拒否する。
        // SDK の DNS リバインディング対策は外しているため、 OriginContentTypeListener が外れると 200 になり失敗する。
        $this->initialize('https://attacker.example');

        $response = $this->client->getResponse();
        $body = (string) $response->getContent();
        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), $body);

        $decoded = json_decode($body, true);
        $this->assertIsArray($decoded, 'OriginContentTypeListener の JSON 応答で拒否される: '.$body);
        $this->assertSame('forbidden', $decoded['error'] ?? null);
        $this->assertSame('Origin not allowed.', $decoded['message'] ?? null);
    }

    private function initialize(?string $origin = null): void
    {
        $member = $this->createMember();
        $client = $this->ensureMcpClient(self::TEST_CLIENT_ID);
        $jwt = $this->issueMcpJwt('host-header-token-'.uniqid(), $client, $member);

        $server = [
            'HTTP_HOST' => self::HOST,
            'HTTPS' => 'on',
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json, text/event-stream',
            'HTTP_AUTHORIZATION' => 'Bearer '.$jwt,
        ];
        if (null !== $origin) {
            $server['HTTP_ORIGIN'] = $origin;
        }

        $this->client->request(
            Request::METHOD_POST,
            '/'.$this->getAdminRoute().'/mcp',
            server: $server,
            content: '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-03-26","clientInfo":{"name":"h","version":"1"},"capabilities":{}}}',
        );
    }

    private function getAdminRoute(): string
    {
        $route = static::getContainer()->getParameter('eccube_admin_route');
        \assert(\is_string($route));

        return $route;
    }
}
