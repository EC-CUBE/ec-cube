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

use Eccube\Repository\BaseInfoRepository;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Layer 0 / Layer 3': ACP discovery (/.well-known/acp.json) の規範要件.
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.discovery.md §4 / §7.4
 */
final class AcpDiscoveryControllerTest extends EccubeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $baseInfo = self::getContainer()->get(BaseInfoRepository::class)->get();
        $baseInfo->setAcpCheckoutEnabled(true);
        $this->entityManager->flush();
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchDiscovery(): array
    {
        // MUST NOT require authentication: Authorization ヘッダ無しで取得する。
        $this->client->request(Request::METHOD_GET, '/.well-known/acp.json');

        return json_decode((string) $this->client->getResponse()->getContent(), true) ?? [];
    }

    public function testDiscoveryIsServedWithoutAuth(): void
    {
        $doc = $this->fetchDiscovery();

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), 'MUST NOT require authentication for /.well-known/acp.json');
        $this->assertSame('acp', $doc['protocol']['name'], 'MUST return protocol.name "acp"');
        $this->assertMatchesRegularExpression('/\A\d{4}-\d{2}-\d{2}\z/', $doc['protocol']['version'], 'MUST return protocol.version in YYYY-MM-DD');
        $this->assertNotEmpty($doc['protocol']['supported_versions'], 'MUST return non-empty supported_versions');
        $this->assertContains('rest', $doc['transports'], 'MUST include at least "rest" in transports');
        $this->assertContains('checkout', $doc['capabilities']['services'], 'MUST include checkout service');
        $this->assertNotEmpty($doc['api_base_url']);
    }

    public function testDiscoveryDoesNotExposeMerchantId(): void
    {
        $this->client->request(Request::METHOD_GET, '/.well-known/acp.json');
        $raw = (string) $this->client->getResponse()->getContent();

        // MUST NOT accept or return merchant_id or merchant-specific identifiers (列挙・指紋採取防止)。
        $this->assertStringNotContainsStringIgnoringCase('merchant_id', $raw, 'MUST NOT return merchant_id (rfc.discovery.md §7.4)');
    }

    public function testDiscoveryIsCacheable(): void
    {
        $this->client->request(Request::METHOD_GET, '/.well-known/acp.json');
        $cacheControl = (string) $this->client->getResponse()->headers->get('Cache-Control');

        // SHOULD include Cache-Control public, max-age >= 3600。
        $this->assertStringContainsString('public', $cacheControl, 'SHOULD be publicly cacheable');
        $this->assertStringContainsString('max-age=3600', $cacheControl);
    }

    public function testDiscoveryReturns404WhenDisabled(): void
    {
        $baseInfo = self::getContainer()->get(BaseInfoRepository::class)->get();
        $baseInfo->setAcpCheckoutEnabled(false);
        $this->entityManager->flush();

        $this->client->request(Request::METHOD_GET, '/.well-known/acp.json');
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), 'acp_checkout_enabled=false なら 404');
    }
}
