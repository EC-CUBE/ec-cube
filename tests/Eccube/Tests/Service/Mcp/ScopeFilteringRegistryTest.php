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

use Eccube\Service\Mcp\McpScope;
use Eccube\Service\Mcp\ScopeFilteringRegistry;
use Mcp\Capability\RegistryInterface;
use Mcp\Schema\Page;
use Mcp\Schema\Tool;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * tools/list を現トークンの scope で絞る {@see ScopeFilteringRegistry} の単体テスト。
 *
 * kernel/league を起こさずに、 装飾対象 (RegistryInterface)・認可 (AuthorizationCheckerInterface)・
 * トークン (TokenStorageInterface) を差し替えて getTools の絞り込みだけを検証する。
 * 実カーネル経路 (firewall → mcp-bundle) の担保は McpScopeEnforcementIntegrationTest が持つ。
 */
final class ScopeFilteringRegistryTest extends TestCase
{
    private function tool(string $name): Tool
    {
        return new Tool($name, null, ['type' => 'object'], null, null);
    }

    /**
     * @param array<string, Tool> $tools
     */
    private function registryReturning(array $tools): RegistryInterface&MockObject
    {
        $registry = $this->createMock(RegistryInterface::class);
        $registry->method('getTools')->willReturn(new Page($tools, null));

        return $registry;
    }

    public function testHidesToolsTheTokenCannotCall(): void
    {
        $inner = $this->registryReturning([
            'search_products' => $this->tool('search_products'),
            'search_orders' => $this->tool('search_orders'),
            'list_plugins' => $this->tool('list_plugins'),
        ]);

        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authChecker->method('isGranted')
            ->willReturnCallback(static fn ($role): bool => McpScope::ROLE_PRODUCT_READ === $role);

        $registry = new ScopeFilteringRegistry($inner, $authChecker, $this->tokenStorageWith(true));

        $names = $this->toolNames($registry->getTools());

        $this->assertSame(['search_products'], $names);
    }

    public function testFiltersBeforePagingSoVisibleToolsSurvivePageBoundary(): void
    {
        // 不可視 Tool が先頭 pageSize を占めても、 可視 Tool が空ページに落ちない (ページング前フィルタ)。
        // order 3 件 (不可視) → product 3 件 (可視) の順で返し、 product scope で 2 件ずつ全ページ走査する。
        $inner = $this->registryReturning([
            'search_orders' => $this->tool('search_orders'),
            'get_order' => $this->tool('get_order'),
            'get_shipping' => $this->tool('get_shipping'),
            'search_products' => $this->tool('search_products'),
            'get_product' => $this->tool('get_product'),
            'get_product_stock' => $this->tool('get_product_stock'),
        ]);

        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authChecker->method('isGranted')
            ->willReturnCallback(static fn ($role): bool => McpScope::ROLE_PRODUCT_READ === $role);

        $registry = new ScopeFilteringRegistry($inner, $authChecker, $this->tokenStorageWith(true));

        // 1 ページ目は空にならず可視 Tool が入る (旧実装なら order だけ切り出し → 空 references + 非 null カーソル)
        $this->assertNotEmpty($registry->getTools(2)->references);

        $collected = [];
        $cursor = null;
        do {
            $page = $registry->getTools(2, $cursor);
            $collected = array_merge($collected, $this->toolNames($page));
            $cursor = $page->nextCursor;
        } while (null !== $cursor);

        // 可視 product 3 件が漏れなく得られ、 不可視 order は一切出ない
        $this->assertSame(['search_products', 'get_product', 'get_product_stock'], $collected);
    }

    public function testReturnsAllToolsWhenNoToken(): void
    {
        $page = new Page(['search_products' => $this->tool('search_products'), 'search_orders' => $this->tool('search_orders')], null);
        $inner = $this->createMock(RegistryInterface::class);
        $inner->method('getTools')->willReturn($page);

        // トークンが無い経路 (CLI 等) では認可判定を一切呼ばず素通しする
        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authChecker->expects($this->never())->method('isGranted');

        $registry = new ScopeFilteringRegistry($inner, $authChecker, $this->tokenStorageWith(false));

        $this->assertSame($page, $registry->getTools());
    }

    public function testHidesToolWithNoScopeMapping(): void
    {
        // 中央マップ未登録の Tool は call 時 fail-closed deny なので、 一覧からも隠す
        $inner = $this->registryReturning([
            'search_products' => $this->tool('search_products'),
            'unknown_tool' => $this->tool('unknown_tool'),
        ]);

        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authChecker->method('isGranted')->willReturn(true);

        $registry = new ScopeFilteringRegistry($inner, $authChecker, $this->tokenStorageWith(true));

        $this->assertSame(['search_products'], $this->toolNames($registry->getTools()));
    }

    public function testDelegatesNonToolMethods(): void
    {
        $inner = $this->createMock(RegistryInterface::class);
        $inner->expects($this->once())->method('hasTools')->willReturn(true);

        $registry = new ScopeFilteringRegistry(
            $inner,
            $this->createStub(AuthorizationCheckerInterface::class),
            $this->createStub(TokenStorageInterface::class),
        );

        $this->assertTrue($registry->hasTools());
    }

    private function tokenStorageWith(bool $hasToken): TokenStorageInterface
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($hasToken ? $this->createMock(TokenInterface::class) : null);

        return $tokenStorage;
    }

    /**
     * @return list<string>
     */
    private function toolNames(Page $page): array
    {
        return array_map(static fn (Tool $tool): string => $tool->name, array_values($page->references));
    }
}
