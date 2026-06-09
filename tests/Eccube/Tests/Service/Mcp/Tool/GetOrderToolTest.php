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

use Eccube\Service\Mcp\Tool\GetOrderTool;
use Eccube\Tests\EccubeTestCase;

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
    }

    public function testReturnsOrderById(): void
    {
        $customer = $this->createCustomer('mcp-getorder@example.com');
        $order = $this->createOrder($customer);

        $result = $this->tool->get(id: $order->getId());

        $this->assertSame($order->getId(), $result['id']);
        $this->assertSame($order->getOrderNo(), $result['order_no']);
    }

    public function testReturnsOrderByOrderNo(): void
    {
        $customer = $this->createCustomer('mcp-getorder-no@example.com');
        $order = $this->createOrder($customer);

        $result = $this->tool->get(orderNo: $order->getOrderNo());

        $this->assertSame($order->getId(), $result['id']);
    }

    public function testReturnsEmptyWhenNotFound(): void
    {
        $result = $this->tool->get(id: 99999999);

        $this->assertSame(['found' => false], $result);
    }

    public function testReturnsEmptyWhenNeitherIdNorOrderNo(): void
    {
        $result = $this->tool->get();

        $this->assertSame(['found' => false], $result);
    }
}
