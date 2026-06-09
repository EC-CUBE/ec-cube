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

use Eccube\Entity\Master\Work;
use Eccube\Entity\Member;
use Eccube\Tests\EccubeTestCase;
use League\Bundle\OAuth2ServerBundle\Entity\AccessToken as AccessTokenEntity;
use League\Bundle\OAuth2ServerBundle\Entity\Client as ClientEntity;
use League\Bundle\OAuth2ServerBundle\Manager\AccessTokenManagerInterface;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\AccessToken as AccessTokenModel;
use League\Bundle\OAuth2ServerBundle\Model\AccessTokenInterface;
use League\Bundle\OAuth2ServerBundle\Model\Client as ClientModel;
use League\Bundle\OAuth2ServerBundle\Model\ClientInterface;
use League\Bundle\OAuth2ServerBundle\ValueObject\Grant;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope as ScopeValue;
use League\OAuth2\Server\CryptKey;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 設計 §8 #6 (失効 → 即 401) と #9 (Member 無効化 → 即 401) の直接テスト。
 *
 * 自前で OAuth2 client + access_token を fixture として DB に保存し、 league の private key で JWT を発行
 * (= 通常の `/token` エンドポイント経由の応答と同等の Bearer)。 その JWT を Bearer として `/admin/mcp` に
 * 投げ:
 *   1. 正常状態 → firewall を通過し、 401 では**ない** ことを確認 (handshake は別途、 ここでは認証経路のみ確認)
 *   2. `AccessToken::revoke()` で revoke → 同じ JWT を投げると **401**
 *   3. 発行 Member を無効化 (`work_id = Work::HIDDEN`) → 同じ JWT を投げると **401**
 *
 * これにより league の `isAccessTokenRevoked` 経路と MemberProvider 経路がそれぞれ独立して fail-fast
 * することを担保する。
 */
final class McpTokenRevocationContractTest extends EccubeTestCase
{
    private const TEST_CLIENT_ID = 'mcp-revocation-test-client';

    // EccubeTestCase::tearDown() が全プロパティに null を強制代入するため nullable + default null が必須
    private ?ClientManagerInterface $clientManager = null;
    private ?AccessTokenManagerInterface $accessTokenManager = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientManager = static::getContainer()->get(ClientManagerInterface::class);
        $this->accessTokenManager = static::getContainer()->get(AccessTokenManagerInterface::class);
    }

    public function testValidJwtIsAcceptedByFirewall(): void
    {
        $member = $this->createMember();
        $client = $this->ensureClient();
        $jwt = $this->issueJwt('valid-token-'.uniqid(), $client, $member, revoked: false);

        $this->mcpRequest($jwt);

        $response = $this->client->getResponse();
        $body = (string) $response->getContent();
        // 200 + JSON-RPC result まで見ることで、 firewall を通過し initialize handshake が成立したことを確認する
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), $body);

        $decoded = json_decode($body, true);
        $this->assertIsArray($decoded, $body);
        $this->assertArrayHasKey('result', $decoded, 'initialize の JSON-RPC result が返る (handshake 成立)');
        $this->assertArrayNotHasKey('error', $decoded);
    }

    public function testRevokedAccessTokenReturns401(): void
    {
        $member = $this->createMember();
        $client = $this->ensureClient();
        $identifier = 'revoked-token-'.uniqid();
        $jwt = $this->issueJwt($identifier, $client, $member, revoked: false);

        // 同じ identifier を `revoked=true` で再保存 → league の AccessTokenRepository::isAccessTokenRevoked が true を返す
        $token = $this->accessTokenManager->find($identifier);
        $this->assertInstanceOf(AccessTokenInterface::class, $token);
        $token->revoke();
        $this->accessTokenManager->save($token);

        $this->mcpRequest($jwt);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode(), 'revoked token は 401');
    }

    public function testDisabledMemberReturns401(): void
    {
        $member = $this->createMember();
        $client = $this->ensureClient();
        $jwt = $this->issueJwt('disabled-member-token-'.uniqid(), $client, $member, revoked: false);

        // Member を「削除済」 (Work=HIDDEN) に変更 → MemberProvider でロードできない or UserChecker で reject
        $this->disableMember($member);

        $this->mcpRequest($jwt);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode(), '無効化された Member の token は 401');
    }

    private function mcpRequest(string $bearerJwt): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/'.$this->getAdminRoute().'/mcp',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$bearerJwt,
            ],
            content: '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-03-26","clientInfo":{"name":"r","version":"1"},"capabilities":{}}}',
        );
    }

    private function ensureClient(): ClientInterface
    {
        $existing = $this->clientManager->find(self::TEST_CLIENT_ID);
        if (null !== $existing) {
            return $existing;
        }

        $client = new ClientModel('MCP revocation test', self::TEST_CLIENT_ID, null);
        $client->setScopes(new ScopeValue('mcp:product:read'));
        $client->setGrants(new Grant('authorization_code'), new Grant('refresh_token'));
        $this->clientManager->save($client);

        return $client;
    }

    /**
     * AccessToken Model を保存し、 同じ identifier の JWT を発行して返す。
     */
    private function issueJwt(string $identifier, ClientInterface $client, Member $member, bool $revoked): string
    {
        $expiry = new \DateTimeImmutable('+1 hour');
        // MemberProvider::loadUserByIdentifier は login_id で検索する。 sub claim には login_id を入れる。
        $userIdentifier = $member->getUsername();

        // 1) Model を DB に保存 (league の isAccessTokenRevoked 照合先)
        $tokenModel = new AccessTokenModel($identifier, $expiry, $client, $userIdentifier, []);
        if ($revoked) {
            $tokenModel->revoke();
        }
        $this->accessTokenManager->save($tokenModel);

        // 2) Bearer 用の JWT を発行 (通常の /token エンドポイント経由の応答と同等)
        $tokenEntity = new AccessTokenEntity();
        $tokenEntity->setIdentifier($identifier);
        $tokenEntity->setExpiryDateTime($expiry);
        $tokenEntity->setUserIdentifier($userIdentifier);

        $clientEntity = new ClientEntity();
        $clientEntity->setIdentifier($client->getIdentifier());
        $tokenEntity->setClient($clientEntity);

        $privateKeyPath = static::getContainer()->getParameter('kernel.project_dir').'/app/PluginData/Api44/oauth/private.key';
        \assert(\is_string($privateKeyPath));
        // テスト環境では private key の permission チェックを無効化 (CI / docker 環境差異への対応)
        $tokenEntity->setPrivateKey(new CryptKey($privateKeyPath, null, keyPermissionsCheck: false));

        return $tokenEntity->toString();
    }

    private function disableMember(Member $member): void
    {
        // MemberProvider は Work::ACTIVE のみロードする。 Work::NON_ACTIVE にすると次回 loadUserByIdentifier で 401
        $workRepo = $this->entityManager->getRepository(Work::class);
        $nonActive = $workRepo->find(Work::NON_ACTIVE);
        $this->assertInstanceOf(Work::class, $nonActive);
        $member->setWork($nonActive);
        $this->entityManager->flush();
    }

    private function getAdminRoute(): string
    {
        $route = static::getContainer()->getParameter('eccube_admin_route');
        \assert(\is_string($route));

        return $route;
    }
}
