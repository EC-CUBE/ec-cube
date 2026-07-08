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
}
