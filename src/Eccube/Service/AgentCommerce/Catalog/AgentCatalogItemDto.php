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
 * カタログ商品 (addressable unit の親 = Product) をプロトコル非依存に保持する DTO.
 *
 * ACP Product / UCP product のいずれにも写し込めるよう、両者の共通項目を保持する。
 * variants[] は AgentCatalogVariantDto (= ProductClass) の配列。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/types/product.json
 */
final readonly class AgentCatalogItemDto
{
    /**
     * @param AgentCatalogMediaDto[]   $media
     * @param string[]                 $categories
     * @param AgentCatalogVariantDto[] $variants
     */
    public function __construct(
        public string $id,
        public string $title,
        public ?AgentCatalogDescriptionDto $description = null,
        public ?string $url = null,
        public array $media = [],
        public array $categories = [],
        public array $variants = [],
    ) {
    }
}
