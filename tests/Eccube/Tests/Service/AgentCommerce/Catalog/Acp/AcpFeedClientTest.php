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

namespace Eccube\Tests\Service\AgentCommerce\Catalog\Acp;

use Eccube\Service\AgentCommerce\Catalog\Acp\AcpFeedClient;
use Eccube\Service\AgentCommerce\Catalog\Acp\AcpFeedGenerator;
use Eccube\Service\AgentCommerce\Catalog\Acp\AcpFeedValidator;
use Eccube\Service\AgentCommerce\Catalog\Exception\AcpFeedException;
use Eccube\Service\AgentCommerce\Catalog\Exception\AcpFeedTransportException;
use Eccube\Service\AgentCommerce\Catalog\Exception\AcpFeedValidationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Layer 5 tests for the ACP Product Feed push client (DB-less, MockHttpClient).
 *
 * Exercises the outbound contract against the OpenAI-hosted ACP Feed API:
 * createFeed / getFeed / getFeedProducts / upsertProducts, outbound Bearer
 * authentication, full-replacement vs. incremental upsert, pre-push schema
 * validation, ACP flat Error parsing, the accepted:bool ack, and the guarantee
 * that the Bearer api_key never leaks into exception messages.
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/openapi/openapi.feed.yaml
 */
final class AcpFeedClientTest extends TestCase
{
    private const API_KEY = 'sk_secret_feed_apikey_DO_NOT_LEAK';

    private const BASE_URL = 'https://feed.example.com/v1';

