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
use League\Bundle\OAuth2ServerBundle\Entity\AccessToken as AccessTokenEntity;
use League\Bundle\OAuth2ServerBundle\Entity\Client as ClientEntity;
use League\Bundle\OAuth2ServerBundle\Entity\Scope as ScopeEntity;
use League\Bundle\OAuth2ServerBundle\Manager\AccessTokenManagerInterface;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\AccessToken as AccessTokenModel;
use League\Bundle\OAuth2ServerBundle\Model\Client as ClientModel;
use League\Bundle\OAuth2ServerBundle\Model\ClientInterface;
use League\Bundle\OAuth2ServerBundle\ValueObject\Grant;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope as ScopeValue;
use League\OAuth2\Server\CryptKey;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * scope 強制が「実カーネル → firewall → mcp-bundle → ScopeEnforcingReferenceHandler → Tool」 の経路全体で
 * 効くことを検証する結合テスト (案 A の核心の回帰テスト)。
 *
 * 配線が外れる (McpScopeEnforcementPass 削除 / builder 構造変更 / mcp-bundle 更新) と本テストが落ちる。
 * scope 付き JWT を自前発行し、 initialize → notifications/initialized → tools/call の handshake を
 * 実カーネルに流して、 scope 充足は result、 不足は isError:true を確認する。
 */
final class McpScopeEnforcementIntegrationTest extends EccubeTestCase
{
    private const TEST_CLIENT_ID = 'mcp-scope-it-client';

    private ?ClientManagerInterface $clientManager = null;
    private ?AccessTokenManagerInterface $accessTokenManager = null;

    protected function setUp(): void
    {
        // 実 JWT 発行と firewall を要する結合テスト。 Api44 (league/oauth2-server-bundle) 未導入の
        // 環境では league クラスが autoload できず fatal になるため、 存在しなければスキップする。
        if (!interface_exists(ClientManagerInterface::class)) {
            $this->markTestSkipped('Api44 (league/oauth2-server-bundle) がインストールされていません');
        }
        parent::setUp();
        $this->clientManager = static::getContainer()->get(ClientManagerInterface::class);
        $this->accessTokenManager = static::getContainer()->get(AccessTokenManagerInterface::class);
    }

    public function testToolCallSucceedsWhenScopeGranted(): void
    {
        $jwt = $this->issueScopedJwt(['mcp:product:read']);

        $result = $this->callTool($jwt, 'search_products', ['limit' => 1]);

        $this->assertArrayHasKey('result', $result, (string) json_encode($result));
        $this->assertNotTrue($result['result']['isError'] ?? false, 'scope 充足の tool は isError にならない');
        $this->assertArrayHasKey('structuredContent', $result['result']);
    }

    public function testToolCallDeniedWhenScopeMissing(): void
    {
        // product scope のみの token で order tool を呼ぶ
        $jwt = $this->issueScopedJwt(['mcp:product:read']);

        $result = $this->callTool($jwt, 'search_orders', ['limit' => 1]);

        $this->assertTrue($result['result']['isError'] ?? false, 'scope 不足の tool は isError:true');
        $text = $result['result']['content'][0]['text'] ?? '';
        $this->assertStringContainsString('Insufficient scope: mcp:order:read', (string) $text);
    }

