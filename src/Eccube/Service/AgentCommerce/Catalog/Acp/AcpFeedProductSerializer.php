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

namespace Eccube\Service\AgentCommerce\Catalog\Acp;

use Eccube\Service\AgentCommerce\Catalog\AgentCatalogDescriptionDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogMediaDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogVariantDto;

/**
 * プロトコル非依存の AgentCatalogItemDto を ACP Feed の Product 連想配列へ写すシリアライザ.
 *
 * 出力は schema.feed.json の $defs/Product に適合する (additionalProperties:false のため
 * null / 空の項目は一切出力しない)。金額は DTO が保持する minor unit 整数をそのまま使う。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json
 */
class AcpFeedProductSerializer
{
    /**
     * AgentCatalogItemDto を ACP Product 連想配列へ変換する.
     *
     * @return array<string, mixed>
     */
    public function serialize(AgentCatalogItemDto $product): array
    {
        // required: id, variants
        $result = [
            'id' => $product->id,
            'variants' => array_map(
                $this->serializeVariant(...),
                $product->variants,
            ),
        ];

        if ($product->title !== '') {
            $result['title'] = $product->title;
        }

        $description = $this->serializeDescription($product->description);
        if ($description !== null) {
            $result['description'] = $description;
        }

        if ($product->url !== null && $product->url !== '') {
            $result['url'] = $product->url;
        }

        $media = $this->serializeMediaList($product->media);
        if ($media !== []) {
            $result['media'] = $media;
        }

        return $result;
    }

    /**
     * AgentCatalogVariantDto を ACP Variant 連想配列へ変換する.
     *
     * @return array<string, mixed>
     */
    public function serializeVariant(AgentCatalogVariantDto $variant): array
    {
        // required: id, title
        $result = [
            'id' => $variant->id,
            'title' => $variant->title,
            'price' => [
                'amount' => $variant->priceMinorUnits,
                'currency' => $variant->currency,
            ],
            'availability' => [
                'available' => $variant->available,
                'status' => $variant->availabilityStatus->value,
            ],
        ];

        $description = $this->serializeDescription($variant->description);
        if ($description !== null) {
            $result['description'] = $description;
        }

        if ($variant->url !== null && $variant->url !== '') {
            $result['url'] = $variant->url;
        }

        if ($variant->listPriceMinorUnits !== null) {
            $result['list_price'] = [
                'amount' => $variant->listPriceMinorUnits,
                'currency' => $variant->currency,
            ];
        }

        $options = [];
        foreach ($variant->options as $option) {
            $options[] = [
                'name' => $option->name,
                'value' => $option->value,
            ];
        }
        if ($options !== []) {
            $result['variant_options'] = $options;
        }

        $barcodes = [];
        foreach ($variant->barcodes as $barcode) {
            $barcodes[] = [
                'type' => $barcode->type,
                'value' => $barcode->value,
            ];
        }
        if ($barcodes !== []) {
            $result['barcodes'] = $barcodes;
        }

        $media = $this->serializeMediaList($variant->media);
        if ($media !== []) {
            $result['media'] = $media;
        }

        return $result;
    }

    /**
     * @param AgentCatalogMediaDto[] $mediaList
     *
     * @return array<int, array<string, mixed>>
     */
    private function serializeMediaList(array $mediaList): array
    {
        $result = [];
        foreach ($mediaList as $media) {
            // required: type, url
            $item = [
                'type' => $media->type,
                'url' => $media->url,
            ];
            if ($media->altText !== null && $media->altText !== '') {
                $item['alt_text'] = $media->altText;
            }
            if ($media->width !== null) {
                $item['width'] = $media->width;
            }
            if ($media->height !== null) {
                $item['height'] = $media->height;
            }
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Description DTO を ACP Description 連想配列へ変換する.
     * 全フィールド null (= 空) の場合は null を返し、呼び出し側で出力対象から除外させる
     * (schema は minProperties:1)。
     *
     * @return array<string, string>|null
     */
    private function serializeDescription(?AgentCatalogDescriptionDto $description): ?array
    {
        if ($description === null || $description->isEmpty()) {
            return null;
        }

        $result = [];
        if ($description->plain !== null && $description->plain !== '') {
            $result['plain'] = $description->plain;
        }
        if ($description->html !== null && $description->html !== '') {
            $result['html'] = $description->html;
        }
        if ($description->markdown !== null && $description->markdown !== '') {
            $result['markdown'] = $description->markdown;
        }

        return $result === [] ? null : $result;
    }
}
