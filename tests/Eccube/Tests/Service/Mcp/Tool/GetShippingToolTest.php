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

use Eccube\Service\Mcp\Tool\GetShippingTool;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * `GetShippingTool` の DB 結合テスト。 Order に紐づく Shipping 一覧の返却と不在時の挙動を検証する。
 */
#[Group('mcp')]
final class GetShippingToolTest extends EccubeTestCase
{
    private ?GetShippingTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(GetShippingTool::class);
    }

    public function testReturnsShippingsForOrder(): void
    {
        $customer = $this->createCustomer('mcp-shipping@example.com');
        $order = $this->createOrder($customer);

        $result = $this->tool->get(orderId: $order->getId());

        $this->assertSame($order->getId(), $result['order_id']);
        $this->assertArrayHasKey('items', $result);
        $this->assertGreaterThanOrEqual(1, \count($result['items']), '通常 createOrder は最低 1 つの Shipping を持つ');
    }

    public function testReturnsEmptyForUnknownOrder(): void
    {
        $result = $this->tool->get(orderId: 99999999);

        $this->assertNull($result['order_id']);
        $this->assertSame([], $result['items']);
    }

    public function testItemFieldsAreSubsetOfShippingAllowList(): void
    {
        $customer = $this->createCustomer('mcp-shipping-allow@example.com');
        $order = $this->createOrder($customer);

        $result = $this->tool->get(orderId: $order->getId());
        $this->assertNotEmpty($result['items']);

        // Api44 の allow_list の `Eccube\Entity\Shipping`
        $allowed = [
            'id', 'name01', 'name02', 'kana01', 'kana02', 'company_name',
            'phone_number', 'postal_code', 'addr01', 'addr02',
            'shipping_delivery_name', 'time_id', 'shipping_delivery_time',
            'shipping_delivery_date', 'shipping_date', 'tracking_number',
            'note', 'sort_no', 'create_date', 'update_date', 'mail_send_date',
            'Order', 'OrderItems', 'Country', 'Pref', 'Delivery', 'Creator',
        ];

        foreach ($result['items'] as $item) {
            foreach (array_keys($item) as $key) {
                $this->assertContains($key, $allowed, sprintf('出力フィールド "%s" は Shipping allow_list 外', $key));
            }
        }
    }
}
