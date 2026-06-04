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
use Eccube\Service\Mcp\Tool\GetProductTool;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        $this->tokenStorage()->setToken(null);
    }

    public function testThrowsWhenScopeIsAbsent(): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->tool->get(id: 1);
    }

    public function testReturnsProductById(): void
    {
        $this->grantScope(McpScope::ROLE_PRODUCT_READ);
        $product = $this->createProduct('mcp-get-001', 2);

        $result = $this->tool->get(id: $product->getId());

        $this->assertArrayHasKey('id', $result);
        $this->assertSame($product->getId(), $result['id']);
        $this->assertArrayHasKey('name', $result);
        $this->assertSame('mcp-get-001', $result['name']);
    }

    public function testReturnsEmptyWhenNotFound(): void
    {
        $this->grantScope(McpScope::ROLE_PRODUCT_READ);

        $result = $this->tool->get(id: 99999999);

        $this->assertSame([], $result, '不在 ID は空配列を返す');
    }

    public function testReturnsEmptyWhenNeitherIdNorCode(): void
    {
        $this->grantScope(McpScope::ROLE_PRODUCT_READ);

        $result = $this->tool->get();

        $this->assertSame([], $result, '両方未指定は空配列');
    }

    public function testReturnsProductByCode(): void
    {
        $this->grantScope(McpScope::ROLE_PRODUCT_READ);
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
        $this->grantScope(McpScope::ROLE_PRODUCT_READ);
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
