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

use Eccube\Service\Mcp\Tool\GetProductStockTool;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * `GetProductStockTool` の DB 結合テスト。 規格あり / 規格なし / 在庫無制限 / 不在の各経路を検証する。
 */
#[Group('mcp')]
final class GetProductStockToolTest extends EccubeTestCase
{
    private ?GetProductStockTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(GetProductStockTool::class);
    }

    public function testReturnsStockForProductWithMultipleClasses(): void
    {
        $product = $this->createProduct('mcp-stock-multi', 3);

        $result = $this->tool->get(productId: $product->getId());

        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertSame(3, $result['summary']['total_classes']);
        $this->assertCount(3, $result['items']);
        $this->assertArrayHasKey('total_stock', $result['summary']);
        $this->assertArrayHasKey('stock_unlimited', $result['summary']);
    }

    public function testReturnsStockForProductWithoutClasses(): void
    {
        // 規格数 0 を指定しても代表 ProductClass が 1 つ生成される
        $product = $this->createProduct('mcp-stock-single', 0);

        $result = $this->tool->get(productId: $product->getId());

        $this->assertSame(1, $result['summary']['total_classes']);
        $this->assertCount(1, $result['items']);
    }

    public function testReturnsEmptyForUnknownProduct(): void
    {
        $result = $this->tool->get(productId: 99999999);

        $this->assertSame(0, $result['summary']['total_classes']);
        $this->assertSame([], $result['items']);
    }

    public function testStockUnlimitedReflectedInSummary(): void
    {
        $product = $this->createProduct('mcp-stock-unlimited', 2);

        // 1 つの規格を在庫無制限に設定
        $first = $product->getProductClasses()->first();
        $this->assertNotFalse($first);
        $first->setStockUnlimited(true);
        $this->entityManager->flush();

        $result = $this->tool->get(productId: $product->getId());

        $this->assertTrue($result['summary']['stock_unlimited'], '無制限規格が 1 つあれば summary に反映');
        $this->assertNull($result['summary']['total_stock'], '無制限規格があるとき total_stock は null');
    }

    public function testItemFieldsAreSubsetOfAllowList(): void
    {
        $product = $this->createProduct('mcp-stock-allow', 1);

        $result = $this->tool->get(productId: $product->getId());

        // ProductClass の allow_list (api44 services.yaml より)
        $allowed = [
            'id', 'code', 'stock', 'stock_unlimited', 'sale_limit',
            'price01', 'price02', 'delivery_fee', 'visible', 'create_date',
            'update_date', 'currency_code', 'point_rate',
            'ProductStock', 'TaxRule', 'Product', 'SaleType',
            'ClassCategory1', 'ClassCategory2', 'DeliveryDuration', 'Creator',
        ];

        $this->assertNotEmpty($result['items']);
        foreach ($result['items'] as $item) {
            foreach (array_keys($item) as $key) {
                $this->assertContains($key, $allowed, sprintf('出力フィールド "%s" は ProductClass allow_list 外', $key));
            }
        }
    }
}
