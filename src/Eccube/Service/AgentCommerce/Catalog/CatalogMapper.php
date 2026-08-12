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

use Eccube\Entity\ClassCategory;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Service\AgentCommerce\MinorUnitConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * EC-CUBE の Product / ProductClass を、プロトコル非依存の AgentCatalogItemDto へ写すマッパー.
 *
 * addressable unit のマッピング方針:
 *   - 親 Product  = AgentCatalogItemDto
 *   - Variant     = ProductClass = AgentCatalogVariantDto
 *
 * 出力対象は公開中商品 (ProductStatus::DISPLAY_SHOW) かつ visible な ProductClass に限る。
 * 金額は店頭表示と同じ税込価格 (price02_inc_tax / price01_inc_tax) を MinorUnitConverter で
 * minor-unit 整数へ変換する (日本の総額表示。確定金額は CheckoutSession で再計算)。
 * `price` は price02 (販売価格)、`list_price` は price01 (通常価格) を割引前参照価格として出力するが、
 * **price01 > price02 のとき (実際に割引がある場合) のみ** list_price を出力する。barcodes は標準では出力しない
 * (Customize seam)。画像 URL / 商品詳細 URL は絶対 URL で出力する。
 *
 * ## EC-CUBE 状態 → 出力 / AvailabilityStatus の対応 (標準実装)
 *
 * (1) カタログ出力対象の判定 (出るか / 出ないか):
 *
 *   | EC-CUBE 状態                          | 判定                          | 出力              |
 *   |---------------------------------------|-------------------------------|-------------------|
 *   | ProductStatus::DISPLAY_SHOW (=1)      | isDisplayed() = true          | 含める            |
 *   | ProductStatus::DISPLAY_HIDE (=2)      | isDisplayed() = false         | 除外 (mapProduct=null) |
 *   | ProductStatus::DISPLAY_ABOLISHED (=3) | isDisplayed() = false         | 除外              |
 *   | ProductClass::isVisible() = false     | getDisplayProductClasses 除外 | その variant のみ除外 |
 *
 * (2) availability の判定 (出力対象 variant のみ。available と status は連動):
 *
 *   | stock_unlimited | stock (ProductClass::getStock) | available | availabilityStatus |
 *   |-----------------|--------------------------------|-----------|--------------------|
 *   | true (無制限)    | 値は無視                        | true      | in_stock           |
 *   | false           | > 0                            | true      | in_stock           |
 *   | false           | <= 0 または null                | false     | out_of_stock       |
 *
 * 標準で出力する AvailabilityStatus は in_stock / out_of_stock の 2 値のみ。
 * limited_stock / backorder / preorder / discontinued は仕様準拠のため enum に定義しているが
 * 標準実装では出力しない (Customize seam: 例 少量在庫→limited_stock、DISPLAY_ABOLISHED→discontinued)。
 * なお ProductStatus は Product 単位、stock_unlimited / stock は ProductClass 単位の判定である。
 *
 * @see AvailabilityStatus
 */
class CatalogMapper
{
    public function __construct(
        private readonly MinorUnitConverter $minorUnitConverter,
        private readonly UrlGeneratorInterface $urlGenerator,
        /** save_image アセットの base_path (framework.yaml の assets.packages.save_image と一致). */
        private readonly string $saveImageUrlPath = '/html/upload/save_image',
    ) {
    }

    /**
     * Product を AgentCatalogItemDto へ写す.
     *
     * 非公開商品、または公開中だが出力可能な ProductClass が 1 件も無い場合は null を返す
     * (variants[] は ACP / UCP いずれでも必須・非空のため)。
     */
    public function mapProduct(Product $product): ?AgentCatalogItemDto
    {
        if (!$this->isDisplayed($product)) {
            return null;
        }

        $variants = [];
        foreach ($this->getDisplayProductClasses($product) as $productClass) {
            $variants[] = $this->mapVariant($productClass);
        }

        if ($variants === []) {
            return null;
        }

        $id = $product->getId();
        if ($id === null) {
            return null;
        }

        return new AgentCatalogItemDto(
            id: (string) $id,
            title: $product->getName(),
            description: $this->mapDescription($product->getDescriptionDetail()),
            url: $this->generateProductUrl($id),
            media: $this->mapProductMedia($product),
            categories: [],
            variants: $variants,
        );
    }

