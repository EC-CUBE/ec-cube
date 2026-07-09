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

namespace Eccube\Tests\Service\AgentCommerce\Schema;

use Eccube\Service\AgentCommerce\Catalog\Acp\AcpFeedProductSerializer;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogBarcodeDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogDescriptionDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogMediaDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogOptionDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogVariantDto;
use Eccube\Service\AgentCommerce\Catalog\AvailabilityStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Layer 2 スキーマ契約 (ACP Feed): AcpFeedProductSerializer の出力が
 * vendored schema.feed.json の $defs/Product・$defs/Variant・$defs/FeedMetadata に適合することを検証する.
 *
 * 純ロジックのため DB / コンテナを使わず DTO を直接組み立てる。Layer 2.5 (DB 突合) では
 * 実 fixtures から生成した出力に対して同じ schema 突き合わせを行う。
 *
 * 一次ソース: ACP 2026-04-17
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json
 */
final class AcpFeedSchemaContractTest extends TestCase
{
    use SchemaValidatorTrait;

    private ?AcpFeedProductSerializer $serializer = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serializer = new AcpFeedProductSerializer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->serializer = null;
    }

    /**
     * 最小限の必須項目 (id, variants[].id, variants[].title) のみの Product が
     * schema.feed.json $defs/Product に適合すること.
     *
     * required は Product: [id, variants], Variant: [id, title]。
     */
    public function testMinimalProductMatchesAcpProductSchema(): void
    {
        $dto = new AgentCatalogItemDto(
            id: '1',
            title: 'Minimal',
            variants: [
                new AgentCatalogVariantDto(
                    id: '10',
                    title: 'Minimal Variant',
                    priceMinorUnits: 0,
                    currency: 'JPY',
                    available: true,
                    availabilityStatus: AvailabilityStatus::IN_STOCK,
                ),
            ],
        );

        $this->assertValidAcp(
            'Product',
            $this->serializer->serialize($dto),
            'ACP Product MUST satisfy required [id, variants] and each Variant required [id, title].'
        );
    }

    /**
     * 全項目を埋めた Product (description/url/media/list_price/variant_options/barcodes/availability)
     * が schema.feed.json $defs/Product に適合すること.
     */
    public function testFullyPopulatedProductMatchesAcpProductSchema(): void
    {
        $dto = $this->buildRichItem();

        $this->assertValidAcp(
            'Product',
            $this->serializer->serialize($dto),
            'A fully populated ACP Product (media, barcodes, variant_options, list_price) MUST satisfy the schema.'
        );
    }

    /**
     * 単一の Variant が schema.feed.json $defs/Variant に適合すること.
     */
    public function testVariantMatchesAcpVariantSchema(): void
    {
        $variant = new AgentCatalogVariantDto(
            id: '10',
            title: 'Classic Tee - Red / S',
            priceMinorUnits: 1999,
            currency: 'USD',
            available: true,
            availabilityStatus: AvailabilityStatus::IN_STOCK,
            sku: 'sku-red-s',
            listPriceMinorUnits: 2499,
            description: new AgentCatalogDescriptionDto(html: '<p>Red, small.</p>'),
            url: 'https://merchant.example/products/1',
            options: [new AgentCatalogOptionDto('Color', 'Red'), new AgentCatalogOptionDto('Size', 'S')],
            barcodes: [new AgentCatalogBarcodeDto('GTIN', '00012345600012')],
            media: [new AgentCatalogMediaDto('https://cdn.example/red-s.jpg', 'image', 'Red S')],
        );

        $this->assertValidAcp(
            'Variant',
            $this->serializer->serializeVariant($variant),
            'A fully populated ACP Variant MUST satisfy the schema.'
        );
    }

    /**
     * price.amount は minor unit 整数で出力されること (schema は integer / minimum:0).
     */
    public function testVariantPriceIsMinorUnitInteger(): void
    {
        $variant = new AgentCatalogVariantDto(
            id: '10',
            title: 'V',
            priceMinorUnits: 12345,
            currency: 'JPY',
            available: true,
            availabilityStatus: AvailabilityStatus::IN_STOCK,
        );

        $serialized = $this->serializer->serializeVariant($variant);

        $this->assertIsInt($serialized['price']['amount'], 'ACP Price.amount MUST be an integer expressed in ISO 4217 minor units.');
        $this->assertSame(12345, $serialized['price']['amount'], 'ACP Price.amount MUST equal the minor-unit integer carried by the DTO.');
        $this->assertValidAcp('Variant', $serialized, 'Price.amount integer in minor units MUST satisfy the schema.');
    }

    /**
     * availability.status の既知値がいずれも schema を満たすこと (extensible string).
     */
    #[DataProvider(methodName: 'availabilityStatusProvider')]
    public function testAvailabilityStatusValuesMatchSchema(AvailabilityStatus $status): void
    {
        $variant = new AgentCatalogVariantDto(
            id: '10',
            title: 'V',
            priceMinorUnits: 100,
            currency: 'JPY',
            available: $status !== AvailabilityStatus::OUT_OF_STOCK,
            availabilityStatus: $status,
        );

        $serialized = $this->serializer->serializeVariant($variant);

        $this->assertSame($status->value, $serialized['availability']['status']);
        $this->assertValidAcp('Variant', $serialized, 'Availability.status known value '.$status->value.' MUST satisfy the schema.');
    }

    /**
     * @return iterable<string, array{AvailabilityStatus}>
     */
    public static function availabilityStatusProvider(): iterable
    {
        foreach (AvailabilityStatus::cases() as $case) {
            yield $case->value => [$case];
        }
    }

    /**
     * FeedMetadata: 必須 id のみでも適合すること.
     */
    public function testFeedMetadataWithOnlyRequiredIdMatchesSchema(): void
    {
        $this->assertValidAcp(
            'FeedMetadata',
            ['id' => 'feed_8f3K2x'],
            'FeedMetadata MUST satisfy required [id].'
        );
    }

    /**
     * FeedMetadata: target_country (alpha-2) と updated_at (date-time) を含めても適合すること.
     */
    public function testFeedMetadataWithTargetCountryAndUpdatedAtMatchesSchema(): void
    {
        $this->assertValidAcp(
            'FeedMetadata',
            ['id' => 'feed_8f3K2x', 'target_country' => 'JP', 'updated_at' => '2026-06-08T00:00:00Z'],
            'FeedMetadata with alpha-2 target_country and date-time updated_at MUST satisfy the schema.'
        );
    }

    /**
     * FeedMetadata: target_country は ^[A-Z]{2}$ に限られ、3 文字 (alpha-3) は reject されること.
     */
    public function testFeedMetadataRejectsNonAlpha2TargetCountry(): void
    {
        $this->assertInvalidAcp(
            'FeedMetadata',
            ['id' => 'feed_x', 'target_country' => 'JPN'],
            'FeedMetadata.target_country MUST be an ISO 3166-1 alpha-2 code (pattern ^[A-Z]{2}$); alpha-3 MUST be rejected.'
        );
    }

    private function buildRichItem(): AgentCatalogItemDto
    {
        return new AgentCatalogItemDto(
            id: '1',
            title: 'Classic Tee',
            description: new AgentCatalogDescriptionDto(html: '<p>A tee.</p>'),
            url: 'https://merchant.example/products/1',
            media: [new AgentCatalogMediaDto('https://cdn.example/main.jpg', 'image', 'front', 800, 600)],
            variants: [
                new AgentCatalogVariantDto(
                    id: '10',
                    title: 'Classic Tee - Red / S',
                    priceMinorUnits: 1999,
                    currency: 'USD',
                    available: true,
                    availabilityStatus: AvailabilityStatus::IN_STOCK,
                    sku: 'sku-red-s',
                    listPriceMinorUnits: 2499,
                    description: new AgentCatalogDescriptionDto(plain: 'Red, small.'),
                    url: 'https://merchant.example/products/1',
                    options: [new AgentCatalogOptionDto('Color', 'Red')],
                    barcodes: [new AgentCatalogBarcodeDto('GTIN', '00012345600012')],
                    media: [new AgentCatalogMediaDto('https://cdn.example/red-s.jpg')],
                ),
            ],
        );
    }
}
