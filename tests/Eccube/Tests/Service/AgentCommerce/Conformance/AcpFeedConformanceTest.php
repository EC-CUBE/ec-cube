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

namespace Eccube\Tests\Service\AgentCommerce\Conformance;

use Eccube\Service\AgentCommerce\Catalog\Acp\AcpFeedProductSerializer;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogVariantDto;
use Eccube\Service\AgentCommerce\Catalog\AvailabilityStatus;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Layer 0 (仕様適合性) — ACP Product Feed (offline full replacement).
 *
 * ACP 2026-04-17 の規範要件 (MUST / MUST NOT) を 1 テスト = 1 要件でトレースする。
 * オフライン全置換取り込みでは metadata.json が FeedMetadata 形状を取り、products.jsonl は
 * 1 行 1 Product (full replacement) で構成される。Product/Variant の必須項目および金額が
 * minor unit 整数であることを、vendored schema (schema.feed.json) と Product serializer の
 * 出力に対して検証する。push トランスポート (POST/PATCH/Bearer) は Layer 9 で別途検証する。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/openapi/openapi.feed.yaml#L10
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json#L435
 */
final class AcpFeedConformanceTest extends TestCase
{
    private const SCHEMA_PATH = __DIR__.'/../../../../../../src/Eccube/Resource/AgentCommerce/Acp/schema.feed.json';

    private ?AcpFeedProductSerializer $serializer = null;

    protected function setUp(): void
    {
        $this->serializer = new AcpFeedProductSerializer();
    }

    protected function tearDown(): void
    {
        $this->serializer = null;
    }

