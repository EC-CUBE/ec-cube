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

use Eccube\Service\AgentCommerce\Catalog\AgentCatalogDescriptionDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogMediaDto;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogVariantDto;

/**
 * プロトコル非依存の AgentCatalogItemDto を UCP Catalog の Product 連想配列へ写すシリアライザ.
 *
 * 出力は UCP の types/product.json / types/variant.json に適合する。
 * Product 必須: id, title, description, price_range, variants。
 * Variant 必須: id, title, description, price。
 * price_range は variants の price から min / max を算出する。金額は DTO が保持する
 * minor unit 整数をそのまま使う。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/types/product.json
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/types/variant.json
 */
class UcpCatalogProductSerializer
{
    /**
     * AgentCatalogItemDto を UCP Product 連想配列へ変換する.
     *
     * @return array<string, mixed>
     */
    public function serialize(AgentCatalogItemDto $product): array
    {
        $variants = array_map(
            $this->serializeVariant(...),
            $product->variants,
        );

        // required: id, title, description, price_range, variants
        $result = [
            'id' => $product->id,
            'title' => $product->title,
            'description' => $this->serializeDescription($product->description),
            'price_range' => $this->buildPriceRange($product->variants),
            'variants' => $variants,
        ];

        if ($product->url !== null && $product->url !== '') {
            $result['url'] = $product->url;
        }

        if ($product->categories !== []) {
            // UCP の categories は category.json に適合する必要があり、required は {value}。
            $result['categories'] = array_map(
                static fn (string $name): array => ['value' => $name],
                $product->categories,
            );
        }

        $media = $this->serializeMediaList($product->media);
        if ($media !== []) {
            $result['media'] = $media;
        }

        return $result;
    }

    /**
     * AgentCatalogVariantDto を UCP Variant 連想配列へ変換する.
     *
     * @return array<string, mixed>
     */
    public function serializeVariant(AgentCatalogVariantDto $variant): array
    {
        // required: id, title, description, price
        $result = [
            'id' => $variant->id,
            'title' => $variant->title,
            'description' => $this->serializeDescription($variant->description),
            'price' => [
                'amount' => $variant->priceMinorUnits,
                'currency' => $variant->currency,
            ],
            'availability' => [
                'available' => $variant->available,
                'status' => $variant->availabilityStatus->value,
            ],
        ];

        if ($variant->sku !== null && $variant->sku !== '') {
            $result['sku'] = $variant->sku;
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
            // UCP の Variant.options は selected_option.json に適合する必要があり、
            // required は {name, label}。AgentCatalogOptionDto の value を label へ写す。
            $options[] = [
                'name' => $option->name,
                'label' => $option->value,
            ];
        }
        if ($options !== []) {
            $result['options'] = $options;
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
     * variants[] の price から price_range (min / max) を算出する.
     *
     * variants が空の場合 (理論上 mapProduct が null を返すため発生しない) は
     * amount 0 の縮退 range を返す。currency は先頭 variant のものを採用する。
     *
     * @param AgentCatalogVariantDto[] $variants
     *
     * @return array{min: array{amount: int, currency: string}, max: array{amount: int, currency: string}}
     */
    private function buildPriceRange(array $variants): array
    {
        if ($variants === []) {
            $zero = ['amount' => 0, 'currency' => 'JPY'];

            return ['min' => $zero, 'max' => $zero];
        }

        $currency = $variants[0]->currency;
        $min = $variants[0]->priceMinorUnits;
        $max = $variants[0]->priceMinorUnits;
        foreach ($variants as $variant) {
            $min = min($min, $variant->priceMinorUnits);
            $max = max($max, $variant->priceMinorUnits);
        }

        return [
            'min' => ['amount' => $min, 'currency' => $currency],
            'max' => ['amount' => $max, 'currency' => $currency],
        ];
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
     * Description DTO を UCP Description 連想配列へ変換する.
     *
     * UCP では Product / Variant の description は必須かつ minProperties:1。
     * DTO が空 (全 null) の場合でも plain を空文字で出力すると minProperties:1 を満たさない
     * ため、最低 1 形式 (plain) を保証する。html / markdown も値があれば付与する。
     *
     * @return array<string, string>
     */
    private function serializeDescription(?AgentCatalogDescriptionDto $description): array
    {
        if ($description === null || $description->isEmpty()) {
            return ['plain' => ''];
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

        return $result === [] ? ['plain' => ''] : $result;
    }
}
