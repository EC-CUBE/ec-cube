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

use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogVariantDto;
use Eccube\Service\AgentCommerce\Catalog\AvailabilityStatus;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogProductSerializer;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogResponseBuilder;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * Layer 0 (仕様適合性) — UCP Catalog capability (REST transport).
 *
 * UCP v2026-04-08 の規範要件 (MUST / MUST NOT) を 1 テスト = 1 要件でトレースする。
 * catalog のエンドポイントはすべて POST (RPC 形式) であり、各レスポンスは ucp ラッパーと
 * products[] (search/lookup) / product (get_product) を持つ。catalog query は read-only の
 * ため RFC 9421 署名は OPTIONAL。HTTP メソッド (POST) や 200/400 ステータスはコントローラ層
 * (Layer 3) で検証し、本クラスはレスポンス本文の構造的 MUST と署名 OPTIONAL をトレースする。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/catalog/rest.md#L587
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/shopping/catalog_search.json#L33
 */
final class UcpCatalogConformanceTest extends TestCase
{
    private ?UcpCatalogResponseBuilder $builder = null;

    protected function setUp(): void
    {
        $this->builder = new UcpCatalogResponseBuilder(new UcpCatalogProductSerializer());
    }

    protected function tearDown(): void
    {
        $this->builder = null;
    }

    /**
     * MUST: search レスポンスは ucp ラッパーと products[] を必須項目として持つ。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/shopping/catalog_search.json#L34
     */
    public function testSearchResponseRequiresUcpWrapperAndProductsArray(): void
    {
        $response = $this->builder->buildSearchResponse([$this->sampleItem()]);

        $this->assertArrayHasKey('ucp', $response, 'MUST: search_response requires the "ucp" wrapper.');
        $this->assertArrayHasKey('products', $response, 'MUST: search_response requires the "products" array.');
        $this->assertIsArray($response['products'], 'MUST: search_response.products is an array.');
        $this->assertArrayHasKey('version', $response['ucp'], 'MUST: the ucp wrapper declares a version.');
        $this->assertSame(
            '2026-04-08',
            $response['ucp']['version'],
            'MUST: the ucp wrapper version is the advertised UCP version 2026-04-08.'
        );
    }

    /**
     * MUST: lookup レスポンスは ucp ラッパーと products[] を必須項目として持つ。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/shopping/catalog_lookup.json#L52
     */
    public function testLookupResponseRequiresUcpWrapperAndProductsArray(): void
    {
        $response = $this->builder->buildLookupResponse([$this->sampleItem()]);

        $this->assertArrayHasKey('ucp', $response, 'MUST: lookup_response requires the "ucp" wrapper.');
        $this->assertArrayHasKey('products', $response, 'MUST: lookup_response requires the "products" array.');
        $this->assertIsArray($response['products'], 'MUST: lookup_response.products is an array.');
    }

    /**
     * MUST: get_product レスポンスは ucp ラッパーと単数の product を必須項目として持つ。
     * (lookup の products[] と異なり単一リソース操作のため "product" は単数。)
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/shopping/catalog_lookup.json#L153
     */
    public function testGetProductResponseRequiresUcpWrapperAndSingularProduct(): void
    {
        $response = $this->builder->buildProductResponse($this->sampleItem());

        $this->assertArrayHasKey('ucp', $response, 'MUST: get_product_response requires the "ucp" wrapper.');
        $this->assertArrayHasKey('product', $response, 'MUST: get_product_response requires a singular "product".');
        $this->assertArrayNotHasKey(
            'products',
            $response,
            'MUST: get_product is a single-resource operation and returns "product" (singular), not "products".'
        );
    }

    /**
     * MUST: catalog の Product は id / title / description / price_range / variants を持つ。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/shopping/types/product.json
     */
    public function testCatalogProductCarriesRequiredFields(): void
    {
        $response = $this->builder->buildProductResponse($this->sampleItem());
        $product = $response['product'];

        foreach (['id', 'title', 'description', 'price_range', 'variants'] as $field) {
            $this->assertArrayHasKey(
                $field,
                $product,
                sprintf('MUST: a UCP catalog Product MUST carry "%s".', $field)
            );
        }
        $this->assertNotEmpty(
            $product['description'],
            'MUST: Product.description has at least one form (minProperties:1).'
        );
    }

