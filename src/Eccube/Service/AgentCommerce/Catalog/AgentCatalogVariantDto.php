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

namespace Eccube\Service\AgentCommerce\Catalog;

/**
 * 商品バリエーション (addressable unit = ProductClass) をプロトコル非依存に保持する DTO.
 *
 * ACP Variant / UCP variant のいずれにも写し込めるよう、両者の共通項目を保持する。
 * 金額はいずれも minor-unit (最小通貨単位) の整数で持つ (MinorUnitConverter で変換済み)。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/types/variant.json
 */
final readonly class AgentCatalogVariantDto
{
    /**
     * @param AgentCatalogOptionDto[]  $options
     * @param AgentCatalogBarcodeDto[] $barcodes
     * @param AgentCatalogMediaDto[]   $media
     */
    public function __construct(
        public string $id,
        public string $title,
        public int $priceMinorUnits,
        public string $currency,
        public bool $available,
        public AvailabilityStatus $availabilityStatus,
        public ?string $sku = null,
        public ?int $listPriceMinorUnits = null,
        public ?AgentCatalogDescriptionDto $description = null,
        public ?string $url = null,
        public array $options = [],
        public array $barcodes = [],
        public array $media = [],
    ) {
    }
}
