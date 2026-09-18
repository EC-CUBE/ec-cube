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

namespace Eccube\Tests\Service\Mcp;

use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Service\Mcp\AllowListResolver;
use Eccube\Service\Mcp\ProductPriceStockSummarizer;
use PHPUnit\Framework\TestCase;

/**
 * `ProductPriceStockSummarizer` のユニット検証。 DB 不要。
 *
 * 可視 ProductClass を組み立て、 min〜max のまとめ方・無制限クラスの扱い・非公開規格の除外・
 * allow_list による fail-closed を確認する。
 */
final class ProductPriceStockSummarizerTest extends TestCase
{
    public function testAggregatesFinitePriceAndStockRange(): void
    {
        $summarizer = $this->summarizerWith([
            ProductClass::class => ['price02', 'stock', 'stock_unlimited'],
        ]);
        $product = $this->productWith([
            ['price02' => '1000', 'stock' => '5'],
            ['price02' => '1500', 'stock' => '30'],
        ]);

        $this->assertSame(
            [
                'price' => ['min' => '1000', 'max' => '1500'],
                'stock' => ['min' => '5', 'max' => '30', 'unlimited' => false],
            ],
            $summarizer->summarize($product),
        );
    }

    public function testUnlimitedClassExcludedFromStockRange(): void
    {
        // HIGH 回帰: 有限在庫(5) と無制限が混在しても stock.min が null に化けず、
        // 有限クラスだけで min/max を出し、 unlimited フラグで無制限を示す。
        $summarizer = $this->summarizerWith([
            ProductClass::class => ['price02', 'stock', 'stock_unlimited'],
        ]);
        $product = $this->productWith([
            ['price02' => '1000', 'stock' => '5'],
            ['price02' => '1200', 'stock' => null, 'unlimited' => true],
        ]);

        $this->assertSame(
            [
                'price' => ['min' => '1000', 'max' => '1200'],
                'stock' => ['min' => '5', 'max' => '5', 'unlimited' => true],
            ],
            $summarizer->summarize($product),
        );
    }

    public function testAllUnlimitedYieldsNullStockRange(): void
    {
        $summarizer = $this->summarizerWith([
            ProductClass::class => ['price02', 'stock', 'stock_unlimited'],
        ]);
        $product = $this->productWith([
            ['price02' => '800', 'stock' => null, 'unlimited' => true],
        ]);

        $this->assertSame(
            ['min' => null, 'max' => null, 'unlimited' => true],
            $summarizer->summarize($product)['stock'],
        );
    }

    public function testInvisibleClassExcluded(): void
    {
        $summarizer = $this->summarizerWith([
            ProductClass::class => ['price02', 'stock', 'stock_unlimited'],
        ]);
        $product = $this->productWith([
            ['price02' => '1000', 'stock' => '5', 'visible' => true],
            ['price02' => '9999', 'stock' => '999', 'visible' => false],
        ]);

        $this->assertSame(
            [
                'price' => ['min' => '1000', 'max' => '1000'],
                'stock' => ['min' => '5', 'max' => '5', 'unlimited' => false],
            ],
            $summarizer->summarize($product),
        );
    }

    public function testPriceNullWhenPrice02NotAllowed(): void
    {
        // price02 が allow_list に無ければ price は出さない (fail-closed)。 stock は許可されていれば出す。
        $summarizer = $this->summarizerWith([
            ProductClass::class => ['stock'],
        ]);
        $product = $this->productWith([['price02' => '1000', 'stock' => '5']]);

        $result = $summarizer->summarize($product);

        $this->assertNull($result['price']);
        $this->assertSame(['min' => '5', 'max' => '5', 'unlimited' => false], $result['stock']);
    }

    public function testUnlimitedFalseWhenStockUnlimitedNotAllowed(): void
    {
        // stock は許可だが stock_unlimited が未許可なら unlimited は常に false 扱い
        $summarizer = $this->summarizerWith([
            ProductClass::class => ['stock'],
        ]);
        $product = $this->productWith([['stock' => null, 'unlimited' => true]]);

        $this->assertFalse($summarizer->summarize($product)['stock']['unlimited']);
    }

    /**
     * @param array<string, list<string>> $allowMap
     */
    private function summarizerWith(array $allowMap): ProductPriceStockSummarizer
    {
        return new ProductPriceStockSummarizer(new AllowListResolver([new FakeAllowList($allowMap)]));
    }

    /**
     * @param list<array{price02?: string|null, stock?: string|null, unlimited?: bool, visible?: bool}> $classes
     */
    private function productWith(array $classes): Product
    {
        $product = new Product();
        foreach ($classes as $spec) {
            $productClass = new ProductClass();
            $productClass->setVisible($spec['visible'] ?? true);
            $productClass->setPrice02($spec['price02'] ?? null);
            $productClass->setStock($spec['stock'] ?? null);
            $productClass->setStockUnlimited($spec['unlimited'] ?? false);
            $product->addProductClass($productClass);
        }

        return $product;
    }
}
