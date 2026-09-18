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

namespace Eccube\Tests\Service\Mcp\Contract;

use Eccube\Service\Mcp\Tool\GetCustomerOrdersTool;
use Eccube\Service\Mcp\Tool\GetCustomerTool;
use Eccube\Service\Mcp\Tool\GetOrderTool;
use Eccube\Service\Mcp\Tool\GetPluginTool;
use Eccube\Service\Mcp\Tool\GetProductStockTool;
use Eccube\Service\Mcp\Tool\GetProductTool;
use Eccube\Service\Mcp\Tool\GetShippingTool;
use Eccube\Service\Mcp\Tool\ListPluginsTool;
use Eccube\Service\Mcp\Tool\SearchCustomersTool;
use Eccube\Service\Mcp\Tool\SearchOrdersTool;
use Eccube\Service\Mcp\Tool\SearchProductsTool;
use Eccube\Tests\EccubeTestCase;
use Mcp\Capability\Attribute\McpTool;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * 受入基準 §8 #1 「tools/list が全 11 ツールを返す」 の契約テスト。
 *
 * mcp-bundle の内部 Registry は container に publish されていないため、 ここでは
 * - 11 個の Tool class が **DI コンテナに登録されている** (= mcp.tool タグで discovery 対象になる)
 * - 各 class に `#[McpTool]` 属性が付与され、 name が期待値と一致する
 *
 * の 2 点を確認することで、 mcp-bundle が discovery で集めて `tools/list` に出す前提条件を担保する。
 */
final class ToolsListContractTest extends EccubeTestCase
{
    /**
     * @return array<string, list<string>> [tool name => [class名]]
     */
    public static function provideExpectedTools(): array
    {
        return [
            'search_products' => [SearchProductsTool::class],
            'get_product' => [GetProductTool::class],
            'get_product_stock' => [GetProductStockTool::class],
            'search_orders' => [SearchOrdersTool::class],
            'get_order' => [GetOrderTool::class],
            'get_shipping' => [GetShippingTool::class],
            'search_customers' => [SearchCustomersTool::class],
            'get_customer' => [GetCustomerTool::class],
            'get_customer_orders' => [GetCustomerOrdersTool::class],
            'list_plugins' => [ListPluginsTool::class],
            'get_plugin' => [GetPluginTool::class],
        ];
    }

    public function testElevenToolsAreRegistered(): void
    {
        $this->assertCount(11, self::provideExpectedTools(), '設計 §8 #1 「全 11 ツール」 と一致');
    }

    #[DataProvider(methodName: 'provideExpectedTools')]
    public function testEachToolIsContainerRegistered(string $toolClass): void
    {
        $instance = static::getContainer()->get($toolClass);
        $this->assertNotNull($instance, "{$toolClass} が DI コンテナに登録されている");
        $this->assertInstanceOf($toolClass, $instance);
    }

    #[DataProvider(methodName: 'provideExpectedTools')]
    public function testEachToolHasMcpToolAttribute(string $toolClass): void
    {
        $expectedName = $this->resolveExpectedName($toolClass);

        $reflection = new \ReflectionClass($toolClass);
        $attributes = [];
        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes(McpTool::class) as $attr) {
                /** @var McpTool $instance */
                $instance = $attr->newInstance();
                $attributes[] = $instance->name;
            }
        }

        $this->assertCount(1, $attributes, "{$toolClass} は #[McpTool] を 1 つ持つ");
        $this->assertSame($expectedName, $attributes[0], "{$toolClass} の Tool name が期待値と一致");
    }

    private function resolveExpectedName(string $toolClass): string
    {
        foreach (self::provideExpectedTools() as $name => [$class]) {
            if ($class === $toolClass) {
                return $name;
            }
        }

        throw new \LogicException("Unknown tool class: {$toolClass}");
    }
}
