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

use Eccube\Service\Mcp\Tool\GetProductTool;
use Eccube\Tests\EccubeTestCase;

/**
 * `GetProductTool` の DB 結合テスト。 商品 ID / 商品コードによる取得と、 不在時の挙動を検証する。
 */
final class GetProductToolTest extends EccubeTestCase
{
    private ?GetProductTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(GetProductTool::class);
    }

    public function testReturnsProductById(): void
    {
        $product = $this->createProduct('mcp-get-001', 2);

        $result = $this->tool->get(id: $product->getId());

        $this->assertArrayHasKey('id', $result);
        $this->assertSame($product->getId(), $result['id']);
        $this->assertArrayHasKey('name', $result);
        $this->assertSame('mcp-get-001', $result['name']);
    }

    public function testReturnsEmptyWhenNotFound(): void
    {
        $result = $this->tool->get(id: 99999999);

        $this->assertSame(['found' => false], $result, '不在 ID は found:false を返す');
    }

    public function testReturnsEmptyWhenNeitherIdNorCode(): void
    {
        $result = $this->tool->get();

        $this->assertSame(['found' => false], $result, '両方未指定は found:false を返す');
    }

    public function testReturnsProductByCode(): void
    {
        $product = $this->createProduct('mcp-by-code', 1);
        $firstClass = $product->getProductClasses()->first();
        $this->assertNotFalse($firstClass);
        $code = $firstClass->getCode();
        $this->assertNotNull($code);

        $result = $this->tool->get(code: $code);

        $this->assertSame($product->getId(), $result['id']);
        $this->assertSame('mcp-by-code', $result['name']);
    }

    public function testOutputFieldsAreSubsetOfAllowList(): void
    {
        $product = $this->createProduct('mcp-allow', 1);

        $result = $this->tool->get(id: $product->getId());

        $allowed = [
            'id', 'name', 'note', 'description_list', 'description_detail',
            'search_word', 'free_area', 'create_date', 'update_date',
            'ProductCategories', 'ProductClasses', 'ProductImage',
            'ProductTag', 'CustomerFavoriteProducts', 'Creator', 'Status',
        ];

        foreach (array_keys($result) as $key) {
            $this->assertContains($key, $allowed, sprintf('出力フィールド "%s" は allow_list 外', $key));
        }
    }
}
