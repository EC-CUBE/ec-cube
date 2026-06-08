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
use Eccube\Service\Mcp\Tool\GetOrderTool;
use Eccube\Tests\EccubeTestCase;
use Mcp\Exception\ToolCallException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * `GetOrderTool` の DB 結合テスト。 ID / 注文番号取得と不在時の挙動を検証する。
 */
final class GetOrderToolTest extends EccubeTestCase
{
    private ?GetOrderTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(GetOrderTool::class);
        $this->tokenStorage()->setToken(null);
    }

    public function testThrowsWhenScopeIsAbsent(): void
    {
        $this->expectException(ToolCallException::class);
        $this->tool->get(id: 1);
    }

    public function testReturnsOrderById(): void
    {
        $this->grantScope(McpScope::ROLE_ORDER_READ);
        $customer = $this->createCustomer('mcp-getorder@example.com');
        $order = $this->createOrder($customer);

        $result = $this->tool->get(id: $order->getId());

        $this->assertSame($order->getId(), $result['id']);
        $this->assertSame($order->getOrderNo(), $result['order_no']);
    }

    public function testReturnsOrderByOrderNo(): void
    {
        $this->grantScope(McpScope::ROLE_ORDER_READ);
        $customer = $this->createCustomer('mcp-getorder-no@example.com');
        $order = $this->createOrder($customer);

        $result = $this->tool->get(orderNo: $order->getOrderNo());

        $this->assertSame($order->getId(), $result['id']);
    }

    public function testReturnsEmptyWhenNotFound(): void
    {
        $this->grantScope(McpScope::ROLE_ORDER_READ);

        $result = $this->tool->get(id: 99999999);

        $this->assertSame(['found' => false], $result);
    }

    public function testReturnsEmptyWhenNeitherIdNorOrderNo(): void
    {
        $this->grantScope(McpScope::ROLE_ORDER_READ);

        $result = $this->tool->get();

        $this->assertSame(['found' => false], $result);
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
