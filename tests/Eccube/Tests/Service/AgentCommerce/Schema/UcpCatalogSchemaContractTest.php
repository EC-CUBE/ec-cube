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

use Eccube\Service\AgentCommerce\Catalog\AgentCatalogDescriptionDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogOptionDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogVariantDto;
use Eccube\Service\AgentCommerce\Catalog\AvailabilityStatus;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogProductSerializer;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogResponseBuilder;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * Layer 2 スキーマ契約 (UCP Catalog): UcpCatalogResponseBuilder の出力が
 * UCP の公式 schema (catalog_search / catalog_lookup) に適合することを検証する.
 *
 * UCP schema はリポジトリに同梱せず、SchemaValidatorTrait が解決する
 * (ECCUBE_UCP_SCHEMA_DIR / var/agent-commerce-spec/ucp / specifications/ucp)。
 * schema が無い環境では markTestSkipped。取得手順は本ディレクトリ README.md 参照。
 *
 * 一次ソース: UCP v2026-04-08
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/shopping/catalog_search.json
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/shopping/catalog_lookup.json
 */
final class UcpCatalogSchemaContractTest extends TestCase
{
    use SchemaValidatorTrait;

    private const SEARCH_RESPONSE_REF = 'https://ucp.dev/schemas/shopping/catalog_search.json#/$defs/search_response';

    // lookup_response ($defs/lookup_response) は variants[].inputs (input_correlation) を MUST とし
    // 現状未実装のため testLookupResponseMatchesUcpSchema は incomplete。実装時に const を復活させる。

    private const GET_PRODUCT_RESPONSE_REF = 'https://ucp.dev/schemas/shopping/catalog_lookup.json#/$defs/get_product_response';

    private ?UcpCatalogResponseBuilder $builder = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new UcpCatalogResponseBuilder(new UcpCatalogProductSerializer());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->builder = null;
    }

    public function testSearchResponseMatchesUcpSchema(): void
    {
        $response = $this->builder->buildSearchResponse(
            [$this->buildItem(), $this->buildMinimalItem()],
            ['has_next_page' => false]
        );

        $this->assertValidUcp(
            self::SEARCH_RESPONSE_REF,
            $response,
            'A catalog search response MUST satisfy catalog_search.json#/$defs/search_response (required: ucp, products).'
        );
    }

    #[DoesNotPerformAssertions]
    public function testLookupResponseMatchesUcpSchema(): void
    {
        // lookup_response の variants は lookup_variant を要求し、これは input_correlation[]
        // (`inputs`, minItems:1, 各 {id, match?}) を MUST とする。現状の
        // UcpCatalogController::lookup / UcpCatalogResponseBuilder は要求 id と variant の
        // 相関 (`inputs`) を出力していないため未充足。要求 id→variant 相関を実装後に green 化する
        // (UcpCatalogController::lookup の TODO 参照)。search / get_product は inputs 不要のため green。
        self::markTestIncomplete(
            'UCP lookup_response.variants[] MUST be lookup_variant with required `inputs` (input_correlation). '
            .'UcpCatalogController::lookup does not yet emit request-id correlation.'
        );

        // 実装後に有効化する検証本体:
        // $response = $this->builder->buildLookupResponse([$this->buildItem()]);
        // $this->assertValidUcp(self::LOOKUP_RESPONSE_REF, $response, '... variants[].inputs ...');
    }

    public function testGetProductResponseMatchesUcpSchema(): void
    {
        $response = $this->builder->buildProductResponse($this->buildItem());

        $this->assertValidUcp(
            self::GET_PRODUCT_RESPONSE_REF,
            $response,
            'A get-product response MUST satisfy catalog_lookup.json#/$defs/get_product_response (required: ucp, product).'
        );
    }

    /**
     * 必須項目のみ (Product: id/title/description/price_range/variants, Variant: id/title/description/price)
     * の最小商品も schema を満たすこと.
     */
    public function testMinimalProductMatchesUcpSchema(): void
    {
        $response = $this->builder->buildProductResponse($this->buildMinimalItem());

        $this->assertValidUcp(
            self::GET_PRODUCT_RESPONSE_REF,
            $response,
            'A minimal product MUST still satisfy required Product/Variant fields in the UCP schema.'
        );
    }

    private function buildItem(): AgentCatalogItemDto
    {
        return new AgentCatalogItemDto(
            id: '1',
            title: 'Classic Tee',
            description: new AgentCatalogDescriptionDto(plain: 'A classic cotton tee.'),
            url: 'https://merchant.example/products/detail/1',
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
                    options: [new AgentCatalogOptionDto('Color', 'Red'), new AgentCatalogOptionDto('Size', 'S')],
                ),
                new AgentCatalogVariantDto(
                    id: '11',
                    title: 'Classic Tee - Blue / M',
                    priceMinorUnits: 2999,
                    currency: 'USD',
                    available: false,
                    availabilityStatus: AvailabilityStatus::OUT_OF_STOCK,
                    sku: 'sku-blue-m',
                ),
            ],
        );
    }

    private function buildMinimalItem(): AgentCatalogItemDto
    {
        return new AgentCatalogItemDto(
            id: '2',
            title: 'Minimal',
            variants: [
                new AgentCatalogVariantDto(
                    id: '20',
                    title: 'Minimal Variant',
                    priceMinorUnits: 0,
                    currency: 'JPY',
                    available: true,
                    availabilityStatus: AvailabilityStatus::IN_STOCK,
                ),
            ],
        );
    }
}