    /**
     * ProductClass を AgentCatalogVariantDto へ写す.
     */
    public function mapVariant(ProductClass $productClass): AgentCatalogVariantDto
    {
        $currency = $productClass->getCurrencyCode();

        // 店頭 (商品詳細) と同じ税込価格を出力する (日本の総額表示)。
        // 金額は税込 getter (price02_inc_tax / price01_inc_tax) を使い、
        // 設定有無は税別 getter (?string) の null で判定する (定価は未設定があり得る)。
        $priceMinor = $productClass->getPrice02() !== null
            ? $this->minorUnitConverter->toMinorUnits($productClass->getPrice02IncTax(), $currency)
            : 0;

        // list_price は ACP/UCP では「割引前の参照価格 (pre-discount price)」。
        // price01 (通常価格) > price02 (販売価格) = 実際に割引がある場合のみ出力する
        // (price01 <= price02 では誤った割引表示になるため省く)。
        // ※ 店頭 (detail.twig) は price01 が設定されていれば常に通常価格を表示するが、
        //   feed の list_price は pre-discount セマンティクスのため割引時のみに限定する。
        $listPriceMinor = null;
        if ($productClass->getPrice01() !== null) {
            $candidate = $this->minorUnitConverter->toMinorUnits($productClass->getPrice01IncTax(), $currency);
            if ($candidate > $priceMinor) {
                $listPriceMinor = $candidate;
            }
        }

        $available = $this->isAvailable($productClass);
        $product = $productClass->getProduct();
        $productId = $product?->getId();

        return new AgentCatalogVariantDto(
            id: (string) $productClass->getId(),
            title: $product?->getName() ?? '',
            priceMinorUnits: $priceMinor,
            currency: $currency,
            available: $available,
            availabilityStatus: $this->resolveAvailabilityStatus($productClass),
            sku: $productClass->getCode(),
            listPriceMinorUnits: $listPriceMinor,
            url: $productId !== null ? $this->generateProductUrl($productId) : null,
            options: $this->mapOptions($productClass),
            barcodes: $this->mapBarcodes(),
            media: [],
        );
    }

    /**
     * 商品が公開中 (ProductStatus::DISPLAY_SHOW) かどうか.
     */
    private function isDisplayed(Product $product): bool
    {
        return $product->getStatus()?->getId() === ProductStatus::DISPLAY_SHOW;
    }

    /**
     * 出力対象 (visible) の ProductClass を返す.
     *
     * @return ProductClass[]
     */
    private function getDisplayProductClasses(Product $product): array
    {
        $classes = $product->getProductClasses();
        if ($classes === null) {
            return [];
        }

        $result = [];
        foreach ($classes as $productClass) {
            if ($productClass->isVisible()) {
                $result[] = $productClass;
            }
        }

        return $result;
    }

    /**
     * 在庫状況から available (購入可否) を判定する.
     *
     * 在庫無制限 -> true / 在庫あり (stock > 0) -> true / それ以外 -> false。
     */
    private function isAvailable(ProductClass $productClass): bool
    {
        if ($productClass->isStockUnlimited()) {
            return true;
        }

        return $this->stockQuantity($productClass) > 0;
    }

    /**
     * 在庫状況を AvailabilityStatus へ写す.
     *
     * 無制限 / 在庫あり -> in_stock、在庫なし -> out_of_stock。
     * (limited_stock の閾値は標準では設けない)
     */
    private function resolveAvailabilityStatus(ProductClass $productClass): AvailabilityStatus
    {
        if ($productClass->isStockUnlimited()) {
            return AvailabilityStatus::IN_STOCK;
        }

        return $this->stockQuantity($productClass) > 0
            ? AvailabilityStatus::IN_STOCK
            : AvailabilityStatus::OUT_OF_STOCK;
    }

