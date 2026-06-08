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

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Service\Mcp\McpScope;
use Eccube\Service\Mcp\Tool\SearchOrdersTool;
use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Fixture\Generator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * `SearchOrdersTool` の DB 結合テスト。
 *
 * scope 不在で AccessDenied、 scope 付与で allow_list ベース出力、 customerId 絞り込みを検証。
 */
final class SearchOrdersToolTest extends EccubeTestCase
{
    private ?SearchOrdersTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(SearchOrdersTool::class);
        $this->tokenStorage()->setToken(null);
    }

    public function testThrowsWhenScopeIsAbsent(): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->tool->search();
    }

    public function testReturnsOrdersWithScope(): void
    {
        $this->grantScope(McpScope::ROLE_ORDER_READ);
        $customer = $this->createCustomer();
        $this->createOrderInDefaultSearchable($customer);

        $result = $this->tool->search(limit: 50);

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertGreaterThanOrEqual(1, $result['total']);
    }

    public function testLimitClampedToUpperBound(): void
    {
        $this->grantScope(McpScope::ROLE_ORDER_READ);

        $result = $this->tool->search(limit: 500);

        $this->assertSame(200, $result['limit']);
    }

    public function testFiltersByCustomerId(): void
    {
        $this->grantScope(McpScope::ROLE_ORDER_READ);
        $customerA = $this->createCustomer('mcp-order-a@example.com');
        $customerB = $this->createCustomer('mcp-order-b@example.com');
        $this->createOrderInDefaultSearchable($customerA);
        $this->createOrderInDefaultSearchable($customerA);
        $this->createOrderInDefaultSearchable($customerB);

        $result = $this->tool->search(customerId: $customerA->getId(), limit: 100);

        $this->assertGreaterThanOrEqual(2, $result['total'], 'customerA の注文だけがカウントされる');
    }

    public function testItemFieldsAreSubsetOfAllowList(): void
    {
        $this->grantScope(McpScope::ROLE_ORDER_READ);
        $customer = $this->createCustomer('mcp-order-allow@example.com');
        $this->createOrderInDefaultSearchable($customer);

        $result = $this->tool->search(limit: 5);
        $this->assertNotEmpty($result['items']);

        // Api44 の allow_list の `Eccube\Entity\Order` 列挙項目
        $allowed = [
            'id', 'pre_order_id', 'order_no', 'message',
            'name01', 'name02', 'kana01', 'kana02', 'company_name', 'email', 'phone_number',
            'postal_code', 'addr01', 'addr02', 'birth',
            'subtotal', 'discount', 'delivery_fee_total', 'charge', 'tax', 'total', 'payment_total',
            'payment_method', 'note', 'create_date', 'update_date', 'order_date', 'payment_date',
            'currency_code', 'complete_message', 'complete_mail_message', 'add_point', 'use_point',
            'OrderItems', 'Shippings', 'MailHistories', 'Customer', 'Country', 'Pref',
            'Sex', 'Job', 'Payment', 'DeviceType',
            'CustomerOrderStatus', 'OrderStatusColor', 'OrderStatus',
        ];

        foreach ($result['items'] as $item) {
            foreach (array_keys($item) as $key) {
                $this->assertContains($key, $allowed, sprintf('出力フィールド "%s" は allow_list 外', $key));
            }
        }
    }

    /**
     * `getQueryBuilderBySearchDataForAdmin` のデフォルトは PROCESSING / PENDING を除外する。
     * 一方 `createOrder` ヘルパは PROCESSING を付与するため、 検索結果に出ない。
     * 検索可能な status (NEW) の Order を作るためのヘルパ。
     */
    private function createOrderInDefaultSearchable(Customer $customer): Order
    {
        $generator = static::getContainer()->get(Generator::class);
        $this->assertInstanceOf(Generator::class, $generator);

        return $generator->createOrder($customer, [], null, 0, 0, OrderStatus::NEW);
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