    /**
     * initialize → notifications/initialized → tools/call の handshake を実カーネルに流し、
     * tools/call の JSON-RPC レスポンス (デコード済み) を返す。
     *
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    private function callTool(string $jwt, string $toolName, array $arguments): array
    {
        $path = '/'.$this->getAdminRoute().'/mcp';
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json, text/event-stream',
            'HTTP_AUTHORIZATION' => 'Bearer '.$jwt,
        ];

        $this->client->request(Request::METHOD_POST, $path, server: $headers, content: (string) json_encode([
            'jsonrpc' => '2.0', 'id' => 1, 'method' => 'initialize',
            'params' => ['protocolVersion' => '2025-03-26', 'clientInfo' => ['name' => 'it', 'version' => '1'], 'capabilities' => []],
        ]));
        $initResponse = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $initResponse->getStatusCode(), (string) $initResponse->getContent());
        $sessionId = $initResponse->headers->get('mcp-session-id');
        $this->assertNotNull($sessionId, 'initialize で Mcp-Session-Id が返る');

        $headers['HTTP_MCP_SESSION_ID'] = $sessionId;
        $this->client->request(Request::METHOD_POST, $path, server: $headers, content: (string) json_encode([
            'jsonrpc' => '2.0', 'method' => 'notifications/initialized',
        ]));

        $this->client->request(Request::METHOD_POST, $path, server: $headers, content: (string) json_encode([
            'jsonrpc' => '2.0', 'id' => 2, 'method' => 'tools/call',
            'params' => ['name' => $toolName, 'arguments' => $arguments],
        ]));

        return $this->decodeJsonRpc((string) $this->client->getResponse()->getContent());
    }

    /**
     * JSON または SSE (data: 行) のどちらでも JSON-RPC ボディをデコードする。
     *
     * @return array<string, mixed>
     */
    private function decodeJsonRpc(string $body): array
    {
        foreach (explode("\n", $body) as $line) {
            $line = str_starts_with($line, 'data: ') ? substr($line, 6) : $line;
            $line = trim($line);
            if ('' === $line) {
                continue;
            }
            $decoded = json_decode($line, true);
            if (\is_array($decoded) && isset($decoded['jsonrpc'])) {
                return $decoded;
            }
        }

        $this->fail('JSON-RPC レスポンスをデコードできなかった: '.$body);
    }

    /**
     * 指定 scope を claim に持つ JWT を発行する (revoked-check 用の AccessToken Model も保存)。
     *
     * @param list<string> $scopes
     */
    private function issueScopedJwt(array $scopes): string
    {
        $member = $this->createMember();
        $client = $this->ensureClient();
        $identifier = 'mcp-scope-it-'.uniqid();
        $expiry = new \DateTimeImmutable('+1 hour');
        $userIdentifier = $member->getUsername();

        // revoked 照合用に Model を保存 (scope は JWT claim 側で表現するので Model は空で可)
        $this->accessTokenManager->save(new AccessTokenModel($identifier, $expiry, $client, $userIdentifier, []));

        $tokenEntity = new AccessTokenEntity();
        $tokenEntity->setIdentifier($identifier);
        $tokenEntity->setExpiryDateTime($expiry);
        $tokenEntity->setUserIdentifier($userIdentifier);
        $clientEntity = new ClientEntity();
        $clientEntity->setIdentifier($client->getIdentifier());
        $tokenEntity->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $scopeEntity = new ScopeEntity();
            $scopeEntity->setIdentifier($scope);
            $tokenEntity->addScope($scopeEntity);
        }

        $privateKeyPath = static::getContainer()->getParameter('kernel.project_dir').'/app/PluginData/Api44/oauth/private.key';
        \assert(\is_string($privateKeyPath));
        $tokenEntity->setPrivateKey(new CryptKey($privateKeyPath, null, keyPermissionsCheck: false));

        return $tokenEntity->toString();
    }

    private function ensureClient(): ClientInterface
    {
        $existing = $this->clientManager->find(self::TEST_CLIENT_ID);
        if (null !== $existing) {
            return $existing;
        }

        $client = new ClientModel('MCP scope integration test', self::TEST_CLIENT_ID, null);
        $client->setScopes(new ScopeValue('mcp:product:read'), new ScopeValue('mcp:order:read'));
        $client->setGrants(new Grant('authorization_code'), new Grant('refresh_token'));
        $this->clientManager->save($client);

        return $client;
    }

    private function getAdminRoute(): string
    {
        $route = static::getContainer()->getParameter('eccube_admin_route');
        \assert(\is_string($route));

        return $route;
    }
}
