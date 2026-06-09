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

use Eccube\Service\Mcp\Tool\GetCustomerOrdersTool;
use Eccube\Tests\EccubeTestCase;

/**
 * `GetCustomerOrdersTool` の DB 結合テスト。
 */
final class GetCustomerOrdersToolTest extends EccubeTestCase
{
    private ?GetCustomerOrdersTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(GetCustomerOrdersTool::class);
    }

    public function testReturnsCustomerOrders(): void
    {
        $customer = $this->createCustomer('mcp-customer-orders@example.com');
        $this->createOrder($customer);
        $this->createOrder($customer);

        $result = $this->tool->get(customerId: $customer->getId(), limit: 50);

        $this->assertSame($customer->getId(), $result['customer_id']);
        $this->assertGreaterThanOrEqual(2, $result['total']);
        $this->assertSame(50, $result['limit']);
    }

    public function testReturnsEmptyForUnknownCustomer(): void
    {
        $result = $this->tool->get(customerId: 99999999);

        $this->assertNull($result['customer_id']);
        $this->assertSame(0, $result['total']);
        $this->assertSame([], $result['items']);
    }

    public function testLimitClamp(): void
    {
        $customer = $this->createCustomer('mcp-customer-clamp@example.com');

        $result = $this->tool->get(customerId: $customer->getId(), limit: 999);

        $this->assertSame(200, $result['limit']);
    }
}
