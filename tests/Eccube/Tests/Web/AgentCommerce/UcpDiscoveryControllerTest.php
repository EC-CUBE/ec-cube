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

namespace Eccube\Tests\Web\AgentCommerce;

use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Layer 3': UCP discovery profile (/.well-known/ucp) の Web テスト.
 *
 * 一次仕様 (v2026-04-08, schemas/profile.json) の規範要件を検証する:
 *   - ラッパー = { "ucp": {...} (必須), "signing_keys": [JWK] (任意) }.
 *   - ucp.version は "YYYY-MM-DD" (= 2026-04-08), services / payment_handlers は object 必須.
 *   - services / capabilities / payment_handlers のキーは reverse-domain.
 *   - discovery は常時公開 (フラグ無効化なし)・catalog capability / service を常時宣言する.
 *   - signing_keys[] は EC 公開鍵 JWK のみ (秘密鍵パラメータ d/p/q/dp/dq/qi/oth/k 非混入).
 *   - 配信: Cache-Control public, max-age >= 60 (no-store/no-cache/private 禁止), HTTPS 想定・3xx 禁止.
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/profile.json
 */
final class UcpDiscoveryControllerTest extends AbstractWebTestCase
{
    /**
     * UCP バージョン (date-based, YYYY-MM-DD).
     */
    private const UCP_VERSION = '2026-04-08';

    /**
     * reverse-domain キーを表す正規表現 (profile.json の propertyNames pattern).
     */
    private const REVERSE_DOMAIN_PATTERN = '/^[a-z][a-z0-9]*(?:\.[a-z][a-z0-9_]*)+$/';

    /**
     * JWK で公開鍵に出現してはならない秘密鍵パラメータ.
     *
     * @var string[]
     */
    private const PRIVATE_JWK_PARAMS = ['d', 'p', 'q', 'dp', 'dq', 'qi', 'oth', 'k'];

