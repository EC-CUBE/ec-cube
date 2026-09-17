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

use Eccube\Service\AgentCommerce\Catalog\Acp\AcpFeedProductSerializer;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogBarcodeDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogDescriptionDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogMediaDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogOptionDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogVariantDto;
use Eccube\Service\AgentCommerce\Catalog\AvailabilityStatus;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (serialization) tests for AcpFeedProductSerializer.
 *
 * Verifies the ACP Feed Product / Variant shape (schema.feed.json $defs):
 * required keys (id, variants / id, title), minor-unit price embedding, and that
 * null / empty optional fields are never emitted (schema additionalProperties:false).
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json
 */
final class AcpFeedProductSerializerTest extends TestCase
{
    private AcpFeedProductSerializer $serializer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serializer = new AcpFeedProductSerializer();
    }

    public function testSerializeProductHasRequiredIdAndVariants(): void
    {
        $product = $this->minimalProduct();

        $result = $this->serializer->serialize($product);

        $this->assertSame('100', $result['id'], 'ACP Product MUST carry the id');
        $this->assertArrayHasKey('variants', $result, 'ACP Product MUST carry variants[]');
        $this->assertCount(1, $result['variants'], 'Each variant DTO serializes to one entry');
    }

    public function testSerializeProductOmitsEmptyTitle(): void
    {
        $product = new AgentCatalogItemDto(id: '100', title: '', variants: [$this->minimalVariant()]);

        $result = $this->serializer->serialize($product);

        $this->assertArrayNotHasKey('title', $result, 'An empty product title must not be emitted');
    }

    public function testSerializeProductEmitsTitleWhenPresent(): void
    {
        $product = new AgentCatalogItemDto(id: '100', title: 'Coffee', variants: [$this->minimalVariant()]);

        $result = $this->serializer->serialize($product);

        $this->assertSame('Coffee', $result['title'], 'A non-empty title must be emitted');
    }

    public function testSerializeProductOmitsNullDescriptionAndUrlAndMedia(): void
    {
        $product = new AgentCatalogItemDto(
            id: '100',
            title: 'Coffee',
            media: [],
            variants: [$this->minimalVariant()],
        );

        $result = $this->serializer->serialize($product);

        $this->assertArrayNotHasKey('description', $result, 'A null description must not be emitted');
        $this->assertArrayNotHasKey('url', $result, 'A null url must not be emitted');
        $this->assertArrayNotHasKey('media', $result, 'Empty media must not be emitted');
    }

    public function testSerializeProductOmitsEmptyDescriptionDto(): void
    {
        $product = new AgentCatalogItemDto(
            id: '100',
            title: 'Coffee',
            description: new AgentCatalogDescriptionDto(),
            variants: [$this->minimalVariant()],
        );

        $result = $this->serializer->serialize($product);

        $this->assertArrayNotHasKey('description', $result, 'An all-null description DTO must not be emitted (schema minProperties:1)');
    }

    public function testSerializeProductEmitsDescriptionHtmlOnly(): void
    {
        $product = new AgentCatalogItemDto(
            id: '100',
            title: 'Coffee',
            description: new AgentCatalogDescriptionDto(html: '<p>Rich</p>'),
            variants: [$this->minimalVariant()],
        );

        $result = $this->serializer->serialize($product);

        $this->assertSame(['html' => '<p>Rich</p>'], $result['description'], 'Only present description formats are emitted; plain/markdown stay absent');
    }

    public function testSerializeProductEmitsMedia(): void
    {
        $product = new AgentCatalogItemDto(
            id: '100',
            title: 'Coffee',
            media: [new AgentCatalogMediaDto(url: 'https://x/i.jpg', type: 'image')],
            variants: [$this->minimalVariant()],
        );

        $result = $this->serializer->serialize($product);

        $this->assertSame([['type' => 'image', 'url' => 'https://x/i.jpg']], $result['media'], 'Media required keys are type and url; optional alt/width/height absent when null');
    }

    public function testSerializeVariantHasRequiredIdAndTitle(): void
    {
        $result = $this->serializer->serializeVariant($this->minimalVariant());

        $this->assertSame('200', $result['id'], 'ACP Variant MUST carry the id');
        $this->assertSame('Coffee Bag', $result['title'], 'ACP Variant MUST carry the title');
    }

    public function testSerializeVariantEmbedsPriceAsMinorUnitInteger(): void
    {
        $result = $this->serializer->serializeVariant($this->minimalVariant());

        $this->assertSame(['amount' => 1200, 'currency' => 'JPY'], $result['price'], 'Variant price MUST be a minor-unit integer amount with a currency');
    }

    public function testSerializeVariantEmbedsAvailability(): void
    {
        $result = $this->serializer->serializeVariant($this->minimalVariant());

        $this->assertSame(['available' => true, 'status' => 'in_stock'], $result['availability'], 'Variant availability MUST carry available bool and a known status string');
    }

    public function testSerializeVariantOmitsListPriceWhenNull(): void
    {
        $variant = $this->variant();

        $result = $this->serializer->serializeVariant($variant);

        $this->assertArrayNotHasKey('list_price', $result, 'A null list price must not be emitted');
    }

    public function testSerializeVariantEmitsListPriceWhenSet(): void
    {
        $variant = $this->variant(listPriceMinorUnits: 1500);

        $result = $this->serializer->serializeVariant($variant);

        $this->assertSame(['amount' => 1500, 'currency' => 'JPY'], $result['list_price'], 'A set list price is emitted with the variant currency');
    }

    public function testSerializeVariantOmitsEmptyOptionsAndBarcodesAndMedia(): void
    {
        $result = $this->serializer->serializeVariant($this->minimalVariant());

        $this->assertArrayNotHasKey('variant_options', $result, 'Empty options must not be emitted');
        $this->assertArrayNotHasKey('barcodes', $result, 'Empty barcodes must not be emitted');
        $this->assertArrayNotHasKey('media', $result, 'Empty media must not be emitted');
        $this->assertArrayNotHasKey('url', $result, 'A null url must not be emitted');
        $this->assertArrayNotHasKey('description', $result, 'A null description must not be emitted');
    }

    public function testSerializeVariantEmitsOptionsAsVariantOptions(): void
    {
        $variant = $this->variant(options: [new AgentCatalogOptionDto('Color', 'Red')]);

        $result = $this->serializer->serializeVariant($variant);

        $this->assertSame([['name' => 'Color', 'value' => 'Red']], $result['variant_options'], 'Options serialize to ACP variant_options with name/value');
    }

    public function testSerializeVariantEmitsBarcodes(): void
    {
        $variant = $this->variant(barcodes: [new AgentCatalogBarcodeDto('gtin', '4901234567894')]);

        $result = $this->serializer->serializeVariant($variant);

        $this->assertSame([['type' => 'gtin', 'value' => '4901234567894']], $result['barcodes'], 'Barcodes serialize with type/value when present');
    }

    private function minimalProduct(): AgentCatalogItemDto
    {
        return new AgentCatalogItemDto(id: '100', title: 'Coffee', variants: [$this->minimalVariant()]);
    }

    private function minimalVariant(): AgentCatalogVariantDto
    {
        return $this->variant();
    }

    /**
     * @param AgentCatalogOptionDto[]  $options
     * @param AgentCatalogBarcodeDto[] $barcodes
     */
    private function variant(?int $listPriceMinorUnits = null, array $options = [], array $barcodes = []): AgentCatalogVariantDto
    {
        return new AgentCatalogVariantDto(
            id: '200',
            title: 'Coffee Bag',
            priceMinorUnits: 1200,
            currency: 'JPY',
            available: true,
            availabilityStatus: AvailabilityStatus::IN_STOCK,
            listPriceMinorUnits: $listPriceMinorUnits,
            options: $options,
            barcodes: $barcodes,
            media: [],
        );
    }
}
