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

namespace Eccube\Service\Mcp;

use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;

/**
 * Product の価格・在庫を「min〜max」へ集約したサマリ断片を作る。
 *
 * 価格・在庫は Product 直下でなく ProductClass 単位に持つため、 可視 ProductClass を直接集計する。
 * Product::getStockMin/Max を使わないのは、 Product::_calc が price01 と違い在庫無制限クラスの
 * stock (=null) も null ガードせず母集団に push するため。 有限在庫と無制限が混在する商品で
 * min が null に化け「在庫が伏せられた / 欠損」と誤読される。 これを避けるため無制限クラスは
 * 有限 min/max の集計から除外し、 別途 `unlimited` フラグで表す。
 *
 * 集約元の ProductClass フィールドが allow_list で許可されている時だけ出力する (未許可なら null)。
 * これによりサマリ経路でも「allow_list に無いものは出さない」境界を保つ。
 */
final readonly class ProductPriceStockSummarizer
{
    public function __construct(
        private AllowListResolver $allowListResolver,
    ) {
    }

    /**
     * @return array{price: array{min: string|null, max: string|null}|null, stock: array{min: string|null, max: string|null, unlimited: bool}|null}
     */
    public function summarize(Product $product): array
    {
        $priceAllowed = $this->allowListResolver->isAllowed(ProductClass::class, 'price02');
        $stockAllowed = $this->allowListResolver->isAllowed(ProductClass::class, 'stock');
        $unlimitedAllowed = $this->allowListResolver->isAllowed(ProductClass::class, 'stock_unlimited');

        /** @var list<string> $prices */
        $prices = [];
        /** @var list<string> $finiteStocks */
        $finiteStocks = [];
        $hasUnlimited = false;

        foreach ($product->getProductClasses() ?? [] as $productClass) {
            // 非公開の規格 (規格ありの placeholder 等) は店頭在庫・価格に出ないため集計しない
            if (!$productClass->isVisible()) {
                continue;
            }
            $price = $productClass->getPrice02();
            if (null !== $price) {
                $prices[] = $price;
            }
            if ($productClass->isStockUnlimited()) {
                $hasUnlimited = true;
                continue;
            }
            $stock = $productClass->getStock();
            if (null !== $stock) {
                $finiteStocks[] = $stock;
            }
        }

        return [
            'price' => $priceAllowed
                ? ['min' => [] === $prices ? null : min($prices), 'max' => [] === $prices ? null : max($prices)]
                : null,
            'stock' => $stockAllowed
                ? [
                    'min' => [] === $finiteStocks ? null : min($finiteStocks),
                    'max' => [] === $finiteStocks ? null : max($finiteStocks),
                    'unlimited' => $unlimitedAllowed && $hasUnlimited,
                ]
                : null,
        ];
    }
}