    /**
     * @var list<array{method: string, url: string, options: array<string, mixed>}>
     */
    private array $recorded = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->recorded = [];
    }

    protected function tearDown(): void
    {
        $this->recorded = [];
        parent::tearDown();
    }

    public function testCreateFeedPostsToFeedsEndpointAndReturnsMetadata(): void
    {
        $client = $this->createClient([
            $this->jsonResponse(201, ['id' => 'feed_8f3K2x', 'target_country' => 'US']),
        ]);

        $metadata = $client->createFeed('US');

        $this->assertSame('feed_8f3K2x', $metadata['id']);
        $this->assertCount(1, $this->recorded);
        $this->assertSame('POST', $this->recorded[0]['method'], 'createFeed MUST POST to /feeds.');
        $this->assertSame(self::BASE_URL.'/feeds', $this->recorded[0]['url']);
        $this->assertSame(['target_country' => 'US'], $this->decodeBody($this->recorded[0]));
    }

    public function testCreateFeedOmitsTargetCountryWhenNull(): void
    {
        $client = $this->createClient([
            $this->jsonResponse(201, ['id' => 'feed_1']),
        ]);

        $client->createFeed();

        $this->assertSame([], $this->decodeBody($this->recorded[0]), 'createFeed without a target country MUST send an empty body.');
    }

    public function testGetFeedGetsMetadataById(): void
    {
        $client = $this->createClient([
            $this->jsonResponse(200, ['id' => 'feed_abc', 'updated_at' => '2026-03-01T00:00:00Z']),
        ]);

        $metadata = $client->getFeed('feed abc');

        $this->assertSame('feed_abc', $metadata['id']);
        $this->assertSame('GET', $this->recorded[0]['method']);
        $this->assertSame(self::BASE_URL.'/feeds/feed%20abc', $this->recorded[0]['url'], 'getFeed MUST GET /feeds/{id} with the id rawurlencoded.');
    }

    public function testGetFeedProductsReturnsTheProductsArray(): void
    {
        $products = [
            ['id' => 'p1', 'variants' => [['id' => 'v1', 'title' => 'V1']]],
            ['id' => 'p2', 'variants' => [['id' => 'v2', 'title' => 'V2']]],
        ];
        $client = $this->createClient([
            $this->jsonResponse(200, ['products' => $products]),
        ]);

        $result = $client->getFeedProducts('feed_1');

        $this->assertSame($products, $result);
        $this->assertSame('GET', $this->recorded[0]['method']);
        $this->assertSame(self::BASE_URL.'/feeds/feed_1/products', $this->recorded[0]['url']);
    }

    public function testGetFeedProductsThrowsWhenProductsArrayMissing(): void
    {
        $client = $this->createClient([
            $this->jsonResponse(200, ['unexpected' => true]),
        ]);

        $this->expectException(AcpFeedTransportException::class);

        $client->getFeedProducts('feed_1');
    }

    public function testUpsertProductsPatchesProductsAndReturnsAcceptedTrue(): void
    {
        $products = [$this->validProduct('p1')];
        $client = $this->createClient([
            $this->jsonResponse(200, ['id' => 'feed_1', 'accepted' => true]),
        ]);

        $accepted = $client->upsertProducts('feed_1', $products);

        $this->assertTrue($accepted, 'upsertProducts MUST return the server accepted flag.');
        $this->assertSame('PATCH', $this->recorded[0]['method'], 'incremental upsert MUST PATCH /feeds/{id}/products.');
        $this->assertSame(self::BASE_URL.'/feeds/feed_1/products', $this->recorded[0]['url']);
        $this->assertSame(['products' => $products], $this->decodeBody($this->recorded[0]));
    }

    public function testUpsertProductsReturnsFalseWhenAcceptedFalse(): void
    {
        $client = $this->createClient([
            $this->jsonResponse(200, ['accepted' => false]),
        ]);

        $this->assertFalse($client->upsertProducts('feed_1', [$this->validProduct('p1')]));
    }

    public function testUpsertProductsReturnsFalseWhenAcceptedAbsent(): void
    {
        $client = $this->createClient([
            $this->jsonResponse(200, ['id' => 'feed_1']),
        ]);

        $this->assertFalse($client->upsertProducts('feed_1', [$this->validProduct('p1')]), 'A missing accepted flag MUST be treated as not accepted.');
    }

    public function testUpsertProductsRejectsInvalidPayloadBeforeSending(): void
    {
        // Product missing the required "variants" array -> must be rejected client-side.
        $invalid = ['id' => 'p_invalid'];
        $client = $this->createClient([
            $this->jsonResponse(200, ['accepted' => true]),
        ]);

        try {
            $client->upsertProducts('feed_1', [$invalid]);
            self::fail('upsertProducts MUST reject a schema-invalid product before sending.');
        } catch (AcpFeedValidationException $e) {
            $this->assertNotEmpty($e->getViolations());
        }

        $this->assertCount(0, $this->recorded, 'No outbound request MUST be made when pre-push validation fails.');
    }

    public function testCreateFeedParsesFlatErrorShape(): void
    {
        $client = $this->createClient([
            $this->jsonResponse(400, [
                'type' => 'invalid_request',
                'code' => 'bad_target_country',
                'message' => 'target_country is invalid',
                'param' => 'target_country',
            ]),
        ]);

        try {
            $client->createFeed('US');
            self::fail('A 4xx response MUST raise an AcpFeedTransportException.');
        } catch (AcpFeedTransportException $e) {
            $this->assertSame(400, $e->getStatusCode());
            $this->assertSame('invalid_request', $e->getErrorType());
            $this->assertSame('bad_target_country', $e->getErrorCode());
            $this->assertSame('target_country', $e->getParam());
            $this->assertSame('target_country is invalid', $e->getMessage());
        }
    }

    public function testParsesErrorWrappedInErrorEnvelope(): void
    {
        $client = $this->createClient([
            $this->jsonResponse(500, [
                'error' => [
                    'type' => 'server_error',
                    'code' => 'internal',
                    'message' => 'boom',
                ],
            ]),
        ]);

        try {
            $client->getFeed('feed_1');
            self::fail('A 5xx response MUST raise an AcpFeedTransportException.');
        } catch (AcpFeedTransportException $e) {
            $this->assertSame(500, $e->getStatusCode());
            $this->assertSame('server_error', $e->getErrorType());
            $this->assertSame('internal', $e->getErrorCode());
            $this->assertSame('boom', $e->getMessage());
        }
    }

    public function testErrorWithoutMessageFallsBackToStatusSummary(): void
    {
        $client = $this->createClient([
            $this->jsonResponse(404, []),
        ]);

        try {
            $client->getFeed('missing');
            self::fail('A 404 MUST raise an AcpFeedTransportException.');
        } catch (AcpFeedTransportException $e) {
            $this->assertSame(404, $e->getStatusCode());
            $this->assertStringContainsString('404', $e->getMessage());
            $this->assertNull($e->getErrorType());
        }
    }

    public function testAllOutboundRequestsCarryBearerAuthorizationHeader(): void
    {
        $client = $this->createClient([
            $this->jsonResponse(201, ['id' => 'feed_1']),
            $this->jsonResponse(200, ['id' => 'feed_1']),
            $this->jsonResponse(200, ['products' => []]),
            $this->jsonResponse(200, ['accepted' => true]),
        ]);

        $client->createFeed('US');
        $client->getFeed('feed_1');
        $client->getFeedProducts('feed_1');
        $client->upsertProducts('feed_1', [$this->validProduct('p1')]);

        $this->assertCount(4, $this->recorded);
        foreach ($this->recorded as $call) {
            $this->assertContains('Authorization: Bearer '.self::API_KEY, $this->normalizedHeaders($call), 'Every outbound Feed API request MUST carry the Bearer api_key.');
        }
    }

    public function testApiKeyDoesNotLeakIntoTransportExceptionOnHttpError(): void
    {
        $client = $this->createClient([
            $this->jsonResponse(401, ['type' => 'auth_error', 'code' => 'invalid_key', 'message' => 'unauthorized']),
        ]);

        try {
            $client->createFeed('US');
            self::fail('Expected AcpFeedTransportException.');
        } catch (AcpFeedTransportException $e) {
            $this->assertStringNotContainsString(self::API_KEY, $e->getMessage(), 'The Bearer api_key MUST NOT leak into exception messages.');
            $this->assertStringNotContainsString(self::API_KEY, (string) $e, 'The Bearer api_key MUST NOT leak into the exception string representation.');
        }
    }

    public function testApiKeyDoesNotLeakIntoTransportExceptionOnNetworkFailure(): void
    {
        // A transport-level failure (e.g. DNS/connection) surfaces via the response.
        $client = $this->createClient([
            new MockResponse('', ['error' => 'Connection refused']),
        ]);

        try {
            $client->getFeed('feed_1');
            self::fail('Expected AcpFeedTransportException on transport failure.');
        } catch (AcpFeedTransportException $e) {
            $this->assertStringNotContainsString(self::API_KEY, $e->getMessage(), 'The Bearer api_key MUST NOT leak when a transport error is wrapped.');
            $this->assertStringNotContainsString(self::API_KEY, (string) $e);
        }
    }

    public function testThrowsWhenBaseUrlNotConfigured(): void
    {
        $client = $this->createClient([], baseUrl: '');

        $this->expectException(AcpFeedException::class);

        $client->getFeed('feed_1');
    }

    public function testThrowsWhenApiKeyNotConfigured(): void
    {
        $client = $this->createClient([], apiKey: '');

        $this->expectException(AcpFeedException::class);

        $client->getFeed('feed_1');
    }

    public function testGuardRunsBeforeAnyOutboundRequest(): void
    {
        // 認証情報未設定 (apiKey 空) のときは outbound 送信前にガードが作動する。
        $client = $this->createClient([], apiKey: '');

        try {
            $client->upsertProducts('feed_1', [$this->validProduct('p1')]);
            self::fail('Missing credentials MUST short-circuit before any request.');
        } catch (AcpFeedException) {
            // expected
        }

        $this->assertCount(0, $this->recorded, 'No HTTP request MUST be made when credentials are not configured.');
    }

    /**
     * @param array<int, MockResponse> $responses
     */
    private function createClient(
        array $responses,
        string $baseUrl = self::BASE_URL,
        string $apiKey = self::API_KEY,
    ): AcpFeedClient {
        return new AcpFeedClient(
            $this->mockHttpClient($responses),
            $this->createStub(AcpFeedGenerator::class),
            new AcpFeedValidator(),
            $baseUrl,
            $apiKey,
        );
    }

    /**
     * @param array<int, MockResponse> $responses
     */
    private function mockHttpClient(array $responses): HttpClientInterface
    {
        return new MockHttpClient(function (string $method, string $url, array $options) use (&$responses): MockResponse {
            $this->recorded[] = ['method' => $method, 'url' => $url, 'options' => $options];
            $response = array_shift($responses);
            if ($response === null) {
                throw new \LogicException('Unexpected extra HTTP request to '.$url);
            }

            return $response;
        });
    }

    /**
     * @param array<string, mixed> $body
     */
    private function jsonResponse(int $status, array $body): MockResponse
    {
        return new MockResponse(
            json_encode($body, JSON_THROW_ON_ERROR),
            ['http_code' => $status, 'response_headers' => ['Content-Type' => 'application/json']],
        );
    }

    /**
     * @return array<string, mixed> ACP Product valid against schema.feed.json $defs/Product
     */
    private function validProduct(string $id): array
    {
        return [
            'id' => $id,
            'variants' => [
                ['id' => $id.'-v1', 'title' => 'Variant 1'],
            ],
        ];
    }

    /**
     * @param array{method: string, url: string, options: array<string, mixed>} $call
     *
     * @return array<string, mixed>
     */
    private function decodeBody(array $call): array
    {
        $body = $call['options']['body'] ?? null;
        if (!\is_string($body) || $body === '') {
            return [];
        }

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }

    /**
     * @param array{method: string, url: string, options: array<string, mixed>} $call
     *
     * @return list<string>
     */
    private function normalizedHeaders(array $call): array
    {
        $headers = $call['options']['headers'] ?? [];
        $normalized = [];
        foreach ($headers as $key => $value) {
            if (\is_int($key)) {
                // already "Name: value"
                $normalized[] = $value;

                continue;
            }
            foreach ((array) $value as $v) {
                $normalized[] = $key.': '.$v;
            }
        }

        return $normalized;
    }
}