    /**
     * MUST: 各 variant は valid な Price (amount=minor unit 整数 + currency) を持つ (REST conformance #2)。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/catalog/rest.md#L590
     */
    public function testVariantsReturnValidPriceObjectsWithIntegerMinorUnits(): void
    {
        $response = $this->builder->buildProductResponse($this->sampleItem());
        $variants = $response['product']['variants'];

        $this->assertNotEmpty($variants, 'A catalog Product carries at least one variant.');
        foreach ($variants as $variant) {
            $this->assertArrayHasKey('price', $variant, 'MUST: each variant returns a valid Price object.');
            $this->assertArrayHasKey('amount', $variant['price'], 'MUST: Price has an amount.');
            $this->assertArrayHasKey('currency', $variant['price'], 'MUST: Price has a currency.');
            $this->assertIsInt(
                $variant['price']['amount'],
                'MUST: Price.amount is an integer in ISO 4217 minor units (not a float).'
            );
            $this->assertMatchesRegularExpression(
                '/^[A-Z]{3}$/',
                $variant['price']['currency'],
                'MUST: Price.currency is a three-letter ISO 4217 code.'
            );
        }
    }

    /**
     * MUST NOT: catalog レスポンス本文の serializer は署名を付与しない。catalog query は
     * read-only のため RFC 9421 署名は OPTIONAL であり、本実装は署名を付さない選択をとる。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/signatures.md#L508
     */
    public function testCatalogResponsesAreUnsignedSinceSignatureIsOptionalForReadOnlyQueries(): void
    {
        $response = $this->builder->buildSearchResponse([$this->sampleItem()]);

        // read-only catalog query では RFC 9421 署名は OPTIONAL。本実装は本文に署名関連項目を入れない。
        $this->assertArrayNotHasKey(
            'signature',
            $response,
            'OPTIONAL: signatures are OPTIONAL for read-only catalog queries; this implementation does not sign catalog responses.'
        );
    }

    /**
     * MUST: REST transport は default limit 10 の cursor-based pagination をサポートする。
     * pagination の付与・cursor 不透明性はコントローラ層 (Layer 3) で検証する。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/catalog/rest.md#L591
     */
    #[DoesNotPerformAssertions]
    public function testCursorPaginationDefaultLimitIsVerifiedAtControllerLayer(): void
    {
        self::markTestIncomplete('MUST: REST transport supports cursor-based pagination with a default limit of 10. pagination cursor の生成・default limit はコントローラ (UcpCatalogController) の Web テスト (Layer 3) で検証する。');
    }

    /**
     * MUST: lookup は HTTP 200 を返し未知 ID は products 件数の減少として扱う。batch 超過は
     * HTTP 400 + request_too_large。これらは HTTP ステータスを伴うためコントローラ層で検証する。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/catalog/rest.md#L592
     */
    #[DoesNotPerformAssertions]
    public function testLookupHttpStatusSemanticsAreVerifiedAtControllerLayer(): void
    {
        self::markTestIncomplete('MUST: lookup は HTTP 200 を返し未知 ID は返却件数減で表現、batch 超過は HTTP 400 + request_too_large。HTTP ステータスを伴うためコントローラ (Layer 3) で検証する。');
    }

    private function sampleItem(): AgentCatalogItemDto
    {
        $variant = new AgentCatalogVariantDto(
            id: '101',
            title: 'Sample Variant',
            priceMinorUnits: 1999,
            currency: 'JPY',
            available: true,
            availabilityStatus: AvailabilityStatus::IN_STOCK,
        );

        return new AgentCatalogItemDto(
            id: '1',
            title: 'Sample Product',
            variants: [$variant],
        );
    }
}
