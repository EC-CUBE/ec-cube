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

namespace Eccube\Tests\Service\AgentCommerce\Catalog\Ucp;

use Eccube\Service\AgentCommerce\Catalog\AgentCatalogDescriptionDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogOptionDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogVariantDto;
use Eccube\Service\AgentCommerce\Catalog\AvailabilityStatus;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogProductSerializer;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (serialization) tests for UcpCatalogProductSerializer.
 *
 * Verifies the UCP catalog Product / Variant shape (types/product.json,
 * types/variant.json): required keys (id, title, description, price_range,
 * variants / id, title, description, price), price_range min/max computation,
 * and the description minProperties:1 guarantee even for empty DTOs.
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/types/product.json
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/types/variant.json
 */
final class UcpCatalogProductSerializerTest extends TestCase
{
    private UcpCatalogProductSerializer $serializer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serializer = new UcpCatalogProductSerializer();
    }

    public function testSerializeProductHasAllRequiredFields(): void
    {
        $product = $this->product([$this->variant('200', 1200)]);

        $result = $this->serializer->serialize($product);

        $this->assertSame('100', $result['id'], 'UCP Product MUST carry id');
        $this->assertSame('Coffee', $result['title'], 'UCP Product MUST carry title');
        $this->assertArrayHasKey('description', $result, 'UCP Product MUST carry description');
        $this->assertArrayHasKey('price_range', $result, 'UCP Product MUST carry price_range');
        $this->assertArrayHasKey('variants', $result, 'UCP Product MUST carry variants[]');
    }

    public function testSerializeProductPriceRangeFromSingleVariant(): void
    {
        $product = $this->product([$this->variant('200', 1200)]);

        $result = $this->serializer->serialize($product);

        $this->assertSame(['amount' => 1200, 'currency' => 'JPY'], $result['price_range']['min'], 'A single variant makes min equal to its price');
        $this->assertSame(['amount' => 1200, 'currency' => 'JPY'], $result['price_range']['max'], 'A single variant makes max equal to its price');
    }

    public function testSerializeProductPriceRangeSpansMultipleVariants(): void
    {
        $product = $this->product([
            $this->variant('201', 1500),
            $this->variant('202', 800),
            $this->variant('203', 1200),
        ]);

        $result = $this->serializer->serialize($product);

        $this->assertSame(800, $result['price_range']['min']['amount'], 'price_range.min must be the lowest variant price');
        $this->assertSame(1500, $result['price_range']['max']['amount'], 'price_range.max must be the highest variant price');
        $this->assertSame('JPY', $result['price_range']['min']['currency'], 'price_range currency comes from the first variant');
    }

    public function testSerializeProductDescriptionEmptyGuaranteesPlain(): void
    {
        $product = new AgentCatalogItemDto(
            id: '100',
            title: 'Coffee',
            description: null,
            variants: [$this->variant('200', 1200)],
        );

        $result = $this->serializer->serialize($product);

        $this->assertSame(['plain' => ''], $result['description'], 'A missing product description must degrade to plain:"" to satisfy minProperties:1');
    }

    public function testSerializeProductDescriptionEmitsPresentFormats(): void
    {
        $product = new AgentCatalogItemDto(
            id: '100',
            title: 'Coffee',
            description: new AgentCatalogDescriptionDto(html: '<p>Rich</p>'),
            variants: [$this->variant('200', 1200)],
        );

        $result = $this->serializer->serialize($product);

        $this->assertSame(['html' => '<p>Rich</p>'], $result['description'], 'Present description formats are emitted verbatim');
    }

    public function testSerializeProductOmitsNullUrlAndEmptyCategoriesAndMedia(): void
    {
        $product = new AgentCatalogItemDto(
            id: '100',
            title: 'Coffee',
            url: null,
            media: [],
            categories: [],
            variants: [$this->variant('200', 1200)],
        );

        $result = $this->serializer->serialize($product);

        $this->assertArrayNotHasKey('url', $result, 'A null url must not be emitted');
        $this->assertArrayNotHasKey('categories', $result, 'Empty categories must not be emitted');
        $this->assertArrayNotHasKey('media', $result, 'Empty media must not be emitted');
    }

    public function testSerializeProductEmitsCategories(): void
    {
        $product = new AgentCatalogItemDto(
            id: '100',
            title: 'Coffee',
            categories: ['Beverages', 'Coffee'],
            variants: [$this->variant('200', 1200)],
        );

        $result = $this->serializer->serialize($product);

        $this->assertSame([['value' => 'Beverages'], ['value' => 'Coffee']], $result['categories'], 'Category names map to {value} objects per UCP category.json');
    }

    public function testSerializeVariantHasAllRequiredFields(): void
    {
        $result = $this->serializer->serializeVariant($this->variant('200', 1200));

        $this->assertSame('200', $result['id'], 'UCP Variant MUST carry id');
        $this->assertSame('Coffee Bag', $result['title'], 'UCP Variant MUST carry title');
        $this->assertArrayHasKey('description', $result, 'UCP Variant MUST carry description');
        $this->assertSame(['amount' => 1200, 'currency' => 'JPY'], $result['price'], 'UCP Variant MUST carry a minor-unit price');
    }

    public function testSerializeVariantDescriptionDegradesToPlain(): void
    {
        $result = $this->serializer->serializeVariant($this->variant('200', 1200));

        $this->assertSame(['plain' => ''], $result['description'], 'A missing variant description degrades to plain:"" (description is required, minProperties:1)');
    }

    public function testSerializeVariantEmbedsAvailability(): void
    {
        $result = $this->serializer->serializeVariant($this->variant('200', 1200, available: false, status: AvailabilityStatus::OUT_OF_STOCK));

        $this->assertSame(['available' => false, 'status' => 'out_of_stock'], $result['availability'], 'Variant availability carries the available bool and status string');
    }

    public function testSerializeVariantOmitsNullSkuAndUrlAndListPrice(): void
    {
        $result = $this->serializer->serializeVariant($this->variant('200', 1200));

        $this->assertArrayNotHasKey('sku', $result, 'A null sku must not be emitted');
        $this->assertArrayNotHasKey('url', $result, 'A null url must not be emitted');
        $this->assertArrayNotHasKey('list_price', $result, 'A null list price must not be emitted');
        $this->assertArrayNotHasKey('options', $result, 'Empty options must not be emitted');
        $this->assertArrayNotHasKey('barcodes', $result, 'Empty barcodes must not be emitted');
    }

    public function testSerializeVariantEmitsSkuAndOptions(): void
    {
        $variant = $this->variant('200', 1200, sku: 'SKU-1', options: [new AgentCatalogOptionDto('Size', 'L')]);

        $result = $this->serializer->serializeVariant($variant);

        $this->assertSame('SKU-1', $result['sku'], 'A present sku is emitted');
        $this->assertSame([['name' => 'Size', 'label' => 'L']], $result['options'], 'UCP variant options use the options key with name/label per selected_option.json');
    }

    /**
     * @param AgentCatalogVariantDto[] $variants
     */
    private function product(array $variants): AgentCatalogItemDto
    {
        return new AgentCatalogItemDto(
            id: '100',
            title: 'Coffee',
            description: new AgentCatalogDescriptionDto(plain: 'A coffee'),
            variants: $variants,
        );
    }

    /**
     * @param AgentCatalogOptionDto[] $options
     */
    private function variant(
        string $id,
        int $priceMinorUnits,
        bool $available = true,
        AvailabilityStatus $status = AvailabilityStatus::IN_STOCK,
        ?string $sku = null,
        array $options = [],
    ): AgentCatalogVariantDto {
        return new AgentCatalogVariantDto(
            id: $id,
            title: 'Coffee Bag',
            priceMinorUnits: $priceMinorUnits,
            currency: 'JPY',
            available: $available,
            availabilityStatus: $status,
            sku: $sku,
            listPriceMinorUnits: null,
            description: null,
            url: null,
            options: $options,
            barcodes: [],
            media: [],
        );
    }
}
