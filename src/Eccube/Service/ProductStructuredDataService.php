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

namespace Eccube\Service;

use Eccube\Entity\Category;
use Eccube\Entity\Product;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\Asset\Packages;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * 商品詳細ページの構造化データ（JSON-LD / schema.org Product）を組み立てる Service.
 *
 * 価格分岐・カテゴリパス整形・StrikethroughPrice 判定などの業務ロジックをここに集約し、
 * 戻り値の連想配列を単体テストで直接アサートできるようにする。
 * リクエスト依存値（絶対URL・通貨コード）は引数で受け取り HTTP 非依存に保つ。
 */
class ProductStructuredDataService
{
    /**
     * 画像が無い場合のフォールバック画像ファイル名.
     */
    private const NO_IMAGE_FILE = 'no_image_product.png';

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Packages $packages,
    ) {
    }

    /**
     * 商品詳細ページの JSON-LD 構造（連想配列）を組み立てて返す.
     *
     * @param string $baseUrl    スキーム込みホスト（例: https://example.com）
     * @param string $productUrl 商品詳細ページの絶対URL
     * @param string $currency   通貨コード（例: JPY）
     *
     * @return array<string, mixed>
     */
    public function createProductJsonLd(Product $Product, string $baseUrl, string $productUrl, string $currency): array
    {
        // 価格の min/max を算出しておく
        $Product->_calc();

        $data = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $Product->getName(),
            'image' => $this->buildImages($Product, $baseUrl),
        ];

        $description = $this->buildDescription($Product);
        if ($description !== '') {
            $data['description'] = $description;
        }

        $sku = $Product->getCodeMin();
        if ($sku !== null && $sku !== '') {
            $data['sku'] = $sku;
        }

        $category = $this->buildCategory($Product);
        if ($category !== null) {
            $data['category'] = $category;
        }

        $offers = $this->buildOffers($Product, $productUrl, $currency);
        if ($offers !== null) {
            $data['offers'] = $offers;
        }

        // プラグイン等が aggregateRating / review 等を配列へ注入するための拡張点.
        $event = new EventArgs(
            [
                'json_ld' => $data,
                'Product' => $Product,
            ]
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_PRODUCT_DETAIL_JSON_LD);

        /** @var array<string, mixed> $data */
        $data = $event->getArgument('json_ld');

        return $data;
    }

    /**
     * 商品画像の絶対URL配列を組み立てる（画像が無ければ no_image を返す）.
     *
     * @return list<string>
     */
    private function buildImages(Product $Product, string $baseUrl): array
    {
        $images = [];
        foreach ($Product->getProductImage() as $ProductImage) {
            $images[] = $baseUrl.$this->packages->getUrl((string) $ProductImage, 'save_image');
        }

        if ($images === []) {
            $images[] = $baseUrl.$this->packages->getUrl(self::NO_IMAGE_FILE, 'save_image');
        }

        return $images;
    }

    /**
     * 説明文を HTML 除去・空白正規化して 300 文字に丸める.
     */
    private function buildDescription(Product $Product): string
    {
        $description = $Product->getDescriptionList();
        if ($description === null || $description === '') {
            $description = $Product->getDescriptionDetail();
        }
        if ($description === null || $description === '') {
            return '';
        }

        $description = strip_tags($description);
        $description = preg_replace('/\s+/u', ' ', $description) ?? $description;
        $description = trim($description);

        return mb_substr($description, 0, 300);
    }

    /**
     * 店舗カテゴリのパス（親カテゴリを ">" 連結）を返す.
     * カテゴリが1件なら文字列、複数なら文字列配列、無ければ null.
     *
     * @return string|list<string>|null
     */
    private function buildCategory(Product $Product): string|array|null
    {
        $paths = [];
        foreach ($Product->getProductCategories() as $ProductCategory) {
            $Category = $ProductCategory->getCategory();
            if ($Category === null) {
                continue;
            }
            $names = [];
            foreach ($Category->getPath() as $ancestor) {
                /* @var Category $ancestor */
                $names[] = $ancestor->getName();
            }
            $path = implode(' > ', $names);
            if ($path !== '') {
                $paths[] = $path;
            }
        }

        $paths = array_values(array_unique($paths));
        if ($paths === []) {
            return null;
        }

        return count($paths) === 1 ? $paths[0] : $paths;
    }

    /**
     * offers 構造を組み立てる.
     * 単一価格 → Offer（条件付きで StrikethroughPrice）／価格帯 → AggregateOffer.
     * 価格が取得できない場合は null（offers を出力しない）.
     *
     * @return array<string, mixed>|null
     */
    private function buildOffers(Product $Product, string $productUrl, string $currency): ?array
    {
        $priceMin = $Product->getPrice02IncTaxMin();
        if ($priceMin === null) {
            return null;
        }
        $priceMax = $Product->getPrice02IncTaxMax();
        $availability = $Product->getStockFind() ? 'InStock' : 'OutOfStock';

        // 価格帯（規格で価格が異なる）商品は AggregateOffer
        if ($priceMax !== null && (float) $priceMin !== (float) $priceMax) {
            return [
                '@type' => 'AggregateOffer',
                'url' => $productUrl,
                'priceCurrency' => $currency,
                'lowPrice' => $priceMin,
                'highPrice' => $priceMax,
                'offerCount' => $this->countVisibleProductClasses($Product),
                'availability' => $availability,
                'itemCondition' => 'NewCondition',
            ];
        }

        $offers = [
            '@type' => 'Offer',
            'url' => $productUrl,
            'priceCurrency' => $currency,
            'price' => $priceMin,
            'availability' => $availability,
            'itemCondition' => 'NewCondition',
        ];

        // 通常価格(price01) > 販売価格(price02) のときのみ取消線価格を付与（税込同士で比較）
        $listPrice = $Product->getPrice01IncTaxMin();
        if ($listPrice !== null && (float) $listPrice > 0 && (float) $listPrice > (float) $priceMin) {
            $offers['priceSpecification'] = [
                '@type' => 'UnitPriceSpecification',
                'priceType' => 'StrikethroughPrice',
                'price' => $listPrice,
                'priceCurrency' => $currency,
            ];
        }

        return $offers;
    }

    private function countVisibleProductClasses(Product $Product): int
    {
        $count = 0;
        foreach ($Product->getProductClasses() as $ProductClass) {
            if ($ProductClass->isVisible()) {
                ++$count;
            }
        }

        return $count;
    }
}
