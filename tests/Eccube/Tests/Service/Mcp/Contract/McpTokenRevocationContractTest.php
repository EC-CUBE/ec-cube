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
use Eccube\Tests\Service\Mcp\EnablesMcpTrait;
use Eccube\Tests\Service\Mcp\IssuesMcpAccessTokenTrait;
use League\Bundle\OAuth2ServerBundle\Manager\AccessTokenManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\AccessTokenInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 設計 §8 #6 (失効 → 即 401) と #9 (Member 無効化 → 即 401) の直接テスト。
 *
 * 自前で OAuth2 client + access_token を fixture として DB に保存し、 league の private key で JWT を発行
 * (= 通常の `/token` エンドポイント経由の応答と同等の Bearer。 `IssuesMcpAccessTokenTrait`)。 その JWT を
 * Bearer として `/admin/mcp` に投げ:
 *   1. 正常状態 → firewall を通過し、 401 では**ない** ことを確認 (handshake は別途、 ここでは認証経路のみ確認)
 *   2. `AccessToken::revoke()` で revoke → 同じ JWT を投げると **401**
 *   3. 発行 Member を無効化 (`work_id = Work::HIDDEN`) → 同じ JWT を投げると **401**
 *
 * これにより league の `isAccessTokenRevoked` 経路と MemberProvider 経路がそれぞれ独立して fail-fast
 * することを担保する。
 */
#[Group('mcp')]
final class McpTokenRevocationContractTest extends EccubeTestCase
{
    use EnablesMcpTrait;
    use IssuesMcpAccessTokenTrait;

    private const TEST_CLIENT_ID = 'mcp-revocation-test-client';

    // EccubeTestCase::tearDown() が全プロパティに null を強制代入するため nullable + default null が必須
    private ?AccessTokenManagerInterface $accessTokenManager = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accessTokenManager = static::getContainer()->get(AccessTokenManagerInterface::class);
        $this->setMcpEnabled(true);
    }

    public function testValidJwtIsAcceptedByFirewall(): void
    {
        $member = $this->createMember();
        $client = $this->ensureMcpClient(self::TEST_CLIENT_ID);
        $jwt = $this->issueMcpJwt('valid-token-'.uniqid(), $client, $member);

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
        $client = $this->ensureMcpClient(self::TEST_CLIENT_ID);
        $identifier = 'revoked-token-'.uniqid();
        $jwt = $this->issueMcpJwt($identifier, $client, $member);

        // 同じ identifier を `revoked=true` で再保存 → league の AccessTokenRepository::isAccessTokenRevoked が true を返す
        $token = $this->accessTokenManager->find($identifier);
        $this->assertInstanceOf(AccessTokenInterface::class, $token);
        $token->revoke();
        $this->accessTokenManager->save($token);

        $this->mcpRequest($jwt);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), 'revoked token は 401');
        // WWW-Authenticate が Bearer で始まることで「oauth2 の bearer 拒否」を確認し、 無関係な 401 (誤ルーティング等) を排除する。
        // discovery の entry_point (McpAuthenticationEntryPoint) が 401 に resource_metadata を付ける経路があるため、 先頭一致で見る。
        $this->assertStringStartsWith('Bearer', (string) $response->headers->get('WWW-Authenticate'));
    }

    public function testDisabledMemberReturns401(): void
    {
        $member = $this->createMember();
        $client = $this->ensureMcpClient(self::TEST_CLIENT_ID);
        $jwt = $this->issueMcpJwt('disabled-member-token-'.uniqid(), $client, $member);

        // Member を「削除済」 (Work=HIDDEN) に変更 → MemberProvider でロードできない or UserChecker で reject
        $this->disableMember($member);

        $this->mcpRequest($jwt);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), '無効化された Member の token は 401');
        $this->assertStringStartsWith('Bearer', (string) $response->headers->get('WWW-Authenticate'));
        // token 検証は通り user 解決 (MemberProvider) で失敗する経路。 token 拒否 (revoke / 署名不正) とは
        // body が分かれる (Symfony Security の "Bad credentials")。 これで Member 無効化の経路を識別する
        $this->assertStringContainsString('Bad credentials', (string) $response->getContent());
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