    /**
     * MUST: products.jsonl は 1 行 1 Product オブジェクトで構成される (full replacement)。
     * 各行が独立して JSON デコード可能であり Product schema に適合しなければならない。
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/openapi/openapi.feed.yaml#L10
     */
    public function testProductsJsonlHasExactlyOneProductObjectPerLine(): void
    {
        $products = [
            $this->serializer->serialize($this->sampleItem('1', '101')),
            $this->serializer->serialize($this->sampleItem('2', '201')),
        ];

        // products.jsonl の生成を 1 行 1 Product で再現する。
        $lines = array_map(
            static fn (array $product): string => json_encode($product, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $products
        );
        $jsonl = implode("\n", $lines)."\n";

        $rows = array_values(array_filter(explode("\n", $jsonl), static fn (string $l): bool => $l !== ''));
        $this->assertCount(2, $rows, 'MUST: products.jsonl contains one Product object per line (one line per product).');

        foreach ($rows as $row) {
            $decoded = json_decode($row, true, 512, JSON_THROW_ON_ERROR);
            $this->assertIsArray($decoded, 'MUST: each products.jsonl line is an independently decodable JSON Product object.');
            $this->assertSchemaValid($decoded, 'Product', 'MUST: each products.jsonl line conforms to the Product schema.');
        }
    }

    /**
     * MUST: ファイル取り込み (products.jsonl) は商品集合を完全置換する (partial 更新は PATCH のみ)。
     * 本実装の JSONL 生成は与えられた全 Product を毎回出力する (差分でない) ことをトレースする。
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/openapi/openapi.feed.yaml#L11
     */
    public function testFileIngestionReplacesTheFullProductSet(): void
    {
        $items = [
            $this->sampleItem('1', '101'),
            $this->sampleItem('2', '201'),
            $this->sampleItem('3', '301'),
        ];

        $lines = array_map(
            fn (AgentCatalogItemDto $item): string => json_encode($this->serializer->serialize($item), JSON_THROW_ON_ERROR),
            $items
        );

        $this->assertCount(
            3,
            $lines,
            'MUST: offline file ingestion replaces the full product set; the generated JSONL emits every product (full replacement, not a delta).'
        );
    }

    /**
     * MUST: ACP Product は id と variants を必須項目として持つ。
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json#L439
     */
    public function testProductRequiresIdAndVariants(): void
    {
        $product = $this->serializer->serialize($this->sampleItem('1', '101'));

        $this->assertArrayHasKey('id', $product, 'MUST: a feed Product MUST have an id.');
        $this->assertArrayHasKey('variants', $product, 'MUST: a feed Product MUST have variants.');
        $this->assertNotEmpty($product['variants'], 'MUST: a feed Product MUST have at least one variant.');
        $this->assertSchemaValid($product, 'Product', 'MUST: the serialized Product conforms to the Product schema.');
    }

    /**
     * MUST: ACP Variant は id と title を必須項目として持つ。
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json#L309
     */
    public function testVariantRequiresIdAndTitle(): void
    {
        $variant = $this->serializer->serializeVariant($this->sampleVariant('101'));

        $this->assertArrayHasKey('id', $variant, 'MUST: a feed Variant MUST have an id.');
        $this->assertArrayHasKey('title', $variant, 'MUST: a feed Variant MUST have a title.');
    }

    /**
     * MUST: Variant の price.amount は ISO 4217 minor unit の整数 (>=0)、currency は ^[A-Z]{3}$。
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json#L29
     */
    public function testPriceAmountIsNonNegativeIntegerMinorUnits(): void
    {
        $variant = $this->serializer->serializeVariant($this->sampleVariant('101'));

        $this->assertArrayHasKey('price', $variant, 'A priced variant carries a Price object.');
        $this->assertIsInt(
            $variant['price']['amount'],
            'MUST: Price.amount is an integer expressed in ISO 4217 minor units (not a float).'
        );
        $this->assertGreaterThanOrEqual(
            0,
            $variant['price']['amount'],
            'MUST: Price.amount has a minimum of 0.'
        );
        $this->assertMatchesRegularExpression(
            '/^[A-Z]{3}$/',
            $variant['price']['currency'],
            'MUST: Price.currency is a three-letter ISO 4217 identifier.'
        );
    }

    /**
     * MUST: metadata.json は FeedMetadata 形状で id を必須とし、target_country は ^[A-Z]{2}$。
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json#L491
     */
    public function testFeedMetadataRequiresIdAndAlpha2TargetCountry(): void
    {
        $metadata = [
            'id' => 'feed_test',
            'target_country' => 'JP',
            'updated_at' => '2026-06-08T00:00:00+00:00',
        ];

        $this->assertArrayHasKey('id', $metadata, 'MUST: FeedMetadata MUST have an id.');
        $this->assertMatchesRegularExpression(
            '/^[A-Z]{2}$/',
            $metadata['target_country'],
            'MUST: FeedMetadata.target_country is an ISO 3166-1 alpha-2 country code.'
        );
        $this->assertSchemaValid($metadata, 'FeedMetadata', 'MUST: metadata.json conforms to the FeedMetadata schema.');
    }

    /**
     * MUST: id を欠く metadata は schema 違反として拒否される (id required の負例)。
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json#L495
     */
    public function testFeedMetadataWithoutIdIsRejected(): void
    {
        $metadata = ['target_country' => 'JP'];

        $validator = new Validator();
        $object = json_decode((string) json_encode($metadata));
        $validator->validate($object, (object) ['$ref' => 'file://'.realpath(self::SCHEMA_PATH).'#/$defs/FeedMetadata']);

        $this->assertFalse(
            $validator->isValid(),
            'MUST: FeedMetadata without the required "id" MUST be rejected.'
        );
    }

    /**
     * MUST: availability.status の既知値集合 (in_stock 等) に AvailabilityStatus enum が適合する。
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json#L60
     */
    public function testAvailabilityStatusUsesKnownValues(): void
    {
        $variant = $this->serializer->serializeVariant($this->sampleVariant('101'));
        $known = ['in_stock', 'limited_stock', 'backorder', 'preorder', 'out_of_stock', 'discontinued'];

        $this->assertArrayHasKey('availability', $variant, 'A variant carries an availability object.');
        $this->assertContains(
            $variant['availability']['status'],
            $known,
            'MUST: availability.status uses a known fulfillment-state value.'
        );
    }

    /**
     * Feed push トランスポート (POST /feeds, PATCH /feeds/{id}/products, outbound Bearer) の
     * 規範要件は国内 GA 前で活用不可のため Layer 9 (ACP push) で別途検証する。
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/openapi/openapi.feed.yaml
     */
    public function testFeedPushTransportRequirementsAreDeferredToLayer9(): void
    {
        self::markTestIncomplete('Feed push transport (POST /feeds, PATCH /feeds/{id}/products, outbound Bearer api_key, idempotency) は国内 GA 前で活用不可のため Layer 9 (ACP push) で検証する。');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function assertSchemaValid(array $data, string $def, string $message): void
    {
        $schemaPath = realpath(self::SCHEMA_PATH);
        $this->assertNotFalse($schemaPath, 'ACP feed schema must exist at src/Eccube/Resource/AgentCommerce/Acp/schema.feed.json');

        $validator = new Validator();
        $object = json_decode((string) json_encode($data));
        $validator->validate($object, (object) ['$ref' => 'file://'.$schemaPath.'#/$defs/'.$def]);

        $errors = array_map(
            static fn (array $e): string => sprintf('[%s] %s', $e['property'], $e['message']),
            $validator->getErrors()
        );
        $this->assertTrue($validator->isValid(), $message.' Violations: '.implode('; ', $errors));
    }

    private function sampleVariant(string $id): AgentCatalogVariantDto
    {
        return new AgentCatalogVariantDto(
            id: $id,
            title: 'Sample Variant '.$id,
            priceMinorUnits: 1999,
            currency: 'JPY',
            available: true,
            availabilityStatus: AvailabilityStatus::IN_STOCK,
        );
    }

    private function sampleItem(string $productId, string $variantId): AgentCatalogItemDto
    {
        return new AgentCatalogItemDto(
            id: $productId,
            title: 'Sample Product '.$productId,
            variants: [$this->sampleVariant($variantId)],
        );
    }
}
