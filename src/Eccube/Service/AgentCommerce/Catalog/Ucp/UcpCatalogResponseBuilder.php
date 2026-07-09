<?php

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

namespace Eccube\Service\AgentCommerce\Catalog\Ucp;

use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;

/**
 * UCP Catalog の各レスポンス本文 (連想配列) を組み立てるビルダー.
 *
 * すべてのレスポンスは ucp ラッパー (ucp.json#/$defs/response_catalog_schema) を持つ。
 * response_catalog_schema は base を継承し version 必須・status 任意 (既定 success)。
 * search: {ucp, products[], pagination?, messages[]}
 * lookup: {ucp, products[], messages[]}
 * product: {ucp, product, messages[]}
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/ucp.json
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/catalog_search.json
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/catalog_lookup.json
 */
class UcpCatalogResponseBuilder
{
    /**
     * 本ビルダーが宣言する UCP バージョン (v2026-04-08).
     */
    public const UCP_VERSION = '2026-04-08';

    public function __construct(
        private readonly UcpCatalogProductSerializer $serializer,
    ) {
    }

    /**
     * POST /catalog/search のレスポンス本文を組み立てる.
     *
     * @param AgentCatalogItemDto[]      $items
     * @param array<string, mixed>|null  $pagination response pagination (has_next_page 必須)
     *
     * @return array<string, mixed>
     */
    public function buildSearchResponse(array $items, ?array $pagination = null): array
    {
        $response = [
            'ucp' => $this->buildUcpEnvelope(),
            'products' => $this->serializeItems($items),
            'messages' => [],
        ];

        if ($pagination !== null) {
            $response['pagination'] = $pagination;
        }

        return $response;
    }

    /**
     * POST /catalog/lookup のレスポンス本文を組み立てる.
     *
     * @param AgentCatalogItemDto[] $items
     *
     * @return array<string, mixed>
     */
    public function buildLookupResponse(array $items): array
    {
        return [
            'ucp' => $this->buildUcpEnvelope(),
            'products' => $this->serializeItems($items),
            'messages' => [],
        ];
    }

    /**
     * POST /catalog/product のレスポンス本文を組み立てる.
     *
     * @return array<string, mixed>
     */
    public function buildProductResponse(AgentCatalogItemDto $item): array
    {
        return [
            'ucp' => $this->buildUcpEnvelope(),
            'product' => $this->serializer->serialize($item),
            'messages' => [],
        ];
    }

    /**
     * ucp ラッパー (response_catalog_schema) を組み立てる.
     *
     * @return array{version: string, status: string}
     */
    private function buildUcpEnvelope(): array
    {
        return [
            'version' => self::UCP_VERSION,
            'status' => 'success',
        ];
    }

    /**
     * AgentCatalogItemDto の配列を UCP Product 連想配列の配列へ変換する.
     *
     * @param AgentCatalogItemDto[] $items
     *
     * @return array<int, array<string, mixed>>
     */
    private function serializeItems(array $items): array
    {
        return array_map(
            $this->serializer->serialize(...),
            array_values($items),
        );
    }
}