    /**
     * 在庫数を int で取得する (getStock() は ?string のため正規化).
     */
    private function stockQuantity(ProductClass $productClass): int
    {
        $stock = $productClass->getStock();

        return $stock === null ? 0 : (int) $stock;
    }

    /**
     * 規格分類 (ClassCategory1 / ClassCategory2) を variant option へ写す.
     *
     * @return AgentCatalogOptionDto[]
     */
    private function mapOptions(ProductClass $productClass): array
    {
        $options = [];
        foreach ([$productClass->getClassCategory1(), $productClass->getClassCategory2()] as $classCategory) {
            $option = $this->mapOption($classCategory);
            if ($option !== null) {
                $options[] = $option;
            }
        }

        return $options;
    }

    private function mapOption(?ClassCategory $classCategory): ?AgentCatalogOptionDto
    {
        if ($classCategory === null) {
            return null;
        }

        $className = $classCategory->getClassName();
        if ($className === null) {
            return null;
        }

        return new AgentCatalogOptionDto(
            name: $className->getName(),
            value: $classCategory->getName(),
        );
    }

    /**
     * barcodes を写す. 標準では GTIN フィールドが無いため常に空配列 (Customize seam).
     *
     * @return AgentCatalogBarcodeDto[]
     */
    private function mapBarcodes(): array
    {
        return [];
    }

    /**
     * 商品説明テキストを description DTO へ写す.
     */
    private function mapDescription(?string $detail): ?AgentCatalogDescriptionDto
    {
        if ($detail === null || trim($detail) === '') {
            return null;
        }

        return new AgentCatalogDescriptionDto(html: $detail);
    }

    /**
     * 商品画像を media DTO へ写す (sort_no 昇順、絶対 URL).
     *
     * @return AgentCatalogMediaDto[]
     */
    private function mapProductMedia(Product $product): array
    {
        $images = $product->getProductImage()->toArray();
        usort($images, static fn (ProductImage $a, ProductImage $b): int => $a->getSortNo() <=> $b->getSortNo());

        $media = [];
        foreach ($images as $image) {
            $media[] = new AgentCatalogMediaDto(
                url: $this->generateImageUrl($image->getFileName()),
                type: 'image',
            );
        }

        return $media;
    }

    /**
     * 商品詳細ページの絶対 URL を生成する.
     */
    private function generateProductUrl(int $productId): string
    {
        return $this->urlGenerator->generate(
            'product_detail',
            ['id' => $productId],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }

    /**
     * 商品画像の絶対 URL を生成する.
     *
     * 画像はルーティング対象でない静的ファイルのため generateUrl() を使えない。
     * 代わりに RequestContext から scheme / host / port / baseUrl を取得して
     * generateUrl(ABSOLUTE_URL) と同等の絶対 URL を組み立てる。
     *
     * RequestContext は RouterListener が Request から構築するため、リバースプロキシ配下では
     * TRUSTED_PROXIES の設定により X-Forwarded-* が反映された外部 scheme/host/port を保持する
     * (本メソッドが個別に X-Forwarded-* を解釈するわけではなく、generateUrl と同じ経路で考慮される)。
     * CLI (Request 無し) では router.request_context.* の設定値が使われる。
     */
    private function generateImageUrl(string $fileName): string
    {
        $context = $this->urlGenerator->getContext();
        $scheme = $context->getScheme();
        $host = $context->getHost();

        $port = '';
        if ($scheme === 'http' && $context->getHttpPort() !== 80) {
            $port = ':'.$context->getHttpPort();
        } elseif ($scheme === 'https' && $context->getHttpsPort() !== 443) {
            $port = ':'.$context->getHttpsPort();
        }

        // baseUrl (サブディレクトリ設置時のアプリ接頭辞) を前置し、ルート URL と整合させる。
        $path = $context->getBaseUrl().'/'.trim($this->saveImageUrlPath, '/').'/'.ltrim($fileName, '/');

        return sprintf('%s://%s%s%s', $scheme, $host, $port, $path);
    }
}
