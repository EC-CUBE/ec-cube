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

namespace Eccube\Tests\Service\Mcp\Tool;

use Eccube\Entity\Product;
use Eccube\Service\Mcp\Tool\SearchProductsTool;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * `SearchProductsTool` の DB 結合テスト。
 *
 * Tool を container 経由で取得し、 scope の有無による拒否 / 許可と、 allow_list 経由出力を検証する。
 * Api44 が install + enable されている前提 (`core.api.allow_list` が container に居る)。
 */
#[Group('mcp')]
final class SearchProductsToolTest extends EccubeTestCase
{
    private ?SearchProductsTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(SearchProductsTool::class);
    }

    public function testReturnsProductsWithScope(): void
    {
        $product = $this->createProduct('mcp-search-001', 3);

        // 既存データに紛れて緑にならないよう、 作成した商品名で絞り込み、 その id が結果に出ることまで確認する。
        $result = $this->tool->search(keyword: 'mcp-search-001', limit: 50);

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertGreaterThanOrEqual(1, $result['total']);
        $this->assertContains(
            $product->getId(),
            array_column($result['items'], 'id'),
            '作成した商品が検索結果に含まれる',
        );
        $this->assertSame(50, $result['limit']);
        $this->assertSame(0, $result['offset']);
    }

    public function testLimitClampedToUpperBound(): void
    {
        $result = $this->tool->search(limit: 500);

        $this->assertSame(200, $result['limit'], 'limit は 200 にクランプされる');
    }

    public function testLimitClampedToLowerBound(): void
    {
        $result = $this->tool->search(limit: 0);

        $this->assertSame(1, $result['limit'], 'limit は最小 1 にクランプされる');
    }

    public function testOffsetClampedToZero(): void
    {
        $result = $this->tool->search(limit: 1, offset: -5);

        $this->assertSame(0, $result['offset'], 'offset は最小 0 にクランプされる');
    }

    public function testItemsReturnSummaryShape(): void
    {
        $this->createProduct('mcp-allow-001', 1);

        $result = $this->tool->search(limit: 5);

        $this->assertNotEmpty($result['items']);

        // サマリ射影のキーのみ: allow_list 由来 (id / name / Status) ＋ 集約値 (price / stock)。
        // description_detail 等の重量フィールドが出ないことをホワイトリストで担保する。
        $summaryKeys = ['id', 'name', 'Status', 'price', 'stock'];

        foreach ($result['items'] as $item) {
            foreach (array_keys($item) as $key) {
                $this->assertContains($key, $summaryKeys, sprintf('サマリ外のフィールド "%s" が出力された', $key));
            }
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('price', $item);
            $this->assertArrayHasKey('min', $item['price']);
            $this->assertArrayHasKey('max', $item['price']);
            $this->assertArrayHasKey('stock', $item);
            $this->assertArrayHasKey('min', $item['stock']);
            $this->assertArrayHasKey('max', $item['stock']);
            $this->assertArrayHasKey('unlimited', $item['stock']);
        }
    }

    /**
     * ② 回帰ガード: 在庫で絞り込んでも、 返る商品の在庫レンジは全表示規格ぶんのまま縮まない。
     * 商品単位の EXISTS で絞り、 fetch-join した規格を部分ハイドレートしないことを担保する。
     */
    public function testStockFilterDoesNotShrinkStockRange(): void
    {
        $name = 'mcp-stock-range-'.uniqid();
        $product = $this->makeProductWithStocks($name, [100, 900], hiddenStock: 0);

        $unfiltered = $this->findById($this->tool->search(keyword: $name, limit: 50), $product->getId());
        $this->assertNotNull($unfiltered, '絞り込み無しで作成商品が出る');

        // 900 の規格だけが満たす条件でも商品はヒットし、 在庫レンジは 100〜900 のまま。
        $filtered = $this->findById($this->tool->search(keyword: $name, stockMin: 800, limit: 50), $product->getId());
        $this->assertNotNull($filtered, 'stockMin=800 でも作成商品はヒットする (900 の規格が満たす)');

        $this->assertSame((int) $unfiltered['stock']['min'], (int) $filtered['stock']['min'], '絞り込み有無で stock.min が一致 (レンジが縮まない)');
        $this->assertSame((int) $unfiltered['stock']['max'], (int) $filtered['stock']['max'], '絞り込み有無で stock.max が一致');
        $this->assertSame(100, (int) $filtered['stock']['min']);
        $this->assertSame(900, (int) $filtered['stock']['max']);
    }

    /**
     * A: 単一 EXISTS のセマンティクス。 [stockMin, stockMax] に入る規格が 1 つも無い商品はヒットしない
     * (min と max を別々の規格が満たす「レンジ交差」ではヒットさせない)。
     */
    public function testStockRangeRequiresSingleClassWithinBounds(): void
    {
        $name = 'mcp-stock-cross-'.uniqid();
        // どの規格も [400,800] に入らない (300 と 900)。 交差解釈なら 900>=400 かつ 300<=800 でヒットしてしまう。
        $product = $this->makeProductWithStocks($name, [300, 900], hiddenStock: 0);

        $result = $this->tool->search(keyword: $name, stockMin: 400, stockMax: 800, limit: 50);

        $this->assertNull(
            $this->findById($result, $product->getId()),
            '[400,800] に入る規格が無い商品はヒットしない',
        );
    }

    /**
     * C: 在庫絞り込みは表示規格のみを母集団にする。 非表示規格だけが条件を満たす商品はヒットしない
     * (出力レンジ = ProductPriceStockSummarizer も非表示規格を除外するため母集団を揃える)。
     */
    public function testStockFilterIgnoresInvisibleClasses(): void
    {
        $name = 'mcp-stock-hidden-'.uniqid();
        // 表示規格は 100、 非表示規格だけが 900。 stockMin=800 は非表示規格しか満たさない。
        $product = $this->makeProductWithStocks($name, [100], hiddenStock: 900);

        $result = $this->tool->search(keyword: $name, stockMin: 800, limit: 50);

        $this->assertNull(
            $this->findById($result, $product->getId()),
            '非表示規格だけが在庫条件を満たす商品はヒットしない',
        );
    }

    /**
     * @param array{items: list<array<string, mixed>>} $result
     *
     * @return array<string, mixed>|null
     */
    private function findById(array $result, ?int $productId): ?array
    {
        foreach ($result['items'] as $item) {
            if ((int) ($item['id'] ?? 0) === $productId) {
                return $item;
            }
        }

        return null;
    }

    /**
     * 表示規格の在庫を $visibleStocks で、 (指定時) 非表示のデフォルト規格の在庫を $hiddenStock で固定した
     * 商品を作る。 Generator は全表示規格を在庫ランダムで作るため、 テスト内で上書きする。
     *
     * @param list<int> $visibleStocks 表示規格に順に割り当てる在庫
     */
    private function makeProductWithStocks(string $name, array $visibleStocks, ?int $hiddenStock = null): Product
    {
        $product = $this->createProduct($name, \count($visibleStocks));

        $visible = [];
        $hidden = [];
        foreach ($product->getProductClasses() ?? [] as $pc) {
            if ($pc->isVisible()) {
                $visible[] = $pc;
            } else {
                $hidden[] = $pc;
            }
        }
        $this->assertCount(\count($visibleStocks), $visible, 'createProduct が期待どおりの表示規格数を作る');

        foreach ($visible as $i => $pc) {
            $pc->setStockUnlimited(false);
            $pc->setStock((string) $visibleStocks[$i]);
        }
        if (null !== $hiddenStock) {
            $this->assertNotEmpty($hidden, '非表示のデフォルト規格が存在する');
            $hidden[0]->setStockUnlimited(false);
            $hidden[0]->setStock((string) $hiddenStock);
        }

        $this->entityManager->flush();

        return $product;
    }
}