    /**
     * @return array<string, mixed>
     */
    private function requestProfile(): array
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('agent_ucp_discovery'));
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), 'discovery 正常系は HTTP 200');

        $decoded = json_decode((string) $response->getContent(), true);
        $this->assertIsArray($decoded, 'discovery profile MUST be a JSON object');

        return $decoded;
    }

    public function testRejectsPost(): void
    {
        $this->client->request(Request::METHOD_POST, $this->generateUrl('agent_ucp_discovery'));

        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode(), 'The discovery document is served via GET only; POST must not be allowed');
    }

    // --- ラッパー / version ------------------------------------------------

    public function testProfileHasUcpWrapper(): void
    {
        $profile = $this->requestProfile();

        $this->assertArrayHasKey('ucp', $profile, 'The profile MUST contain the required top-level "ucp" object');
        $this->assertIsArray($profile['ucp']);
    }

    public function testUcpVersionIsDateBased(): void
    {
        $profile = $this->requestProfile();

        $this->assertArrayHasKey('version', $profile['ucp'], 'ucp.version is REQUIRED');
        $this->assertSame(self::UCP_VERSION, $profile['ucp']['version'], 'ucp.version MUST be the protocol version v2026-04-08');
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $profile['ucp']['version'], 'ucp.version MUST be a "YYYY-MM-DD" date string');
    }

    public function testServicesAndPaymentHandlersAreObjects(): void
    {
        // JSON_FORCE_OBJECT に頼らず {} を出力できているかを raw 文字列で確認する.
        $this->client->request(Request::METHOD_GET, $this->generateUrl('agent_ucp_discovery'));
        $raw = (string) $this->client->getResponse()->getContent();

        $profile = json_decode($raw, true);
        $this->assertArrayHasKey('services', $profile['ucp'], 'ucp.services is REQUIRED');
        $this->assertArrayHasKey('payment_handlers', $profile['ucp'], 'ucp.payment_handlers is REQUIRED');

        // 配列(空)であっても JSON 上は {} でなければならない (object 必須).
        $this->assertStringNotContainsString('"payment_handlers":[]', $raw, 'ucp.payment_handlers MUST serialize as a JSON object {} (not an array []) even when empty');
    }

    // --- Catalog capability 宣言 -------------------------------------------

    public function testCatalogCapabilityAndServiceAlwaysDeclared(): void
    {
        $profile = $this->requestProfile();

        $this->assertArrayHasKey('capabilities', $profile['ucp']);
        $this->assertArrayHasKey('dev.ucp.shopping.catalog.search', $profile['ucp']['capabilities'], 'When Catalog API is enabled the search capability MUST be declared');
        $this->assertArrayHasKey('dev.ucp.shopping.catalog.lookup', $profile['ucp']['capabilities'], 'When Catalog API is enabled the lookup capability MUST be declared');

        $this->assertArrayHasKey('dev.ucp.shopping.catalog', $profile['ucp']['services'], 'When Catalog API is enabled the catalog REST service MUST be declared');
        $service = $profile['ucp']['services']['dev.ucp.shopping.catalog'];
        $this->assertSame('rest', $service['transport'], 'A REST service MUST declare transport "rest"');
        $this->assertArrayHasKey('endpoint', $service, 'A non-embedded service MUST declare an endpoint');
        $this->assertMatchesRegularExpression('#^https?://#', (string) $service['endpoint'], 'The service endpoint MUST be an absolute URL (RequestContext-derived, not a hardcoded path)');
    }

    // --- reverse-domain キー -----------------------------------------------

    public function testRegistryKeysAreReverseDomain(): void
    {
        $profile = $this->requestProfile();

        foreach (['services', 'capabilities', 'payment_handlers'] as $registry) {
            $entries = $profile['ucp'][$registry] ?? [];
            if (!is_array($entries)) {
                continue;
            }
            foreach (array_keys($entries) as $key) {
                $this->assertMatchesRegularExpression(self::REVERSE_DOMAIN_PATTERN, (string) $key, sprintf('Key "%s" in ucp.%s MUST be a reverse-domain identifier', $key, $registry));
            }
        }
    }

    // --- signing_keys ------------------------------------------------------

    public function testSigningKeysAreEcPublicJwks(): void
    {
        $profile = $this->requestProfile();

        $this->assertArrayHasKey('signing_keys', $profile, 'A merchant with a signing key MUST advertise it in signing_keys[] (key auto-generated on enable)');
        $this->assertNotEmpty($profile['signing_keys']);

        foreach ($profile['signing_keys'] as $jwk) {
            $this->assertSame('EC', $jwk['kty'] ?? null, 'UCP signing keys MUST be EC keys (kty:"EC")');
            $this->assertContains($jwk['crv'] ?? null, ['P-256', 'P-384'], 'UCP signing key curve MUST be P-256 or P-384');
            $this->assertArrayHasKey('kid', $jwk, 'A signing JWK MUST carry a kid');
            $this->assertArrayHasKey('x', $jwk, 'An EC public JWK MUST carry the x coordinate');
            $this->assertArrayHasKey('y', $jwk, 'An EC public JWK MUST carry the y coordinate');
        }
    }

    public function testSigningKeysDoNotLeakPrivateParameters(): void
    {
        $profile = $this->requestProfile();

        $this->assertArrayHasKey('signing_keys', $profile);
        foreach ($profile['signing_keys'] as $jwk) {
            foreach (self::PRIVATE_JWK_PARAMS as $param) {
                $this->assertArrayNotHasKey($param, $jwk, sprintf('signing_keys[] is a public advertisement; the private JWK parameter "%s" MUST NOT appear', $param));
            }
        }
    }

    // --- 配信ヘッダ --------------------------------------------------------

    public function testCacheControlIsPublicWithMinimumMaxAge(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('agent_ucp_discovery'));
        $response = $this->client->getResponse();

        $cacheControl = (string) $response->headers->get('Cache-Control');

        $this->assertStringContainsString('public', $cacheControl, 'The discovery document MUST be served with Cache-Control: public');

        $this->assertMatchesRegularExpression('/max-age=(\d+)/', $cacheControl, 'The discovery document MUST declare a max-age');
        preg_match('/max-age=(\d+)/', $cacheControl, $m);
        $this->assertGreaterThanOrEqual(60, (int) $m[1], 'Cache-Control max-age MUST be >= 60 seconds');

        foreach (['no-store', 'no-cache', 'private'] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $cacheControl, sprintf('The discovery document MUST NOT use Cache-Control "%s"', $forbidden));
        }
    }

    public function testServedAsJson(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('agent_ucp_discovery'));
        $response = $this->client->getResponse();

        $this->assertStringContainsString('application/json', (string) $response->headers->get('Content-Type'), 'The discovery document MUST be served as application/json');
        $this->assertFalse($response->isRedirection(), 'The discovery document MUST be served directly (no 3xx redirect)');
    }
}
