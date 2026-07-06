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

use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogCache;
use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Layer 3: UCP Catalog エンドポイント (pull / REST transport) の Web テスト.
 *
 * 一次仕様 (v2026-04-08) では search / lookup / product はいずれも POST の RPC 形式。
 * 検証観点:
 *   - UCP Catalog は常時公開 (フラグ無効化なし).
 *   - 正常系のレスポンス shape (ucp envelope + products / product, version 形式).
 *   - pagination (cursor / has_next_page).
 *   - gzip (Accept-Encoding: gzip -> Content-Encoding: gzip + Vary).
 *   - product の必須解決失敗は 404.
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/catalog_search.json
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/catalog_lookup.json
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/types/product.json
 */
final class UcpCatalogControllerTest extends AbstractWebTestCase
{
    /**
     * UCP バージョン (date-based, YYYY-MM-DD).
     */
    private const UCP_VERSION = '2026-04-08';

    protected function setUp(): void
    {
        parent::setUp();
        // UCP Catalog は常時公開。直前のテスト結果が漏れないようキャッシュをクリアする。
        static::getContainer()->get(UcpCatalogCache::class)->clear();
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJson(Response $response): array
    {
        $content = (string) $response->getContent();
        if (str_contains((string) $response->headers->get('Content-Encoding', ''), 'gzip')) {
            $decompressed = gzdecode($content);
            $this->assertNotFalse($decompressed, 'gzip response body must be decodable');
            $content = $decompressed;
        }
        $decoded = json_decode($content, true);
        $this->assertIsArray($decoded, 'UCP Catalog response body must be a JSON object');

        return $decoded;
    }

    // --- HTTP メソッド制約 -------------------------------------------------

    public function testSearchRejectsGet(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('agent_ucp_catalog_search'));

        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode(), 'UCP Catalog search is an RPC POST endpoint; GET must not be allowed');
    }

    // --- search 正常系 -----------------------------------------------------

    public function testSearchReturnsUcpEnvelopeWithProducts(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_search'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode([])
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), 'search 正常系は HTTP 200');
        $this->assertStringContainsString('application/json', (string) $response->headers->get('Content-Type'), 'UCP Catalog responses are JSON');

        $data = $this->decodeJson($response);

        $this->assertArrayHasKey('ucp', $data, 'search response MUST carry the required "ucp" envelope');
        $this->assertSame(self::UCP_VERSION, $data['ucp']['version'] ?? null, 'ucp.version MUST be the date-based protocol version (v2026-04-08)');
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', (string) ($data['ucp']['version'] ?? ''), 'ucp.version MUST be a YYYY-MM-DD date string');
        $this->assertArrayHasKey('products', $data, 'search response MUST carry the required "products" array');
        $this->assertIsArray($data['products']);
        $this->assertNotEmpty($data['products'], 'default fixtures should yield at least one display product');
    }

    public function testSearchProductShapeHasRequiredFields(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_search'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode([])
        );

        $data = $this->decodeJson($this->client->getResponse());
        $product = $data['products'][0];

        foreach (['id', 'title', 'description', 'price_range', 'variants'] as $required) {
            $this->assertArrayHasKey($required, $product, sprintf('UCP Product MUST define the required field "%s"', $required));
        }
        $this->assertNotEmpty($product['variants'], 'UCP Product.variants MUST be non-empty');

        $variant = $product['variants'][0];
        foreach (['id', 'title', 'description', 'price'] as $required) {
            $this->assertArrayHasKey($required, $variant, sprintf('UCP Variant MUST define the required field "%s"', $required));
        }
        $this->assertArrayHasKey('amount', $variant['price'], 'Variant.price MUST carry a minor-unit amount');
        $this->assertIsInt($variant['price']['amount'], 'Variant.price.amount MUST be a minor-unit integer');
        $this->assertArrayHasKey('currency', $variant['price'], 'Variant.price MUST carry an ISO 4217 currency');
    }

    public function testSearchQueryFiltersByProductName(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_search'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode(['query' => 'ZZZ_NO_SUCH_PRODUCT_NAME_ZZZ'])
        );

        $data = $this->decodeJson($this->client->getResponse());
        $this->assertSame([], $data['products'], 'A query matching no product name must return an empty products array');
    }

    // --- pagination --------------------------------------------------------

    public function testSearchPaginationReturnsCursorAndHasNextPage(): void
    {
        // limit=1 で全件 (>1 件) のうち 1 件だけ返り has_next_page=true となること.
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_search'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode(['pagination' => ['limit' => 1]])
        );

        $data = $this->decodeJson($this->client->getResponse());

        $this->assertCount(1, $data['products'], 'pagination.limit=1 must return exactly one product');
        $this->assertArrayHasKey('pagination', $data, 'search response should carry pagination metadata');
        $this->assertTrue($data['pagination']['has_next_page'] ?? false, 'With more products than the limit, has_next_page must be true');
        $this->assertArrayHasKey('cursor', $data['pagination'], 'A next page must expose an opaque cursor');

        $firstId = $data['products'][0]['id'];
        $cursor = $data['pagination']['cursor'];

        // 2 ページ目を cursor で取得し、別の商品 (id が異なる) が返ること.
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_search'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode(['pagination' => ['limit' => 1, 'cursor' => $cursor]])
        );

        $page2 = $this->decodeJson($this->client->getResponse());
        $this->assertCount(1, $page2['products'], 'second page with limit=1 must return one product');
        $this->assertNotSame($firstId, $page2['products'][0]['id'], 'The cursor must advance to a different product (opaque offset cursor)');
    }

    // --- gzip --------------------------------------------------------------

    public function testSearchGzipWhenAcceptEncodingGzip(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_search'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT_ENCODING' => 'gzip'],
            (string) json_encode([])
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), (string) $response->getContent());
        $this->assertSame('gzip', $response->headers->get('Content-Encoding'), 'When the client advertises Accept-Encoding: gzip the response MUST be gzip-encoded');
        $this->assertStringContainsString('Accept-Encoding', (string) $response->headers->get('Vary'), 'A gzip-negotiated response MUST advertise Vary: Accept-Encoding');

        $decompressed = gzdecode((string) $response->getContent());
        $this->assertNotFalse($decompressed, 'gzip body must be decodable');
        $decoded = json_decode($decompressed, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('ucp', $decoded, 'decoded gzip body must still be a valid UCP response');
    }

    public function testSearchNotGzippedWithoutAcceptEncoding(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_search'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode([])
        );

        $response = $this->client->getResponse();
        $this->assertNull($response->headers->get('Content-Encoding'), 'Without Accept-Encoding: gzip the response MUST NOT be gzip-encoded');
    }

    // --- lookup ------------------------------------------------------------

    public function testLookupResolvesProductById(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_lookup'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode(['ids' => ['1']])
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), 'lookup 正常系は HTTP 200');

        $data = $this->decodeJson($response);
        $this->assertArrayHasKey('ucp', $data, 'lookup response MUST carry the "ucp" envelope');
        $this->assertArrayHasKey('products', $data, 'lookup response MUST carry the "products" array');
        $this->assertCount(1, $data['products'], 'lookup of one product id must resolve to one product');
        $this->assertSame('1', (string) $data['products'][0]['id'], 'lookup must resolve the requested product id');
    }

    public function testLookupDeduplicatesProducts(): void
    {
        // 同一 product を product id と (おそらく) 別表現で 2 回指定しても 1 件に集約される.
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_lookup'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode(['ids' => ['1', '1']])
        );

        $data = $this->decodeJson($this->client->getResponse());
        $this->assertCount(1, $data['products'], 'Duplicate ids for the same product MUST be deduplicated');
    }

    public function testLookupUnknownIdReturnsEmptyProducts(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_lookup'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode(['ids' => ['99999999']])
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), 'lookup of unknown ids stays HTTP 200');
        $data = $this->decodeJson($response);
        $this->assertSame([], $data['products'], 'Unresolvable ids must yield an empty products array (not an error)');
    }

    // --- product -----------------------------------------------------------

    public function testProductReturnsSingleProduct(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_product'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode(['id' => '1'])
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode(), 'product 正常系は HTTP 200');

        $data = $this->decodeJson($response);
        $this->assertArrayHasKey('ucp', $data, 'product response MUST carry the "ucp" envelope');
        $this->assertArrayHasKey('product', $data, 'get_product response MUST carry a single "product" (not products[])');
        $this->assertArrayNotHasKey('products', $data, 'get_product response MUST NOT return a products[] array');
        $this->assertSame('1', (string) $data['product']['id'], 'product must resolve the requested id');
    }

    public function testProductMissingIdReturns404(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_product'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode([])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), 'get_product without a required id must return 404');
    }

    public function testProductUnknownIdReturns404(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('agent_ucp_catalog_product'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode(['id' => '99999999'])
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), 'get_product for an unknown id must return 404 (product is required; no empty 200)');
    }
}
