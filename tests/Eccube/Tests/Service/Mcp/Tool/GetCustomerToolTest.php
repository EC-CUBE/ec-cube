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

use Eccube\Service\Mcp\Tool\GetCustomerTool;
use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Fixture\Generator;
use PHPUnit\Framework\Attributes\Group;

/**
 * `GetCustomerTool` の DB 結合テスト。
 */
#[Group('mcp')]
final class GetCustomerToolTest extends EccubeTestCase
{
    private ?GetCustomerTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(GetCustomerTool::class);
    }

    public function testReturnsCustomerById(): void
    {
        $customer = $this->createCustomer('mcp-getcustomer@example.com');

        $result = $this->tool->get(id: $customer->getId());

        $this->assertSame($customer->getId(), $result['id']);
        $this->assertSame('mcp-getcustomer@example.com', $result['email']);
    }

    public function testReturnsEmptyWhenNotFound(): void
    {
        $result = $this->tool->get(id: 99999999);

        $this->assertSame(['found' => false], $result);
    }

    public function testCollapsesOrdersToIdSummary(): void
    {
        $customer = $this->createCustomer('mcp-getcustomer-orders@example.com');
        $generator = static::getContainer()->get(Generator::class);
        $this->assertInstanceOf(Generator::class, $generator);
        $order = $generator->createOrder($customer);
        $customerId = $customer->getId();
        $orderId = $order->getId();

        // createOrder は Customer.Orders コレクションを in-memory で更新しないため、
        // 実リクエストと同条件で DB から読み直させる
        $this->entityManager->clear();

        $result = $this->tool->get(id: $customerId);

        $this->assertArrayHasKey('Orders', $result);
        $this->assertNotEmpty($result['Orders']);
        foreach ($result['Orders'] as $orderSummary) {
            // 縮退: 各注文は id のみ (order_no や明細等の full フィールドは出ない)
            $this->assertSame(['id'], array_keys($orderSummary));
        }
        $this->assertContains($orderId, array_column($result['Orders'], 'id'));
    }
}
