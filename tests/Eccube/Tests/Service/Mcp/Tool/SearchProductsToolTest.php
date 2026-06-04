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

use Eccube\Service\Mcp\McpScope;
use Eccube\Service\Mcp\Tool\SearchProductsTool;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * `SearchProductsTool` の DB 結合テスト。
 *
 * Tool を container 経由で取得し、 scope の有無による拒否 / 許可と、 allow_list 経由出力を検証する。
 * Api44 が install + enable されている前提 (`core.api.allow_list` が container に居る)。
 */
final class SearchProductsToolTest extends EccubeTestCase
{
    private ?SearchProductsTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(SearchProductsTool::class);
        // 前のテストが残した token があれば消す。 デフォルトの「scope 無し」状態から開始する
        $this->tokenStorage()->setToken(null);
    }

    public function testThrowsWhenScopeIsAbsent(): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->tool->search();
    }

    public function testReturnsProductsWithScope(): void
    {
        $this->grantScope(McpScope::ROLE_PRODUCT_READ);
        $this->createProduct('mcp-search-001', 3);

        $result = $this->tool->search(limit: 50);

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertGreaterThanOrEqual(1, $result['total']);
        $this->assertSame(50, $result['limit']);
        $this->assertSame(0, $result['offset']);
    }

    public function testLimitClampedToUpperBound(): void
    {
        $this->grantScope(McpScope::ROLE_PRODUCT_READ);

        $result = $this->tool->search(limit: 500);

        $this->assertSame(200, $result['limit'], 'limit は 200 にクランプされる');
    }

    public function testLimitClampedToLowerBound(): void
    {
        $this->grantScope(McpScope::ROLE_PRODUCT_READ);

        $result = $this->tool->search(limit: 0);

        $this->assertSame(1, $result['limit'], 'limit は最小 1 にクランプされる');
    }

    public function testOffsetClampedToZero(): void
    {
        $this->grantScope(McpScope::ROLE_PRODUCT_READ);

        $result = $this->tool->search(limit: 1, offset: -5);

        $this->assertSame(0, $result['offset'], 'offset は最小 0 にクランプされる');
    }

    public function testItemFieldsAreSubsetOfAllowList(): void
    {
        $this->grantScope(McpScope::ROLE_PRODUCT_READ);
        $this->createProduct('mcp-allow-001', 1);

        $result = $this->tool->search(limit: 5);

        $this->assertNotEmpty($result['items']);

        // Api44 の core.api.allow_list で `Eccube\Entity\Product` に列挙されている項目
        $allowed = [
            'id', 'name', 'note', 'description_list', 'description_detail',
            'search_word', 'free_area', 'create_date', 'update_date',
            'ProductCategories', 'ProductClasses', 'ProductImage',
            'ProductTag', 'CustomerFavoriteProducts', 'Creator', 'Status',
        ];

        foreach ($result['items'] as $item) {
            foreach (array_keys($item) as $key) {
                $this->assertContains($key, $allowed, sprintf('出力フィールド "%s" は allow_list 外', $key));
            }
        }
    }

    private function grantScope(string $role): void
    {
        $member = $this->createMember();
        $token = new UsernamePasswordToken($member, 'admin', [$role]);
        $this->tokenStorage()->setToken($token);
    }

    private function tokenStorage(): TokenStorageInterface
    {
        $tokenStorage = static::getContainer()->get(TokenStorageInterface::class);
        $this->assertInstanceOf(TokenStorageInterface::class, $tokenStorage);

        return $tokenStorage;
    }
}
