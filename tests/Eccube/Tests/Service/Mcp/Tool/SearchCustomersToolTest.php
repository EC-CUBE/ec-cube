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

use Eccube\Entity\Master\CustomerStatus;
use Eccube\Service\Mcp\McpScope;
use Eccube\Service\Mcp\Tool\SearchCustomersTool;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * `SearchCustomersTool` の DB 結合テスト。 scope / 検索 / status 絞り込み / allow_list を検証。
 */
final class SearchCustomersToolTest extends EccubeTestCase
{
    private ?SearchCustomersTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(SearchCustomersTool::class);
        $this->tokenStorage()->setToken(null);
    }

    public function testThrowsWhenScopeIsAbsent(): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->tool->search();
    }

    public function testReturnsCustomersWithScope(): void
    {
        $this->grantScope(McpScope::ROLE_CUSTOMER_READ);
        $this->createCustomer('mcp-customer-1@example.com');

        $result = $this->tool->search(limit: 50);

        $this->assertArrayHasKey('total', $result);
        $this->assertGreaterThanOrEqual(1, $result['total']);
    }

    public function testFiltersByActiveStatus(): void
    {
        $this->grantScope(McpScope::ROLE_CUSTOMER_READ);
        $this->createCustomer('mcp-customer-active@example.com');

        $result = $this->tool->search(statusIds: [CustomerStatus::ACTIVE], limit: 100);

        $this->assertGreaterThanOrEqual(1, $result['total'], 'ACTIVE 会員が 1 件以上');
    }

    public function testLimitClampedToUpperBound(): void
    {
        $this->grantScope(McpScope::ROLE_CUSTOMER_READ);

        $result = $this->tool->search(limit: 500);

        $this->assertSame(200, $result['limit']);
    }

    public function testItemFieldsAreSubsetOfAllowList(): void
    {
        $this->grantScope(McpScope::ROLE_CUSTOMER_READ);
        $this->createCustomer('mcp-customer-allow@example.com');

        $result = $this->tool->search(limit: 5);
        $this->assertNotEmpty($result['items']);

        $allowed = [
            'id', 'name01', 'name02', 'kana01', 'kana02', 'company_name',
            'postal_code', 'addr01', 'addr02', 'email', 'phone_number', 'birth',
            'first_buy_date', 'last_buy_date', 'buy_times', 'buy_total', 'note',
            'reset_expire', 'point', 'create_date', 'update_date',
            'CustomerFavoriteProducts', 'CustomerAddresses', 'Orders',
            'Status', 'Sex', 'Job', 'Country', 'Pref',
        ];

        foreach ($result['items'] as $item) {
            foreach (array_keys($item) as $key) {
                $this->assertContains($key, $allowed, sprintf('出力フィールド "%s" は Customer allow_list 外', $key));
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
